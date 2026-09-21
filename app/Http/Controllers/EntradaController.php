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
use Illuminate\Http\Request;

class EntradaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $entradas = Entrada::with(['consolidado', 'cliente', 'conductor', 'vehiculo', 'reempacador', 'codigor', 'createdBy', 'updatedBy', 'remitente', 'destinatario', 'transportadora', 'oficina'])
            ->orderBy('id', 'desc')
            ->get();

        return view('entradas.index', compact('entradas'));
    }

    public function create()
    {
        $catalogos = $this->catalogos();

        return view('entradas.create', $catalogos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|string|max:255',
            'alias_cliente_numero' => 'required|boolean',
            'cliente_id' => 'required|integer|exists:clientes,id',
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
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        Entrada::create($request->all());

        return redirect()->route('entradas.index')->with('success', 'Entrada creada correctamente.');
    }

    public function show(Entrada $entrada)
    {
        $entrada->load(['consolidado', 'cliente', 'conductor', 'vehiculo', 'reempacador', 'codigor', 'createdBy', 'updatedBy', 'remitente', 'destinatario', 'transportadora', 'oficina']);

        return view('entradas.show', compact('entrada'));
    }

    public function edit(Entrada $entrada)
    {
        $catalogos = $this->catalogos();

        return view('entradas.edit', array_merge(['entrada' => $entrada], $catalogos));
    }

    public function update(Request $request, Entrada $entrada)
    {
        $request->validate([
            'numero' => 'required|string|max:255',
            'alias_cliente_numero' => 'required|boolean',
            'cliente_id' => 'required|integer|exists:clientes,id',
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
            'created_by' => 'required|integer',
            'updated_by' => 'required|integer',
        ]);

        $entrada->update($request->all());

        return redirect()->route('entradas.index')->with('success', 'Entrada actualizada correctamente.');
    }

    public function destroy(Entrada $entrada)
    {
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
        ];
    }
}
