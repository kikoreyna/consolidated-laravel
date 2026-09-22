<?php

namespace App\Http\Controllers;

use App\Consolidado;
use App\Entrada;
use App\Cliente;
use App\EntradaMovimiento;
use App\ConsolidadoMovimiento;
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

    public function importCsv(Request $request, Consolidado $consolidado)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeConsolidado($consolidado);

        if ($consolidado->cerrado) {
            abort(422, 'El consolidado está cerrado y no puede recibir nuevas guías.');
        }

        $request->validate(['archivo' => 'required|file|mimes:csv,txt|max:10240']);

        $handle = fopen($request->file('archivo')->getRealPath(), 'r');
        $headers = fgetcsv($handle);
        $headers = array_map(function ($header) {
            return strtolower(trim((string) $header));
        }, $headers ?: []);
        $required = ['entrada_numero'];

        if (count(array_diff($required, $headers)) > 0) {
            fclose($handle);
            throw ValidationException::withMessages(['archivo' => 'El CSV debe incluir la columna entrada_numero.']);
        }

        $rows = [];
        $line = 1;
        while (($values = fgetcsv($handle)) !== false) {
            $line++;
            if (count(array_filter($values, 'strlen')) === 0) {
                continue;
            }
            $row = array_combine($headers, array_pad($values, count($headers), null));
            $row['_line'] = $line;
            $rows[] = $row;
        }
        fclose($handle);

        if (!$rows) {
            throw ValidationException::withMessages(['archivo' => 'El CSV no contiene guías.']);
        }

        DB::transaction(function () use ($rows, $consolidado) {
            foreach ($rows as $row) {
                if (isset($row['cliente_id']) && $row['cliente_id'] !== '' && (int) $row['cliente_id'] !== (int) $consolidado->cliente_id) {
                    throw ValidationException::withMessages(['archivo' => 'La fila ' . $row['_line'] . ' pertenece a otro cliente.']);
                }
                if (!$row['entrada_numero'] || Entrada::where('numero', trim($row['entrada_numero']))->exists()) {
                    throw ValidationException::withMessages(['archivo' => 'Número de entrada inválido o duplicado en la fila ' . $row['_line'] . '.']);
                }
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
                $entrada = Entrada::create([
                    'numero' => trim($row['entrada_numero']),
                    'alias' => $row['alias'] ?? null,
                    'alias_cliente_numero' => filter_var($row['alias_cliente_numero'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'observaciones' => $row['observaciones'] ?? null,
                    'cliente_id' => $consolidado->cliente_id,
                    'consolidado_id' => $consolidado->id,
                    'bodega_id' => $row['bodega_id'] ?? null,
                    'remitente_id' => $row['remitente_id'] ?? null,
                    'destinatario_id' => $row['destinatario_id'] ?? null,
                    'transportadora_id' => $row['transportadora_id'] ?? null,
                    'oficina_id' => $row['oficina_id'] ?? null,
                    'modalidad_entrega' => $row['modalidad_entrega'] ?? null,
                    'vuelta' => $row['vuelta'] ?? null,
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

    public function store(Request $request)
    {
        $this->authorizeOperationalAccess();
        $request->validate([
            'numero' => 'required|string|max:255',
            'palets' => 'nullable|integer|min:0',
            'cliente_id' => 'required|integer|exists:clientes,id',
            'cliente_alias_numero' => 'nullable|boolean',
            'notificacion' => 'nullable|date',
            'cerrado' => 'nullable|boolean',
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
        $entradas = Entrada::with('cliente')->where('consolidado_id', $consolidado->id)
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

        if ($consolidado->cerrado && !$this->canEditClosedConsolidado()) {
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

        DB::transaction(function () use ($request, $consolidado) {
            $clienteCambio = (int) $consolidado->cliente_id !== (int) $request->input('cliente_id');
            $original = $consolidado->only(['numero', 'palets', 'cliente_id', 'cliente_alias_numero', 'notificacion']);
            $data = $request->only(['numero', 'palets', 'cliente_id', 'cliente_alias_numero', 'notificacion']);
            $data['notificacion'] = $data['notificacion'] ?? $consolidado->notificacion ?? now();

            if ($request->boolean('cerrado')) {
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

            if ($request->boolean('cerrado')) {
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
                        'datos' => [
                            'consolidado_id' => $consolidado->id,
                            'cliente_id' => $consolidado->cliente_id,
                        ],
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
