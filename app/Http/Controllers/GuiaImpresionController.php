<?php

namespace App\Http\Controllers;

use App\GuiaImpresion;
use Illuminate\Http\Request;
use App\Http\Requests\GuiaImpresionSaveRequest as SaveRequest;
use App\Ahex\GuiaImpresion\Application\Contenido\ContentContainer;

class GuiaImpresionController extends Controller
{
    public function index()
    {
        return view('guias_impresion.index')->with('guias', GuiaImpresion::all()->sortByDesc('id'));
    }

    public function show(GuiaImpresion $guia)
    {
        return redirect()->route('guias_impresion.index');
    }

    public function create()
    {
        return view('guias_impresion.create', [
            'mediciones' => config('system.medidas'),
            'tipografias' => config('system.tipografias'),
            'contenidos' => ContentContainer::all(),
            'guia' => new GuiaImpresion,
        ]);
    }

    public function store(SaveRequest $request)
    {
        $prepared = GuiaImpresion::prepare($request->validated());

        if( ! $guia = GuiaImpresion::create($prepared) )
            return back()->with('failure', 'Error al guardar guía de impresión');

        return redirect()->route('guias_impresion.index')->with('success', "Guía de impresión {$guia->nombre} guardada");
    }

    public function edit(GuiaImpresion $guia)
    {
        return view('guias_impresion.edit', [
            'mediciones' => config('system.medidas'),
            'tipografias' => config('system.tipografias'),
            'contenidos' => ContentContainer::all(),
            'guia' => $guia,
        ]);
    }

    public function update(GuiaImpresion $guia, SaveRequest $request)
    {
        $prepared = GuiaImpresion::prepare($request->validated());

        if( ! $guia->fill($prepared)->save() )
            return back()->with('failure', 'Error al actualizar guía de impresión');

        return back()->with('success', "Guía de impresión {$guia->nombre} actualizada");
    }

    public function destroy(GuiaImpresion $guia)
    {
        if( ! $guia->delete() )
            return back()->with('failure', 'Error al eliminar guía de impresión');

        return redirect()->route('guias_impresion.index')->with('success', "Guía de impresión {$guia->nombre} eliminada");
    }
}