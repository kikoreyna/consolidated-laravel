<?php

namespace App\Http\Controllers;

use App\Remitente;
use Illuminate\Http\Request;

class RemitenteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $remitentes = Remitente::orderBy('nombre')->get();
        return view('remitentes.index', compact('remitentes'));
    }

    public function create()
    {
        return view('remitentes.create');
    }

    public function store(Request $request)
    {
        Remitente::create($this->validated($request));
        return redirect()->route('remitentes.index')->with('success', 'Remitente creado correctamente.');
    }

    public function show(Remitente $remitente)
    {
        return view('remitentes.show', compact('remitente'));
    }

    public function edit(Remitente $remitente)
    {
        return view('remitentes.edit', compact('remitente'));
    }

    public function update(Request $request, Remitente $remitente)
    {
        $remitente->update($this->validated($request));
        return redirect()->route('remitentes.index')->with('success', 'Remitente actualizado correctamente.');
    }

    public function destroy(Remitente $remitente)
    {
        $remitente->update(['activo' => false]);
        return redirect()->route('remitentes.index')->with('success', 'Remitente desactivado correctamente.');
    }

    private function validated(Request $request)
    {
        return $request->validate([
            'nombre' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'correo_electronico' => 'nullable|email|max:255',
            'direccion' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);
    }
}
