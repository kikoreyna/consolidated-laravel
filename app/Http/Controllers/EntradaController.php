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
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

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
            'observacion' => 'nullable|string',
            'incidente' => 'nullable|in:sin_incidente,dano_embalaje,empaque_danado,faltante,excedente,diferencia_peso,guia_ilegible,otro',
        ]);

        $entrada = $this->findOrCreateUsaEntry($data['numero']);

        if (!$entrada->bodega_id) {
            $bodegaUsa = $this->usaBodegaForUser();
            $entrada->update(['bodega_id' => $bodegaUsa->id]);
            $entrada->load('bodega');
            $this->logMovement($entrada, 'asignada_bodega_usa', 'Guía existente asignada a bodega USA por escaneo.');
        }

        $this->authorizeEntry($entrada);

        if (!$entrada->bodega || strtoupper((string) $entrada->bodega->codigo) !== 'USA') {
            abort(422, 'La guía no está asignada a una bodega USA.');
        }

        session()->flash('usa_datos_cliente', $this->clientMeasurements($entrada));

        if ($data['accion'] === 'solo_recibido') {
            $this->completeUsaControl($entrada, $data['accion'], $data);
            session(['usa_scan_action' => $data['accion']]);
            return redirect()->route('bodega-usa')->with('success', 'Guía recibida correctamente.');
        }

        session(['usa_pendiente' => [
            'entrada_id' => $entrada->id,
            'numero' => $entrada->numero,
            'accion' => $data['accion'],
            'incidente' => $data['incidente'] ?? 'sin_incidente',
        ]]);

        return redirect()->route('bodega-usa');
    }

    public function completeControlUsa(Request $request)
    {
        $data = $request->validate([
            'entrada_id' => 'required|integer|exists:entradas,id',
            'accion' => 'required|in:solo_pesar,pesar_medir',
            'peso_usa' => 'required|numeric|min:0',
            'largo_usa' => 'nullable|numeric|min:0',
            'ancho_usa' => 'nullable|numeric|min:0',
            'alto_usa' => 'nullable|numeric|min:0',
            'observacion' => 'nullable|string',
            'incidente' => 'nullable|in:sin_incidente,dano_embalaje,empaque_danado,faltante,excedente,diferencia_peso,guia_ilegible,otro',
        ]);

        $entrada = Entrada::with('bodega')->findOrFail($data['entrada_id']);
        $this->authorizeEntry($entrada);

        if ($data['accion'] === 'pesar_medir' && (empty($data['largo_usa']) || empty($data['ancho_usa']) || empty($data['alto_usa']))) {
            abort(422, 'Largo, ancho y alto son obligatorios para pesar y medir.');
        }

        $data['incidente'] = $data['incidente'] ?: session('usa_pendiente.incidente', 'sin_incidente');
        $this->completeUsaControl($entrada, $data['accion'], $data);
        session()->forget('usa_pendiente');
        session(['usa_scan_action' => $data['accion']]);

        return redirect()->route('bodega-usa')->with('success', 'Control USA registrado para la guía ' . $entrada->numero . '.');
    }

    public function controlMexico(Request $request)
    {
        if (!session('mexico_contexto.conductor_id') || !session('mexico_contexto.vehiculo_id')) {
            return redirect()->route('bodega-mexico')->with('error', 'Configura el conductor y vehículo antes de escanear guías.');
        }

        $data = $request->validate([
            'numero' => 'required|string|max:255',
            'accion' => 'required|in:solo_recibido,solo_pesar,pesar_medir',
            'observacion' => 'nullable|string',
            'incidente' => 'nullable|string|max:100',
        ]);

        $entrada = Entrada::with('bodega')->where('numero', $data['numero'])->first();
        $sinEntradaUsa = !$entrada || !$entrada->recibido_usa_at;
        $bodegaMexico = $this->mexicoBodegaForUser();

        if (!$entrada) {
            $entrada = Entrada::create([
                'numero' => $data['numero'],
                'alias_cliente_numero' => false,
                'cliente_id' => null,
                'consolidado_id' => null,
                'bodega_id' => $bodegaMexico->id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
            $this->logMovement($entrada, 'creada_sin_consolidar_mexico', 'Guía no existente registrada por escaneo en Bodega México.');
        } elseif ((int) $entrada->bodega_id !== (int) $bodegaMexico->id) {
            $entrada->update(['bodega_id' => $bodegaMexico->id, 'updated_by' => auth()->id()]);
            $entrada->load('bodega');
            $this->logMovement($entrada, 'asignada_bodega_mexico', 'Guía asignada a Bodega México por escaneo.');
        }

        $this->authorizeEntry($entrada);

        session(['mexico_pendiente' => [
            'entrada_id' => $entrada->id,
            'numero' => $entrada->numero,
            'accion' => $data['accion'],
            'observacion' => $data['observacion'] ?? '',
            'incidente' => $data['incidente'] ?? '',
            'sin_entrada_usa' => $sinEntradaUsa,
        ]]);

        if ($sinEntradaUsa) {
            $mensaje = 'La guía llegó a Bodega México sin entrada registrada en USA.';
            session()->flash('warning', $mensaje);
            $this->notifyMissingUsaEntry($entrada);
        }

        return redirect()->route('bodega-mexico');
    }

    public function configurarMexico(Request $request)
    {
        $data = $request->validate([
            'conductor_id' => 'required|integer|exists:conductores,id',
            'vehiculo_id' => 'required|integer|exists:vehiculos,id',
            'vuelta' => 'nullable|integer|min:0',
        ]);

        session(['mexico_contexto' => $data]);

        return redirect()->route('bodega-mexico')->with('success', 'Conductor, vehículo y vuelta configurados para los siguientes escaneos.');
    }

    public function completeControlMexico(Request $request)
    {
        $data = $request->validate([
            'entrada_id' => 'required|integer|exists:entradas,id',
            'accion' => 'required|in:solo_recibido,solo_pesar,pesar_medir',
            'peso_mexico' => 'required|numeric|min:0',
            'largo_mexico' => 'nullable|numeric|min:0',
            'ancho_mexico' => 'nullable|numeric|min:0',
            'alto_mexico' => 'nullable|numeric|min:0',
            'observacion' => 'nullable|string',
            'incidente' => 'nullable|string|max:100',
        ]);

        $entrada = Entrada::with('bodega')->findOrFail($data['entrada_id']);
        $this->authorizeEntry($entrada);

        $contexto = session('mexico_contexto');
        if (!$contexto || empty($contexto['conductor_id']) || empty($contexto['vehiculo_id'])) {
            throw ValidationException::withMessages([
                'conductor_id' => 'Configura el conductor y vehículo antes de escanear guías.',
            ]);
        }

        if ($data['accion'] === 'pesar_medir' && (empty($data['largo_mexico']) || empty($data['ancho_mexico']) || empty($data['alto_mexico']))) {
            abort(422, 'Largo, ancho y alto son obligatorios para pesar y medir.');
        }

        $update = [
            'recibido_mexico_at' => now(),
            'recibido_mexico_por' => auth()->id(),
            'conductor_id' => $contexto['conductor_id'],
            'vehiculo_id' => $contexto['vehiculo_id'],
            'vuelta' => $contexto['vuelta'] ?? null,
            'cruce_at' => now(),
            'control_mexico_tipo' => $data['accion'],
            'control_mexico_completado_at' => now(),
            'control_mexico_completado_por' => auth()->id(),
            'peso_mexico' => $data['peso_mexico'],
            'largo_mexico' => $data['largo_mexico'] ?? null,
            'ancho_mexico' => $data['ancho_mexico'] ?? null,
            'alto_mexico' => $data['alto_mexico'] ?? null,
            'volumen_mexico' => $data['accion'] === 'pesar_medir' ? $data['largo_mexico'] * $data['ancho_mexico'] * $data['alto_mexico'] : null,
        ];

        $entrada->update($update);
        $this->logMovement($entrada, 'recibido_mexico', $data['observacion'] ?? 'Entrada registrada en Bodega México.', [
            'incidente' => $data['incidente'] ?? '',
            'peso_mexico' => $data['peso_mexico'],
            'conductor_id' => $contexto['conductor_id'],
            'vehiculo_id' => $contexto['vehiculo_id'],
        ]);
        if (session('mexico_pendiente.sin_entrada_usa')) {
            session()->flash('warning', 'La guía fue registrada en México, pero llegó sin entrada registrada en USA.');
        }
        session()->forget('mexico_pendiente');

        return redirect()->route('bodega-mexico')->with('success', 'Entrada registrada en Bodega México para la guía ' . $entrada->numero . '.');
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
            'peso_cliente' => 'nullable|numeric|min:0',
            'largo_cliente' => 'nullable|numeric|min:0',
            'ancho_cliente' => 'nullable|numeric|min:0',
            'alto_cliente' => 'nullable|numeric|min:0',
            'volumen_cliente' => 'nullable|numeric|min:0',
            'recibido_usa_confirmar' => 'nullable|boolean',
            'recibido_mexico_confirmar' => 'nullable|boolean',
        ]);

        if (Entrada::where('numero', $request->input('numero'))->exists()) {
            throw ValidationException::withMessages([
                'numero' => 'Ya existe una guía con ese número.',
            ]);
        }

        $this->validateConsolidadoCliente($request);

        $data = $request->only([
            'numero', 'alias_cliente_numero', 'cliente_id', 'bodega_id', 'consolidado_id',
            'remitente_id', 'destinatario_id', 'transportadora_id', 'oficina_id',
            'modalidad_entrega', 'alias', 'observaciones', 'vuelta', 'recibido_at', 'conductor_id',
            'vehiculo_id', 'cruce_at', 'reempacador_id', 'codigor_id',
            'reempacado_at', 'peso_cliente', 'largo_cliente', 'ancho_cliente',
            'alto_cliente', 'volumen_cliente',
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

    public function editSalida(Entrada $entrada)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeEntry($entrada);

        if (!$entrada->destinatario_confirmado) {
            return redirect()->route('entradas.show', $entrada)
                ->with('error', 'Verifica primero el destinatario para capturar la salida.');
        }

        return view('entradas.salida-edit', [
            'entrada' => $entrada,
            'transportadoras' => Transportadora::orderBy('nombre')->get(),
            'oficinas' => Oficina::where('activa', true)->with('transportadora')->orderBy('nombre')->get(),
        ]);
    }

    public function updateSalida(Request $request, Entrada $entrada)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeEntry($entrada);

        if (!$entrada->destinatario_confirmado) {
            abort(422, 'Verifica primero el destinatario para capturar la salida.');
        }

        $data = $request->validate([
            'codigo_rastreo' => 'nullable|string|max:255',
            'codigo_confirmacion' => 'nullable|string|max:255',
            'transportadora_id' => 'nullable|integer|exists:transportadoras,id',
            'modalidad_entrega' => 'nullable|in:domicilio,ocurre',
            'oficina_id' => 'nullable|integer|exists:oficinas,id',
            'status_salida' => 'nullable|string|max:100',
            'incidente_salida' => 'nullable|string|max:255',
            'notas_salida' => 'nullable|string',
        ]);

        $data['modalidad_entrega'] = $data['modalidad_entrega'] ?? 'domicilio';

        if ($data['modalidad_entrega'] === 'ocurre') {
            if (empty($data['transportadora_id']) || empty($data['oficina_id'])) {
                throw ValidationException::withMessages([
                    'oficina_id' => 'Selecciona una transportadora y una oficina para la cobertura ocurre.',
                ]);
            }

            $oficinaValida = Oficina::whereKey($data['oficina_id'])
                ->where('transportadora_id', $data['transportadora_id'])
                ->where('activa', true)
                ->exists();

            if (!$oficinaValida) {
                throw ValidationException::withMessages([
                    'oficina_id' => 'La oficina seleccionada no pertenece a la transportadora elegida.',
                ]);
            }
        } else {
            $data['oficina_id'] = null;
        }

        $data['updated_by'] = auth()->id();
        $entrada->update($data);
        $this->logMovement($entrada, 'salida_actualizada', 'Datos de salida actualizados.');

        return redirect()->route('entradas.show', $entrada)->with('success', 'Datos de salida actualizados.');
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
            'peso_cliente' => 'nullable|numeric|min:0',
            'largo_cliente' => 'nullable|numeric|min:0',
            'ancho_cliente' => 'nullable|numeric|min:0',
            'alto_cliente' => 'nullable|numeric|min:0',
            'volumen_cliente' => 'nullable|numeric|min:0',
            'recibido_usa_confirmar' => 'nullable|boolean',
            'recibido_mexico_confirmar' => 'nullable|boolean',
        ]);

        if (Entrada::where('numero', $request->input('numero'))->where('id', '!=', $entrada->id)->exists()) {
            throw ValidationException::withMessages([
                'numero' => 'Ya existe otra guía con ese número.',
            ]);
        }

        $this->validateConsolidadoCliente($request);

        $data = $request->only([
            'numero', 'alias_cliente_numero', 'cliente_id', 'bodega_id', 'consolidado_id',
            'remitente_id', 'destinatario_id', 'transportadora_id', 'oficina_id',
            'modalidad_entrega', 'alias', 'observaciones', 'vuelta', 'recibido_at', 'conductor_id',
            'vehiculo_id', 'cruce_at', 'reempacador_id', 'codigor_id',
            'reempacado_at', 'peso_cliente', 'largo_cliente', 'ancho_cliente',
            'alto_cliente', 'volumen_cliente',
        ]);
        $destinatarioCambio = (int) $entrada->destinatario_id !== (int) ($data['destinatario_id'] ?? 0);
        $bodegaAnterior = $entrada->bodega_id;
        $bodegaNueva = $data['bodega_id'] ?? null;
        $bodegaCambio = (string) $bodegaAnterior !== (string) $bodegaNueva;
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
        if ($bodegaCambio) {
            $this->logMovement($entrada, 'traslado_bodega', 'Guía trasladada a otra bodega.', [
                'bodega_anterior_id' => $bodegaAnterior,
                'bodega_nueva_id' => $bodegaNueva,
            ]);
        }
        if ($cambios && !$nuevoRecibidoUsa && !$nuevoRecibidoMexico && !$nuevoReempacado && !$bodegaCambio) {
            $this->logMovement($entrada, 'actualizada', 'Datos de la guía actualizados');
        }

        return redirect()->route('entradas.show', $entrada)->with('success', 'Entrada actualizada correctamente.');
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

        if ($user->rol === 'bodega_usa') {
            return $query->whereIn('bodega_id', $user->bodegas()->pluck('bodegas.id'));
        }

        if ($user->rol === 'bodega_mexico') {
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

    private function findOrCreateUsaEntry($numero)
    {
        $entrada = Entrada::with('bodega')->where('numero', $numero)->first();

        if ($entrada) {
            return $entrada;
        }

        $bodegaUsa = $this->usaBodegaForUser();

        $entrada = Entrada::create([
            'numero' => $numero,
            'alias_cliente_numero' => false,
            'cliente_id' => null,
            'consolidado_id' => null,
            'bodega_id' => $bodegaUsa->id,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
        $entrada->load('bodega');
        $this->logMovement($entrada, 'creada_sin_consolidar', 'Guía no existente registrada por escaneo.');

        return $entrada;
    }

    private function usaBodegaForUser()
    {
        $bodegaUsa = Bodega::where('codigo', 'USA')
            ->whereIn('id', auth()->user()->bodegas()->pluck('bodegas.id'))
            ->first();

        if (!$bodegaUsa && in_array(auth()->user()->rol, ['supervisor', 'administrador', 'superadministrador'], true)) {
            $bodegaUsa = Bodega::firstOrCreate(
                ['codigo' => 'USA'],
                [
                    'nombre' => 'Bodega USA',
                    'descripcion' => 'Bodega de recepción y control en Estados Unidos.',
                    'pais' => 'USA',
                    'activa' => true,
                    'control_usa' => 'solo_recibido',
                ]
            );
        }

        if (!$bodegaUsa) {
            abort(422, 'No tienes una bodega USA asignada para registrar esta guía.');
        }

        return $bodegaUsa;
    }

    private function mexicoBodegaForUser()
    {
        $bodegaMexico = Bodega::whereIn('codigo', ['MEX', 'MEXICO'])
            ->whereIn('id', auth()->user()->bodegas()->pluck('bodegas.id'))
            ->first();

        if (!$bodegaMexico && in_array(auth()->user()->rol, ['supervisor', 'administrador', 'superadministrador'], true)) {
            $bodegaMexico = Bodega::where('codigo', 'MEXICO')->first();
            if (!$bodegaMexico) {
                $bodegaMexico = Bodega::create([
                    'nombre' => 'Bodega México',
                    'descripcion' => 'Recepción y control en México.',
                    'codigo' => 'MEXICO',
                    'pais' => 'México',
                    'activa' => true,
                ]);
            }
        }

        if (!$bodegaMexico) {
            abort(422, 'No tienes una bodega México asignada para registrar esta guía.');
        }

        return $bodegaMexico;
    }

    private function notifyMissingUsaEntry(Entrada $entrada)
    {
        $destino = env('MAIL_INTERNAL_TO', config('mail.from.address'));

        if (!$destino) {
            return;
        }

        try {
            Mail::raw(
                'La guía ' . $entrada->numero . ' llegó a Bodega México sin entrada registrada en Bodega USA. Usuario: ' . auth()->user()->name . '.',
                function ($message) use ($destino, $entrada) {
                    $message->to($destino)->subject('Alerta: guía sin entrada USA - ' . $entrada->numero);
                }
            );
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function completeUsaControl(Entrada $entrada, $accion, array $data = [])
    {
        $update = [
            'recibido_usa_at' => now(),
            'recibido_usa_por' => auth()->id(),
            'control_usa_tipo' => $accion,
            'control_usa_completado_at' => now(),
            'control_usa_completado_por' => auth()->id(),
        ];

        foreach (['peso_usa', 'largo_usa', 'ancho_usa', 'alto_usa'] as $campo) {
            if (array_key_exists($campo, $data)) {
                $update[$campo] = $data[$campo];
            }
        }

        if ($accion === 'pesar_medir') {
            $update['volumen_usa'] = $data['largo_usa'] * $data['ancho_usa'] * $data['alto_usa'];
        }

        $entrada->update($update);
        $incidentText = $data['incidente'] ?? 'sin_incidente';
        $this->logMovement($entrada, $accion, $data['observacion'] ?? 'Control USA completado', [
            'incidente' => $incidentText,
            'peso_lb' => $data['peso_usa'] ?? null,
            'largo_in' => $data['largo_usa'] ?? null,
            'ancho_in' => $data['ancho_usa'] ?? null,
            'alto_in' => $data['alto_usa'] ?? null,
            'volumen_in3' => $entrada->volumen_usa,
        ]);
    }

    private function clientMeasurements(Entrada $entrada)
    {
        return [
            'numero' => $entrada->numero,
            'peso' => $entrada->peso_cliente,
            'largo' => $entrada->largo_cliente,
            'ancho' => $entrada->ancho_cliente,
            'alto' => $entrada->alto_cliente,
            'volumen' => $entrada->volumen_cliente,
        ];
    }

    private function logMovement(Entrada $entrada, $tipo, $observacion, array $datos = [])
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
            ] + $datos,
        ]);
    }
}
