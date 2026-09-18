<?php

namespace App\Http\Controllers;

use App\Consolidado;
use Illuminate\Http\Request;

class ConsolidadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $consolidados = Consolidado::orderBy('id', 'desc')->get();

        return view('consolidados.index', compact('consolidados'));
    }

    public function create()
    {
        return view('consolidados.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|string|max:255',
            'palets' => 'nullable|integer|min:0',
            'cliente_id' => 'required|integer',
            'cliente_alias_numero' => 'nullable|boolean',
            'notificacion' => 'nullable|date',
        ]);

        Consolidado::create($request->all());

        return redirect()->route('consolidados.index')->with('success', 'Consolidado creado correctamente.');
    }

    public function show(Consolidado $consolidado)
    {
        return view('consolidados.show', compact('consolidado'));
    }

    public function edit(Consolidado $consolidado)
    {
        return view('consolidados.edit', compact('consolidado'));
    }

    public function update(Request $request, Consolidado $consolidado)
    {
        $request->validate([
            'numero' => 'required|string|max:255',
            'palets' => 'nullable|integer|min:0',
            'cliente_id' => 'required|integer',
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
