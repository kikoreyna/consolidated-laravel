<?php

namespace App\Http\Controllers;

use App\Observacion;
use App\Entrada;
use App\User;
use Illuminate\Http\Request;

class ObservacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $observaciones = Observacion::with(['user', 'entrada'])->orderBy('id', 'desc')->get();

        return view('observaciones.index', compact('observaciones'));
    }

    public function create()
    {
        return view('observaciones.create', $this->catalogos());
    }

    public function store(Request $request)
    {
        $request->validate([
            'contenido' => 'required|string',
            'user_id' => 'required|integer|exists:users,id',
            'entrada_id' => 'required|integer|exists:entradas,id',
        ]);

        Observacion::create($request->all());

        return redirect()->route('observaciones.index')->with('success', 'Observación creada correctamente.');
    }

    public function show(Observacion $observacion)
    {
        $observacion->load(['user', 'entrada']);

        return view('observaciones.show', compact('observacion'));
    }

    public function edit(Observacion $observacion)
    {
        return view('observaciones.edit', array_merge(['observacion' => $observacion], $this->catalogos()));
    }

    public function update(Request $request, Observacion $observacion)
    {
        $request->validate([
            'contenido' => 'required|string',
            'user_id' => 'required|integer|exists:users,id',
            'entrada_id' => 'required|integer|exists:entradas,id',
        ]);

        $observacion->update($request->all());

        return redirect()->route('observaciones.index')->with('success', 'Observación actualizada correctamente.');
    }

    public function destroy(Observacion $observacion)
    {
        $observacion->delete();

        return redirect()->route('observaciones.index')->with('success', 'Observación eliminada correctamente.');
    }

    private function catalogos()
    {
        return [
            'usuarios' => User::orderBy('name')->get(),
            'entradas' => Entrada::orderBy('numero')->get(),
        ];
    }
}
