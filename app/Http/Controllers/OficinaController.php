<?php

namespace App\Http\Controllers;

use App\Oficina;
use App\Transportadora;
use Illuminate\Http\Request;

class OficinaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $oficinas = Oficina::with('transportadora')->orderBy('nombre')->get();
        return view('oficinas.index', compact('oficinas'));
    }

    public function create()
    {
        $transportadoras = Transportadora::orderBy('nombre')->get();
        return view('oficinas.create', compact('transportadoras'));
    }

    public function store(Request $request)
    {
        Oficina::create($this->validated($request));
        return redirect()->route('oficinas.index')->with('success', 'Oficina creada correctamente.');
    }

    public function show(Oficina $oficina)
    {
        $oficina->load('transportadora');
        return view('oficinas.show', compact('oficina'));
    }

    public function edit(Oficina $oficina)
    {
        $transportadoras = Transportadora::orderBy('nombre')->get();
        return view('oficinas.edit', compact('oficina', 'transportadoras'));
    }

    public function update(Request $request, Oficina $oficina)
    {
        $oficina->update($this->validated($request));
        return redirect()->route('oficinas.index')->with('success', 'Oficina actualizada correctamente.');
    }

    public function destroy(Oficina $oficina)
    {
        $oficina->update(['activa' => false]);
        return redirect()->route('oficinas.index')->with('success', 'Oficina desactivada correctamente.');
    }

    private function validated(Request $request)
    {
        return $request->validate([
            'transportadora_id' => 'required|integer|exists:transportadoras,id',
            'nombre' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string',
            'activa' => 'required|boolean',
        ]);
    }
}
