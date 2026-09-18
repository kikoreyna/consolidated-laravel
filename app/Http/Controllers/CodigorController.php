<?php

namespace App\Http\Controllers;

use App\Codigor;
use Illuminate\Http\Request;

class CodigorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $codigosr = Codigor::orderBy('id', 'desc')->get();

        return view('codigosr.index', compact('codigosr'));
    }

    public function create()
    {
        return view('codigosr.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        Codigor::create($request->all());

        return redirect()->route('codigosr.index')->with('success', 'Código creado correctamente.');
    }

    public function show(Codigor $codigor)
    {
        return view('codigosr.show', compact('codigor'));
    }

    public function edit(Codigor $codigor)
    {
        return view('codigosr.edit', compact('codigor'));
    }

    public function update(Request $request, Codigor $codigor)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $codigor->update($request->all());

        return redirect()->route('codigosr.index')->with('success', 'Código actualizado correctamente.');
    }

    public function destroy(Codigor $codigor)
    {
        $codigor->delete();

        return redirect()->route('codigosr.index')->with('success', 'Código eliminado correctamente.');
    }
}
