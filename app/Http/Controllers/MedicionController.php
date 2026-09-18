<?php

namespace App\Http\Controllers;

use App\Medicion;
use Illuminate\Http\Request;

class MedicionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $mediciones = Medicion::orderBy('id', 'desc')->get();

        return view('mediciones.index', compact('mediciones'));
    }

    public function create()
    {
        return view('mediciones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:mediciones,nombre',
        ]);

        Medicion::create($request->all());

        return redirect()->route('mediciones.index')->with('success', 'Medición creada correctamente.');
    }

    public function show(Medicion $medicion)
    {
        return view('mediciones.show', compact('medicion'));
    }

    public function edit(Medicion $medicion)
    {
        return view('mediciones.edit', compact('medicion'));
    }

    public function update(Request $request, Medicion $medicion)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:mediciones,nombre,' . $medicion->id,
        ]);

        $medicion->update($request->all());

        return redirect()->route('mediciones.index')->with('success', 'Medición actualizada correctamente.');
    }

    public function destroy(Medicion $medicion)
    {
        $medicion->delete();

        return redirect()->route('mediciones.index')->with('success', 'Medición eliminada correctamente.');
    }
}
