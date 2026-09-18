<?php

namespace App\Http\Controllers;

use App\Observacion;
use Illuminate\Http\Request;

class ObservacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $observaciones = Observacion::orderBy('id', 'desc')->get();

        return view('observaciones.index', compact('observaciones'));
    }

    public function create()
    {
        return view('observaciones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'contenido' => 'required|string',
            'user_id' => 'required|integer',
            'entrada_id' => 'required|integer',
        ]);

        Observacion::create($request->all());

        return redirect()->route('observaciones.index')->with('success', 'Observación creada correctamente.');
    }

    public function show(Observacion $observacion)
    {
        return view('observaciones.show', compact('observacion'));
    }

    public function edit(Observacion $observacion)
    {
        return view('observaciones.edit', compact('observacion'));
    }

    public function update(Request $request, Observacion $observacion)
    {
        $request->validate([
            'contenido' => 'required|string',
            'user_id' => 'required|integer',
            'entrada_id' => 'required|integer',
        ]);

        $observacion->update($request->all());

        return redirect()->route('observaciones.index')->with('success', 'Observación actualizada correctamente.');
    }

    public function destroy(Observacion $observacion)
    {
        $observacion->delete();

        return redirect()->route('observaciones.index')->with('success', 'Observación eliminada correctamente.');
    }
}
