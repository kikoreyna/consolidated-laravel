<?php

namespace App\Http\Controllers;

use App\Destinatario;
use Illuminate\Http\Request;

class DestinatarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $destinatarios = Destinatario::orderBy('nombre')->get();
        return view('destinatarios.index', compact('destinatarios'));
    }

    public function create()
    {
        return view('destinatarios.create');
    }

    public function store(Request $request)
    {
        Destinatario::create($this->validated($request));
        return redirect()->route('destinatarios.index')->with('success', 'Destinatario creado correctamente.');
    }

    public function show(Destinatario $destinatario)
    {
        return view('destinatarios.show', compact('destinatario'));
    }

    public function edit(Destinatario $destinatario)
    {
        return view('destinatarios.edit', compact('destinatario'));
    }

    public function update(Request $request, Destinatario $destinatario)
    {
        $destinatario->update($this->validated($request));
        return redirect()->route('destinatarios.index')->with('success', 'Destinatario actualizado correctamente.');
    }

    public function destroy(Destinatario $destinatario)
    {
        $destinatario->update(['activo' => false]);
        return redirect()->route('destinatarios.index')->with('success', 'Destinatario desactivado correctamente.');
    }

    private function validated(Request $request)
    {
        return $request->validate([
            'nombre' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'correo_electronico' => 'nullable|email|max:255',
            'direccion' => 'nullable|string',
            'codigo_postal' => 'nullable|string|max:20',
            'referencias' => 'nullable|string',
            'ciudad' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'pais' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'activo' => 'required|boolean',
        ]);
    }
}
