<?php

namespace App\Http\Controllers;

use App\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $clientes = Cliente::orderBy('id', 'desc')->get();

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'contacto' => 'required|string|max:255',
            'alias' => 'required|string|max:100',
            'telefono' => 'required|string|max:50',
            'correo_electronico' => 'required|email|max:255',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'pais' => 'required|string|max:100',
            'notas' => 'nullable|string',
        ]);

        Cliente::create($request->all());

        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente.');
    }

    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'contacto' => 'required|string|max:255',
            'alias' => 'required|string|max:100',
            'telefono' => 'required|string|max:50',
            'correo_electronico' => 'required|email|max:255',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'estado' => 'required|string|max:100',
            'pais' => 'required|string|max:100',
            'notas' => 'nullable|string',
        ]);

        $cliente->update($request->all());

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
    }
}
