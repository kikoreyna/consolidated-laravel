<?php

namespace App\Http\Controllers;

use App\Bodega;
use Illuminate\Http\Request;

class BodegaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $bodegas = Bodega::orderBy('id', 'desc')->get();

        return view('bodegas.index', compact('bodegas'));
    }

    public function create()
    {
        return view('bodegas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:bodegas,nombre',
            'descripcion' => 'nullable|string',
        ]);

        Bodega::create($request->all());

        return redirect()->route('bodegas.index')->with('success', 'Bodega creada correctamente.');
    }

    public function show(Bodega $bodega)
    {
        return view('bodegas.show', compact('bodega'));
    }

    public function edit(Bodega $bodega)
    {
        return view('bodegas.edit', compact('bodega'));
    }

    public function update(Request $request, Bodega $bodega)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:bodegas,nombre,' . $bodega->id,
            'descripcion' => 'nullable|string',
        ]);

        $bodega->update($request->all());

        return redirect()->route('bodegas.index')->with('success', 'Bodega actualizada correctamente.');
    }

    public function destroy(Bodega $bodega)
    {
        $bodega->delete();

        return redirect()->route('bodegas.index')->with('success', 'Bodega eliminada correctamente.');
    }
}
