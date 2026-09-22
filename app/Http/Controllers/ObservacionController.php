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
        $observaciones = $this->visibleObservaciones()->with(['user', 'entrada'])->orderBy('id', 'desc')->get();

        return view('observaciones.index', compact('observaciones'));
    }

    public function create()
    {
        $this->authorizeOperationalAccess();
        return view('observaciones.create', $this->catalogos());
    }

    public function store(Request $request)
    {
        $this->authorizeOperationalAccess();
        $request->validate([
            'contenido' => 'required|string',
            'user_id' => 'required|integer|exists:users,id',
            'entrada_id' => 'required|integer|exists:entradas,id',
        ]);

        $data = $request->only(['contenido', 'entrada_id']);
        $data['user_id'] = auth()->id();
        Observacion::create($data);

        return redirect()->route('observaciones.index')->with('success', 'Observación creada correctamente.');
    }

    public function show(Observacion $observacion)
    {
        $this->authorizeObservation($observacion);
        $observacion->load(['user', 'entrada']);

        return view('observaciones.show', compact('observacion'));
    }

    public function edit(Observacion $observacion)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeObservation($observacion);
        return view('observaciones.edit', array_merge(['observacion' => $observacion], $this->catalogos()));
    }

    public function update(Request $request, Observacion $observacion)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeObservation($observacion);
        $request->validate([
            'contenido' => 'required|string',
            'user_id' => 'required|integer|exists:users,id',
            'entrada_id' => 'required|integer|exists:entradas,id',
        ]);

        $observacion->update($request->only(['contenido', 'entrada_id']));

        return redirect()->route('observaciones.index')->with('success', 'Observación actualizada correctamente.');
    }

    public function destroy(Observacion $observacion)
    {
        $this->authorizeOperationalAccess();
        $this->authorizeObservation($observacion);
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

    private function visibleObservaciones()
    {
        if (auth()->user()->rol === 'cliente') {
            return Observacion::whereHas('entrada', function ($query) {
                $query->whereIn('cliente_id', auth()->user()->clientes()->wherePivot('activo', true)->pluck('clientes.id'));
            });
        }

        return Observacion::query();
    }

    private function authorizeObservation(Observacion $observacion)
    {
        if (!$this->visibleObservaciones()->whereKey($observacion->id)->exists()) {
            abort(403);
        }
    }

    private function authorizeOperationalAccess()
    {
        if (auth()->user()->rol === 'cliente') {
            abort(403);
        }
    }
}
