<?php

namespace App\Http\Controllers;

use App\Entrada;
use Illuminate\Http\Request;

class EntradaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $entradas = Entrada::orderBy('id', 'desc')->get();

        return view('entradas.index', compact('entradas'));
    }

    public function create()
    {
        return view('entradas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|string|max:255',
            'alias_cliente_numero' => 'required|boolean',
            'cliente_id' => 'required|integer',
            'consolidado_id' => 'nullable|integer',
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
        return view('entradas.show', compact('entrada'));
    }

    public function edit(Entrada $entrada)
    {
        return view('entradas.edit', compact('entrada'));
    }

    public function update(Request $request, Entrada $entrada)
    {
        $request->validate([
            'numero' => 'required|string|max:255',
            'alias_cliente_numero' => 'required|boolean',
            'cliente_id' => 'required|integer',
            'consolidado_id' => 'nullable|integer',
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
}
