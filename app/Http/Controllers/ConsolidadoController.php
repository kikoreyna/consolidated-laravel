<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Consolidado;
use App\ConsolidadoMovimiento;
use App\Entrada;
use App\EntradaMovimiento;
use App\Remitente;
use App\Destinatario;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConsolidadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $consolidados = $this->visibleConsolidados()->with('cliente')->orderBy('id', 'desc')->get();

        return view('consolidados.index', compact('consolidados'));
    }

    public function create()
    {
        $this->authorizeOperationalAccess();
        $clientes = Cliente::orderBy('nombre')->get();

        return view('consolidados.create', compact('clientes'));
    }

    public function importForm(Consolidado $consolidado)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeConsolidado($consolidado);

        if ($consolidado->cerrado) {
            abort(422, 'El consolidado está cerrado y no puede recibir nuevas guías.');
        }

        return view('consolidados.import', compact('consolidado'));
    }

    public function entradaForm(Consolidado $consolidado)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeConsolidado($consolidado);

        if ($consolidado->cerrado) {
            abort(422, 'El consolidado está cerrado y no puede recibir nuevas guías.');
        }

        return view('consolidados.entrada', [
            'consolidado' => $consolidado,
            'remitentes' => Remitente::where('activo', true)->orderBy('nombre')->get(),
            'destinatarios' => Destinatario::where('activo', true)->orderBy('nombre')->get(),
        ]);
    }

    public function importCsv(Request $request, Consolidado $consolidado)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeConsolidado($consolidado);

        if ($consolidado->cerrado) {
            abort(422, 'El consolidado está cerrado y no puede recibir nuevas guías.');
        }

        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:10240',
            'alias_cliente_numero' => 'nullable|boolean',
        ]);

        $usarAliasCliente = $request->boolean('alias_cliente_numero');
        $consolidado->loadMissing('cliente');
        if ($usarAliasCliente && !$consolidado->cliente->alias) {
            throw ValidationException::withMessages([
                'alias_cliente_numero' => 'El cliente no tiene un alias configurado.',
            ]);
        }
        $handle = fopen($request->file('archivo')->getRealPath(), 'r');
        $headers = array_map(function ($header) {
            return $this->normalizeCsvHeader($header);
        }, fgetcsv($handle) ?: []);
        $required = ['entrada_numero'];

        if (count(array_diff($required, $headers)) > 0) {
            fclose($handle);
            throw ValidationException::withMessages(['archivo' => 'El CSV debe incluir la columna entrada_numero.']);
        }

        $rows = [];
        $line = 1;
        while (($values = fgetcsv($handle)) !== false) {
            $line++;
            $row = array_combine($headers, array_pad($values, count($headers), null));
            if ($this->csvValue($row, 'entrada_numero') === null) {
                continue;
            }
            $row['_line'] = $line;
            $rows[] = $row;
        }
        fclose($handle);

        if (!$rows) {
            throw ValidationException::withMessages(['archivo' => 'El CSV no contiene guías.']);
        }

        DB::transaction(function () use ($rows, $consolidado, $usarAliasCliente) {
            $numeros = [];
            foreach ($rows as $row) {
                $cliente = $this->csvValue($row, 'cliente_id');
                if ($cliente !== null && (int) $cliente !== (int) $consolidado->cliente_id) {
                    throw ValidationException::withMessages(['archivo' => 'La fila ' . $row['_line'] . ' pertenece a otro cliente.']);
                }
                $numero = $this->entradaNumeroConAlias(
                    $this->csvValue($row, 'entrada_numero'),
                    $consolidado,
                    $usarAliasCliente || filter_var($this->csvValue($row, 'alias_cliente_numero') ?: false, FILTER_VALIDATE_BOOLEAN)
                );
                if ($numero === null || isset($numeros[$numero]) || Entrada::where('numero', $numero)->exists()) {
                    throw ValidationException::withMessages(['archivo' => 'Número de entrada inválido o duplicado en la fila ' . $row['_line'] . '.']);
                }
                $numeros[$numero] = true;
            }

            ConsolidadoMovimiento::create([
                'consolidado_id' => $consolidado->id,
                'tipo' => 'importado_csv',
                'usuario_id' => auth()->id(),
                'ocurrido_at' => now(),
                'observacion' => 'Guías agregadas desde CSV al consolidado.',
                'datos' => ['filas' => count($rows)],
            ]);

            foreach ($rows as $row) {
                $remitenteId = $this->csvValue($row, 'remitente_id');
                if (!$remitenteId && $this->csvValue($row, 'remitente_nombre')) {
                    $remitenteId = Remitente::create([
                        'nombre' => $this->csvValue($row, 'remitente_nombre'),
                        'telefono' => $this->csvValue($row, 'remitente_telefono'),
                        'direccion' => $this->csvValue($row, 'remitente_direccion'),
                        'codigo_postal' => $this->csvValue($row, 'remitente_codigo_postal'),
                        'ciudad' => $this->csvValue($row, 'remitente_ciudad'),
                        'estado' => $this->csvValue($row, 'remitente_estado'),
                        'pais' => $this->csvValue($row, 'remitente_pais'),
                        'activo' => true,
                    ])->id;
                }
                $destinatarioId = $this->csvValue($row, 'destinatario_id');
                if (!$destinatarioId && $this->csvValue($row, 'destinatario_nombre')) {
                    $destinatarioId = Destinatario::create([
                        'nombre' => $this->csvValue($row, 'destinatario_nombre'),
                        'telefono' => $this->csvValue($row, 'destinatario_telefono'),
                        'direccion' => $this->csvValue($row, 'destinatario_direccion'),
                        'codigo_postal' => $this->csvValue($row, 'destinatario_codigo_postal'),
                        'referencias' => $this->csvValue($row, 'destinatario_referencias'),
                        'ciudad' => $this->csvValue($row, 'destinatario_ciudad'),
                        'estado' => $this->csvValue($row, 'destinatario_estado'),
                        'pais' => $this->csvValue($row, 'destinatario_pais'),
                        'activo' => true,
                    ])->id;
                }
                $entrada = Entrada::create([
                    'numero' => $this->entradaNumeroConAlias($this->csvValue($row, 'entrada_numero'), $consolidado, $usarAliasCliente || filter_var($this->csvValue($row, 'alias_cliente_numero') ?: false, FILTER_VALIDATE_BOOLEAN)),
                    'alias' => $this->csvValue($row, 'alias'),
                    'alias_cliente_numero' => $usarAliasCliente || filter_var($this->csvValue($row, 'alias_cliente_numero') ?: false, FILTER_VALIDATE_BOOLEAN),
                    'observaciones' => $this->csvValue($row, 'observaciones'),
                    'peso_cliente' => $this->csvNumber($row, 'peso_cliente'),
                    'largo_cliente' => $this->csvNumber($row, 'largo_cliente'),
                    'ancho_cliente' => $this->csvNumber($row, 'ancho_cliente'),
                    'alto_cliente' => $this->csvNumber($row, 'alto_cliente'),
                    'volumen_cliente' => $this->csvNumber($row, 'volumen_cliente'),
                    'cliente_id' => $consolidado->cliente_id,
                    'consolidado_id' => $consolidado->id,
                    'bodega_id' => $this->csvValue($row, 'bodega_id'),
                    'remitente_id' => $remitenteId,
                    'destinatario_id' => $destinatarioId,
                    'transportadora_id' => $this->csvValue($row, 'transportadora_id'),
                    'oficina_id' => $this->csvValue($row, 'oficina_id'),
                    'modalidad_entrega' => $this->csvValue($row, 'modalidad_entrega'),
                    'vuelta' => $this->csvNumber($row, 'vuelta'),
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                EntradaMovimiento::create([
                    'entrada_id' => $entrada->id,
                    'tipo' => 'importada_csv',
                    'usuario_id' => auth()->id(),
                    'ocurrido_at' => now(),
                    'bodega_id' => $entrada->bodega_id,
                    'observacion' => 'Guía importada desde CSV.',
                    'datos' => ['consolidado_id' => $consolidado->id],
                ]);
            }
        });

        return redirect()->route('consolidados.show', $consolidado)->with('success', 'Guías importadas correctamente al consolidado.');
    }

    public function addEntrada(Request $request, Consolidado $consolidado)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeConsolidado($consolidado);

        if ($consolidado->cerrado) {
            abort(422, 'El consolidado está cerrado y no puede recibir nuevas guías.');
        }

        $data = $request->validate([
            'numero' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'peso_cliente' => 'nullable|numeric|min:0',
            'largo_cliente' => 'nullable|numeric|min:0',
            'ancho_cliente' => 'nullable|numeric|min:0',
            'alto_cliente' => 'nullable|numeric|min:0',
            'volumen_cliente' => 'nullable|numeric|min:0',
            'remitente_id' => 'nullable|integer|exists:remitentes,id',
            'destinatario_id' => 'nullable|integer|exists:destinatarios,id',
            'remitente_nombre' => 'nullable|string|max:255',
            'remitente_telefono' => 'nullable|string|max:255',
            'remitente_direccion' => 'nullable|string|max:255',
            'destinatario_nombre' => 'nullable|string|max:255',
            'destinatario_telefono' => 'nullable|string|max:255',
            'destinatario_direccion' => 'nullable|string|max:255',
            'destinatario_codigo_postal' => 'nullable|string|max:50',
            'destinatario_referencias' => 'nullable|string|max:255',
            'destinatario_ciudad' => 'nullable|string|max:255',
            'destinatario_estado' => 'nullable|string|max:255',
            'destinatario_pais' => 'nullable|string|max:255',
            'transportadora_id' => 'nullable|integer|exists:transportadoras,id',
            'oficina_id' => 'nullable|integer|exists:oficinas,id',
            'modalidad_entrega' => 'nullable|in:domicilio,ocurre',
            'vuelta' => 'nullable|integer|min:0',
        ]);

        if (!$data['remitente_id'] && !empty($data['remitente_nombre'])) {
            $data['remitente_id'] = Remitente::create([
                'nombre' => $data['remitente_nombre'],
                'telefono' => $data['remitente_telefono'] ?? null,
                'direccion' => $data['remitente_direccion'] ?? null,
                'activo' => true,
            ])->id;
        }
        if (!$data['destinatario_id'] && !empty($data['destinatario_nombre'])) {
            $data['destinatario_id'] = Destinatario::create([
                'nombre' => $data['destinatario_nombre'],
                'telefono' => $data['destinatario_telefono'] ?? null,
                'direccion' => $data['destinatario_direccion'] ?? null,
                'codigo_postal' => $data['destinatario_codigo_postal'] ?? null,
                'referencias' => $data['destinatario_referencias'] ?? null,
                'ciudad' => $data['destinatario_ciudad'] ?? null,
                'estado' => $data['destinatario_estado'] ?? null,
                'pais' => $data['destinatario_pais'] ?? null,
                'activo' => true,
            ])->id;
        }

        unset($data['remitente_nombre'], $data['remitente_telefono'], $data['remitente_direccion'], $data['destinatario_nombre'], $data['destinatario_telefono'], $data['destinatario_direccion'], $data['destinatario_codigo_postal'], $data['destinatario_referencias'], $data['destinatario_ciudad'], $data['destinatario_estado'], $data['destinatario_pais']);

        $data['numero'] = $this->entradaNumeroConAlias($data['numero'], $consolidado, (bool) $consolidado->cliente_alias_numero);
        if (Entrada::where('numero', $data['numero'])->exists()) {
            throw ValidationException::withMessages([
                'numero' => 'Ya existe una guía con el número ' . $data['numero'] . '.',
            ]);
        }

        $entrada = Entrada::create(array_merge($data, [
            'alias_cliente_numero' => (bool) $consolidado->cliente_alias_numero,
            'cliente_id' => $consolidado->cliente_id,
            'consolidado_id' => $consolidado->id,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]));

        EntradaMovimiento::create([
            'entrada_id' => $entrada->id,
            'tipo' => 'creada_manual',
            'usuario_id' => auth()->id(),
            'ocurrido_at' => now(),
            'bodega_id' => $entrada->bodega_id,
            'observacion' => 'Guía agregada manualmente al consolidado.',
            'datos' => ['consolidado_id' => $consolidado->id],
        ]);

        $this->logMovement($consolidado, 'guia_agregada_manual', 'Guía agregada manualmente al consolidado.', [
            'entrada_id' => $entrada->id,
            'entrada_numero' => $entrada->numero,
        ]);

        return redirect()->route('consolidados.show', $consolidado)->with('success', 'Guía agregada correctamente al consolidado.');
    }

    public function store(Request $request)
    {
        $this->authorizeOperationalAccess();
        $request->validate([
            'numero' => 'required|string|max:255',
            'palets' => 'nullable|integer|min:0',
            'cliente_id' => 'required|integer|exists:clientes,id',
            'cliente_alias_numero' => 'nullable|boolean',
            'notificacion' => 'nullable|date',
        ]);

        $data = $request->only(['numero', 'palets', 'cliente_id', 'cliente_alias_numero', 'notificacion']);
        $data['notificacion'] = $data['notificacion'] ?? now();
        $consolidado = Consolidado::create($data);
        $this->logMovement($consolidado, 'creado', 'Consolidado creado');

        return redirect()->route('consolidados.index')->with('success', 'Consolidado creado correctamente.');
    }

    public function show(Consolidado $consolidado)
    {
        $this->authorizeConsolidado($consolidado);
        $consolidado->load(['cliente', 'cerradoPor', 'movimientos.usuario']);
        $entradas = Entrada::with(['cliente', 'remitente', 'destinatario'])->where('consolidado_id', $consolidado->id)
            ->when(auth()->user()->rol === 'cliente', function ($query) {
                return $query->whereIn('cliente_id', auth()->user()->clientes()->wherePivot('activo', true)->pluck('clientes.id'));
            })
            ->orderBy('id', 'desc')
            ->get();

        return view('consolidados.show', compact('consolidado', 'entradas'));
    }

    public function edit(Consolidado $consolidado)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeConsolidado($consolidado);
        $clientes = Cliente::orderBy('nombre')->get();

        return view('consolidados.edit', compact('consolidado', 'clientes'));
    }

    public function update(Request $request, Consolidado $consolidado)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeConsolidado($consolidado);

        if ($consolidado->cerrado && !in_array(auth()->user()->rol, ['supervisor', 'administrador', 'superadministrador'], true)) {
            abort(422, 'El consolidado está cerrado y no puede modificarse.');
        }

        $request->validate([
            'numero' => 'required|string|max:255',
            'palets' => 'nullable|integer|min:0',
            'cliente_id' => 'required|integer|exists:clientes,id',
            'cliente_alias_numero' => 'nullable|boolean',
            'notificacion' => 'nullable|date',
            'cerrado' => 'nullable|boolean',
        ]);

        if ($request->boolean('cliente_alias_numero')) {
            $cliente = Cliente::findOrFail($request->input('cliente_id'));
            if (!$cliente->alias) {
                throw ValidationException::withMessages([
                    'cliente_alias_numero' => 'El cliente seleccionado no tiene un alias configurado.',
                ]);
            }
        }

        DB::transaction(function () use ($request, $consolidado) {
            $clienteCambio = (int) $consolidado->cliente_id !== (int) $request->input('cliente_id');
            $original = $consolidado->only(['numero', 'palets', 'cliente_id', 'cliente_alias_numero', 'notificacion']);
            $data = $request->only(['numero', 'palets', 'cliente_id', 'cliente_alias_numero', 'notificacion']);
            $data['notificacion'] = $data['notificacion'] ?? $consolidado->notificacion ?? now();

            if ($request->boolean('cerrado') && !$consolidado->cerrado) {
                $data['cerrado'] = true;
                $data['cerrado_at'] = now();
                $data['cerrado_por'] = auth()->id();
            }

            $consolidado->update($data);
            $cambios = [];
            foreach ($original as $campo => $valor) {
                if ((string) $valor !== (string) $consolidado->{$campo}) {
                    $cambios[$campo] = ['anterior' => $valor, 'nuevo' => $consolidado->{$campo}];
                }
            }

            if ($cambios) {
                $this->logMovement($consolidado, $clienteCambio ? 'cliente_cambiado' : 'actualizado', 'Datos del consolidado actualizados', $cambios);
            }
            if ($request->boolean('cerrado') && !$consolidado->cerrado) {
                $this->logMovement($consolidado, 'cerrado', 'Consolidado validado y cerrado');
            }

            if ($clienteCambio) {
                $consolidado->entradas()->update(['cliente_id' => $consolidado->cliente_id]);
                foreach ($consolidado->entradas as $entrada) {
                    EntradaMovimiento::create([
                        'entrada_id' => $entrada->id,
                        'tipo' => 'cliente_consolidado_cambiado',
                        'usuario_id' => auth()->id(),
                        'ocurrido_at' => now(),
                        'bodega_id' => $entrada->bodega_id,
                        'conductor_id' => $entrada->conductor_id,
                        'vehiculo_id' => $entrada->vehiculo_id,
                        'vuelta' => $entrada->vuelta,
                        'reempacador_id' => $entrada->reempacador_id,
                        'codigor_id' => $entrada->codigor_id,
                        'observacion' => 'La guía heredó el nuevo cliente del consolidado.',
                        'datos' => ['consolidado_id' => $consolidado->id, 'cliente_id' => $consolidado->cliente_id],
                    ]);
                }
            }
        });

        return redirect()->route('consolidados.index')->with('success', 'Consolidado actualizado correctamente.');
    }

    public function destroy(Consolidado $consolidado)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeConsolidado($consolidado);
        $consolidado->delete();

        return redirect()->route('consolidados.index')->with('success', 'Consolidado eliminado correctamente.');
    }

    private function visibleConsolidados()
    {
        if (auth()->user()->rol === 'cliente') {
            return Consolidado::whereIn('cliente_id', auth()->user()->clientes()->wherePivot('activo', true)->pluck('clientes.id'));
        }

        return Consolidado::query();
    }

    private function authorizeConsolidado(Consolidado $consolidado)
    {
        if (!$this->visibleConsolidados()->whereKey($consolidado->id)->exists()) {
            abort(403);
        }
    }

    private function authorizeOperationalAccess()
    {
        if (auth()->user()->rol === 'cliente') {
            abort(403);
        }
    }

    private function csvValue(array $row, $key)
    {
        if (!array_key_exists($key, $row) || trim((string) $row[$key]) === '') {
            return null;
        }

        return trim($row[$key]);
    }

    private function csvNumber(array $row, $key)
    {
        $value = $this->csvValue($row, $key);

        if ($value === null) {
            return null;
        }

        $value = str_replace(',', '.', $value);

        return preg_match('/-?\d+(?:\.\d+)?/', $value, $matches) ? $matches[0] : null;
    }

    private function entradaNumeroConAlias($numero, Consolidado $consolidado, $usarAlias)
    {
        if (!$usarAlias || !$consolidado->cliente || !$consolidado->cliente->alias) {
            return $numero;
        }

        $alias = trim($consolidado->cliente->alias);

        return stripos($numero, $alias) === 0 ? $numero : $alias . $numero;
    }

    private function normalizeCsvHeader($header)
    {
        $header = trim((string) $header);
        $header = strtr($header, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
        ]);
        $header = strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '_', $header));
        $header = trim($header, '_');

        return [
            'numero_de_entrada' => 'entrada_numero',
            'entrada_numero' => 'entrada_numero',
            'peso' => 'peso_cliente',
            'ancho' => 'ancho_cliente',
            'altura' => 'alto_cliente',
            'profundidad' => 'largo_cliente',
            'r_nombre' => 'remitente_nombre',
            'r_telefono' => 'remitente_telefono',
            'r_direccion' => 'remitente_direccion',
            'r_codigo_postal' => 'remitente_codigo_postal',
            'r_ciudad' => 'remitente_ciudad',
            'r_estado' => 'remitente_estado',
            'r_pais' => 'remitente_pais',
            'd_nombre' => 'destinatario_nombre',
            'd_telefono' => 'destinatario_telefono',
            'd_direccion' => 'destinatario_direccion',
            'd_codigo_postal' => 'destinatario_codigo_postal',
            'd_referencias' => 'destinatario_referencias',
            'd_ciudad' => 'destinatario_ciudad',
            'd_estado' => 'destinatario_estado',
            'd_pais' => 'destinatario_pais',
        ][$header] ?? $header;
    }

    private function logMovement(Consolidado $consolidado, $tipo, $observacion, array $datos = [])
    {
        ConsolidadoMovimiento::create([
            'consolidado_id' => $consolidado->id,
            'tipo' => $tipo,
            'usuario_id' => auth()->id(),
            'ocurrido_at' => now(),
            'observacion' => $observacion,
            'datos' => $datos,
        ]);
    }
}
