<?php

namespace App\Http\Controllers;

use App\Transportadora;
use Illuminate\Http\Request;

class TransportadoraController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $transportadoras = Transportadora::orderBy('id', 'desc')->get();

        return view('transportadoras.index', compact('transportadoras'));
    }

    public function create()
    {
        return view('transportadoras.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'web' => 'nullable|url|max:255',
            'telefono' => 'nullable|string|max:50',
            'notas' => 'nullable|string',
        ]);

        Transportadora::create($request->all());

        return redirect()->route('transportadoras.index')->with('success', 'Transportadora creada correctamente.');
    }

    public function show(Transportadora $transportadora)
    {
        return view('transportadoras.show', compact('transportadora'));
    }

    public function edit(Transportadora $transportadora)
    {
        return view('transportadoras.edit', compact('transportadora'));
    }

    public function update(Request $request, Transportadora $transportadora)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'web' => 'nullable|url|max:255',
            'telefono' => 'nullable|string|max:50',
            'notas' => 'nullable|string',
        ]);

        $transportadora->update($request->all());

        return redirect()->route('transportadoras.index')->with('success', 'Transportadora actualizada correctamente.');
    }

    public function destroy(Transportadora $transportadora)
    {
        $transportadora->delete();

        return redirect()->route('transportadoras.index')->with('success', 'Transportadora eliminada correctamente.');
    }
}
