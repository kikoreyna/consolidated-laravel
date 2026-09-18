<?php

namespace App\Http\Controllers;

use App\Reempacador;
use Illuminate\Http\Request;

class ReempacadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $reempacadores = Reempacador::orderBy('id', 'desc')->get();

        return view('reempacadores.index', compact('reempacadores'));
    }

    public function create()
    {
        return view('reempacadores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'clave' => 'required|string|max:255',
        ]);

        Reempacador::create($request->all());

        return redirect()->route('reempacadores.index')->with('success', 'Reempacador creado correctamente.');
    }

    public function show(Reempacador $reempacador)
    {
        return view('reempacadores.show', compact('reempacador'));
    }

    public function edit(Reempacador $reempacador)
    {
        return view('reempacadores.edit', compact('reempacador'));
    }

    public function update(Request $request, Reempacador $reempacador)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'clave' => 'required|string|max:255',
        ]);

        $reempacador->update($request->all());

        return redirect()->route('reempacadores.index')->with('success', 'Reempacador actualizado correctamente.');
    }

    public function destroy(Reempacador $reempacador)
    {
        $reempacador->delete();

        return redirect()->route('reempacadores.index')->with('success', 'Reempacador eliminado correctamente.');
    }
}
