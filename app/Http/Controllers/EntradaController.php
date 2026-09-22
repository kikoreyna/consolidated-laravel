<?php

namespace App\Http\Controllers;

use App\Entrada;
use App\Consolidado;
use App\Cliente;
use App\Conductor;
use App\Vehiculo;
use App\Reempacador;
use App\Codigor;
use App\User;
use App\Remitente;
use App\Destinatario;
use App\Transportadora;
use App\Oficina;
use App\Bodega;
use App\EntradaMovimiento;
use Illuminate\Http\Request;

class EntradaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $entradas = $this->visibleEntries()->with(['consolidado', 'cliente', 'conductor', 'vehiculo', 'reempacador', 'codigor', 'createdBy', 'updatedBy', 'remitente', 'destinatario', 'transportadora', 'oficina'])
            ->orderBy('id', 'desc')
            ->get();

        return view('entradas.index', compact('entradas'));
    }

    public function controlUsa(Request $request)
    {
        $data = $request->validate([
            'numero' => 'required|string|max:255',
            'accion' => 'required|in:solo_recibido,solo_pesar,pesar_medir',
            'peso_usa' => 'nullable|numeric|min:0',
            'largo_usa' => 'nullable|numeric|min:0',
            'ancho_usa' => 'nullable|numeric|min:0',
            'alto_usa' => 'nullable|numeric|min:0',
            'observacion' => 'nullable|string',
        ]);

        $entrada = Entrada::with('bodega')->where('numero', $data['numero'])->firstOrFail();
        $this->authorizeEntry($entrada);

        if (!$entrada->bodega || strtoupper((string) $entrada->bodega->codigo) !== 'USA') {
            abort(422, 'La guía no está asignada a una bodega USA.');
        }

        if ($data['accion'] === 'solo_pesar' && empty($data['peso_usa'])) {
            abort(422, 'El peso es obligatorio para esta operación.');
        }

        if ($data['accion'] === 'pesar_medir' && (empty($data['peso_usa']) || empty($data['largo_usa']) || empty($data['ancho_usa']) || empty($data['alto_usa']))) {
            abort(422, 'Peso, largo, ancho y alto son obligatorios para pesar y medir.');
        }

        $update = [
            'recibido_usa_at' => now(),
            'recibido_usa_por' => auth()->id(),
            'control_usa_tipo' => $data['accion'],
            'control_usa_completado_at' => now(),
            'control_usa_completado_por' => auth()->id(),
        ];

        foreach (['peso_usa', 'largo_usa', 'ancho_usa', 'alto_usa'] as $campo) {
            if (array_key_exists($campo, $data) && $data[$campo] !== null) {
                $update[$campo] = $data[$campo];
            }
        }

        if ($data['accion'] === 'pesar_medir') {
            $update['volumen_usa'] = $data['largo_usa'] * $data['ancho_usa'] * $data['alto_usa'];
        }

        $entrada->update($update);
        $this->logMovement($entrada, $data['accion'], $data['observacion'] ?? 'Control USA completado');

        return redirect()->route('entradas.index')->with('success', 'Control USA registrado para la guía ' . $entrada->numero . '.');
    }

    public function create()
    {
        $this->authorizeOperationalAccess();

        $catalogos = $this->catalogos();

        return view('entradas.create', $catalogos);
    }

    public function store(Request $request)
    {
        $this->authorizeOperationalAccess();

        $request->validate([
            'numero' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'alias_cliente_numero' => 'required|boolean',
            'cliente_id' => 'required|integer|exists:clientes,id',
            'bodega_id' => 'nullable|integer|exists:bodegas,id',
            'consolidado_id' => 'nullable|integer|exists:consolidados,id',
            'remitente_id' => 'nullable|integer|exists:remitentes,id',
            'destinatario_id' => 'nullable|integer|exists:destinatarios,id',
            'transportadora_id' => 'nullable|required_if:modalidad_entrega,ocurre|integer|exists:transportadoras,id',
            'oficina_id' => 'nullable|required_if:modalidad_entrega,ocurre|integer|exists:oficinas,id',
            'modalidad_entrega' => 'nullable|in:domicilio,ocurre',
            'vuelta' => 'nullable|integer',
            'recibido_at' => 'nullable|date',
            'conductor_id' => 'nullable|integer',
            'vehiculo_id' => 'nullable|integer',
            'cruce_at' => 'nullable|date',
            'reempacador_id' => 'nullable|integer',
            'codigor_id' => 'nullable|integer',
            'reempacado_at' => 'nullable|date',
            'recibido_usa_confirmar' => 'nullable|boolean',
            'recibido_mexico_confirmar' => 'nullable|boolean',
        ]);

        $this->validateConsolidadoCliente($request);

        $data = $request->only([
            'numero', 'alias_cliente_numero', 'cliente_id', 'bodega_id', 'consolidado_id',
            'remitente_id', 'destinatario_id', 'transportadora_id', 'oficina_id',
            'modalidad_entrega', 'alias', 'observaciones', 'vuelta', 'recibido_at', 'conductor_id',
            'vehiculo_id', 'cruce_at', 'reempacador_id', 'codigor_id',
            'reempacado_at',
        ]);
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        $entrada = Entrada::create($data);
        $this->logMovement($entrada, 'creada', 'Guía creada');

        return redirect()->route('entradas.index')->with('success', 'Entrada creada correctamente.');
    }

    public function show(Entrada $entrada)
    {
        $this->authorizeEntry($entrada);
        $entrada->load(['consolidado', 'cliente', 'conductor', 'vehiculo', 'reempacador', 'codigor', 'createdBy', 'updatedBy', 'remitente', 'destinatario', 'transportadora', 'oficina', 'destinatarioConfirmadoPor', 'recibidoUsaPor', 'recibidoMexicoPor', 'reempacadoPor', 'movimientos.usuario', 'movimientos.bodega', 'movimientos.conductor', 'movimientos.vehiculo', 'movimientos.reempacador', 'movimientos.codigor']);

        return view('entradas.show', compact('entrada'));
    }

    public function edit(Entrada $entrada)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeEntry($entrada);
        $catalogos = $this->catalogos();

        return view('entradas.edit', array_merge(['entrada' => $entrada], $catalogos));
    }

    public function update(Request $request, Entrada $entrada)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeEntry($entrada);
        $request->validate([
            'numero' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'alias_cliente_numero' => 'required|boolean',
            'cliente_id' => 'required|integer|exists:clientes,id',
            'bodega_id' => 'nullable|integer|exists:bodegas,id',
            'consolidado_id' => 'nullable|integer|exists:consolidados,id',
            'remitente_id' => 'nullable|integer|exists:remitentes,id',
            'destinatario_id' => 'nullable|integer|exists:destinatarios,id',
            'transportadora_id' => 'nullable|required_if:modalidad_entrega,ocurre|integer|exists:transportadoras,id',
            'oficina_id' => 'nullable|required_if:modalidad_entrega,ocurre|integer|exists:oficinas,id',
            'modalidad_entrega' => 'nullable|in:domicilio,ocurre',
            'vuelta' => 'nullable|integer',
            'recibido_at' => 'nullable|date',
            'conductor_id' => 'nullable|integer',
            'vehiculo_id' => 'nullable|integer',
            'cruce_at' => 'nullable|date',
            'reempacador_id' => 'nullable|integer',
            'codigor_id' => 'nullable|integer',
            'reempacado_at' => 'nullable|date',
            'recibido_usa_confirmar' => 'nullable|boolean',
            'recibido_mexico_confirmar' => 'nullable|boolean',
        ]);

        $this->validateConsolidadoCliente($request);

        $data = $request->only([
            'numero', 'alias_cliente_numero', 'cliente_id', 'bodega_id', 'consolidado_id',
            'remitente_id', 'destinatario_id', 'transportadora_id', 'oficina_id',
            'modalidad_entrega', 'alias', 'observaciones', 'vuelta', 'recibido_at', 'conductor_id',
            'vehiculo_id', 'cruce_at', 'reempacador_id', 'codigor_id',
            'reempacado_at',
        ]);
        $destinatarioCambio = (int) $entrada->destinatario_id !== (int) ($data['destinatario_id'] ?? 0);
        $confirmado = $request->boolean('destinatario_confirmado');

        $data['updated_by'] = auth()->id();
        $data['recibido_usa_at'] = $request->boolean('recibido_usa_confirmar') ? now() : $entrada->recibido_usa_at;
        $data['recibido_usa_por'] = $request->boolean('recibido_usa_confirmar') ? auth()->id() : $entrada->recibido_usa_por;
        $data['recibido_mexico_at'] = $request->boolean('recibido_mexico_confirmar') ? now() : $entrada->recibido_mexico_at;
        $data['recibido_mexico_por'] = $request->boolean('recibido_mexico_confirmar') ? auth()->id() : $entrada->recibido_mexico_por;
        $data['reempacado_por'] = !empty($data['reempacado_at']) ? auth()->id() : $entrada->reempacado_por;
        $nuevoRecibidoUsa = $request->boolean('recibido_usa_confirmar') && empty($entrada->recibido_usa_at);
        $nuevoRecibidoMexico = $request->boolean('recibido_mexico_confirmar') && empty($entrada->recibido_mexico_at);
        $nuevoReempacado = !empty($data['reempacado_at']) && empty($entrada->reempacado_at);
        if ($confirmado && !empty($data['destinatario_id'])) {
            $data['destinatario_confirmado'] = true;
            $data['destinatario_confirmado_at'] = now();
            $data['destinatario_confirmado_por'] = auth()->id();
        } else {
            $data['destinatario_confirmado'] = false;
            $data['destinatario_confirmado_at'] = null;
            $data['destinatario_confirmado_por'] = null;
        }

        if ($destinatarioCambio) {
            $data['destinatario_confirmado'] = false;
            $data['destinatario_confirmado_at'] = null;
            $data['destinatario_confirmado_por'] = null;
        }

        $entrada->fill($data);
        $cambios = $entrada->getDirty();
        $entrada->save();

        if ($nuevoRecibidoUsa) {
            $this->logMovement($entrada, 'recibido_usa', 'Guía recibida en bodega USA');
        }
        if ($nuevoRecibidoMexico) {
            $this->logMovement($entrada, 'recibido_mexico', 'Guía recibida en bodega México');
        }
        if ($nuevoReempacado) {
            $this->logMovement($entrada, 'reempacado', 'Guía reempacada');
        }
        if ($cambios && !$nuevoRecibidoUsa && !$nuevoRecibidoMexico && !$nuevoReempacado) {
            $this->logMovement($entrada, 'actualizada', 'Datos de la guía actualizados');
        }

        return redirect()->route('entradas.index')->with('success', 'Entrada actualizada correctamente.');
    }

    public function destroy(Entrada $entrada)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeEntry($entrada);
        $entrada->delete();

        return redirect()->route('entradas.index')->with('success', 'Entrada eliminada correctamente.');
    }

    private function catalogos()
    {
        return [
            'clientes' => Cliente::orderBy('nombre')->get(),
            'consolidados' => Consolidado::orderBy('numero')->get(),
            'conductores' => Conductor::orderBy('nombre')->get(),
            'vehiculos' => Vehiculo::orderBy('alias')->get(),
            'reempacadores' => Reempacador::orderBy('nombre')->get(),
            'codigosr' => Codigor::orderBy('nombre')->get(),
            'usuarios' => User::orderBy('name')->get(),
            'remitentes' => Remitente::where('activo', true)->orderBy('nombre')->get(),
            'destinatarios' => Destinatario::where('activo', true)->orderBy('nombre')->get(),
            'transportadoras' => Transportadora::orderBy('nombre')->get(),
            'oficinas' => Oficina::where('activa', true)->with('transportadora')->orderBy('nombre')->get(),
            'bodegas' => Bodega::where('activa', true)->orderBy('nombre')->get(),
        ];
    }

    private function visibleEntries()
    {
        $user = auth()->user();
        $query = Entrada::query();

        if (in_array($user->rol, ['administrador', 'superadministrador'], true)) {
            return $query;
        }

        if (in_array($user->rol, ['documentador', 'supervisor'], true)) {
            return $query->whereIn('bodega_id', $user->bodegas()->pluck('bodegas.id'));
        }

        return $query->whereIn(
            'cliente_id',
            $user->clientes()->wherePivot('activo', true)->pluck('clientes.id')
        );
    }

    private function authorizeEntry(Entrada $entrada)
    {
        if (!$this->visibleEntries()->whereKey($entrada->id)->exists()) {
            abort(403);
        }
    }

    private function authorizeOperationalAccess()
    {
        if (auth()->user()->rol === 'cliente') {
            abort(403);
        }
    }

    private function validateConsolidadoCliente(Request $request)
    {
        if (!$request->filled('consolidado_id')) {
            return;
        }

        $consolidado = Consolidado::findOrFail($request->input('consolidado_id'));

        if ((int) $consolidado->cliente_id !== (int) $request->input('cliente_id')) {
            abort(422, 'El cliente de la guía debe coincidir con el cliente del consolidado.');
        }
    }

    private function validateLockedClient(Request $request, Entrada $entrada)
    {
        $consolidadoActual = $entrada->consolidado_id;
        $consolidadoSolicitado = $request->input('consolidado_id');

        if ($consolidadoActual && (string) $consolidadoActual === (string) $consolidadoSolicitado &&
            (int) $entrada->cliente_id !== (int) $request->input('cliente_id')) {
            abort(422, 'El cliente de una guía consolidada no se puede modificar. Retira primero la guía del consolidado.');
        }
    }

    private function logMovement(Entrada $entrada, $tipo, $observacion)
    {
        EntradaMovimiento::create([
            'entrada_id' => $entrada->id,
            'tipo' => $tipo,
            'usuario_id' => auth()->id(),
            'ocurrido_at' => now(),
            'bodega_id' => $entrada->bodega_id,
            'conductor_id' => $entrada->conductor_id,
            'vehiculo_id' => $entrada->vehiculo_id,
            'vuelta' => $entrada->vuelta,
            'reempacador_id' => $entrada->reempacador_id,
            'codigor_id' => $entrada->codigor_id,
            'observacion' => $observacion,
            'datos' => [
                'numero' => $entrada->numero,
                'modalidad_entrega' => $entrada->modalidad_entrega,
            ],
        ]);
    }
}
