<?php

namespace App\Http\Controllers;

use App\Conductor;
use Illuminate\Http\Request;

class ConductorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $conductores = Conductor::orderBy('id', 'desc')->get();

        return view('conductores.index', compact('conductores'));
    }

    public function create()
    {
        return view('conductores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        Conductor::create($request->all());

        return redirect()->route('conductores.index')->with('success', 'Conductor creado correctamente.');
    }

    public function show(Conductor $conductor)
    {
        return view('conductores.show', compact('conductor'));
    }

    public function edit(Conductor $conductor)
    {
        return view('conductores.edit', compact('conductor'));
    }

    public function update(Request $request, Conductor $conductor)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $conductor->update($request->all());

        return redirect()->route('conductores.index')->with('success', 'Conductor actualizado correctamente.');
    }

    public function destroy(Conductor $conductor)
    {
        $conductor->delete();

        return redirect()->route('conductores.index')->with('success', 'Conductor eliminado correctamente.');
    }
}
