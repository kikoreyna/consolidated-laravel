<?php

namespace App\Http\Controllers;

use App\Consolidado;
use App\Entrada;
use App\Cliente;
use Illuminate\Http\Request;

class ConsolidadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $consolidados = Consolidado::with('cliente')->orderBy('id', 'desc')->get();

        return view('consolidados.index', compact('consolidados'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();

        return view('consolidados.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|string|max:255',
            'palets' => 'nullable|integer|min:0',
            'cliente_id' => 'required|integer|exists:clientes,id',
            'cliente_alias_numero' => 'nullable|boolean',
            'notificacion' => 'nullable|date',
        ]);

        Consolidado::create($request->all());

        return redirect()->route('consolidados.index')->with('success', 'Consolidado creado correctamente.');
    }

    public function show(Consolidado $consolidado)
    {
        $consolidado->load('cliente');
        $entradas = Entrada::with('cliente')->where('consolidado_id', $consolidado->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('consolidados.show', compact('consolidado', 'entradas'));
    }

    public function edit(Consolidado $consolidado)
    {
        $clientes = Cliente::orderBy('nombre')->get();

        return view('consolidados.edit', compact('consolidado', 'clientes'));
    }

    public function update(Request $request, Consolidado $consolidado)
    {
        $request->validate([
            'numero' => 'required|string|max:255',
            'palets' => 'nullable|integer|min:0',
            'cliente_id' => 'required|integer|exists:clientes,id',
            'cliente_alias_numero' => 'nullable|boolean',
            'notificacion' => 'nullable|date',
        ]);

        $consolidado->update($request->all());

        return redirect()->route('consolidados.index')->with('success', 'Consolidado actualizado correctamente.');
    }

    public function destroy(Consolidado $consolidado)
    {
        $consolidado->delete();

        return redirect()->route('consolidados.index')->with('success', 'Consolidado eliminado correctamente.');
    }
}
