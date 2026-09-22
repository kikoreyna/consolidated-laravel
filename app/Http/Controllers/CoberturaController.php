<?php

namespace App\Http\Controllers;

use App\Bodega;
use App\Cliente;
use App\Cobertura;
use App\Oficina;
use App\Transportadora;
use Illuminate\Http\Request;

class CoberturaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:administrador,superadministrador');
    }

    public function index()
    {
        $coberturas = Cobertura::with(['cliente', 'oficina', 'transportadora', 'bodega'])->latest()->get();
        return view('coberturas.index', compact('coberturas'));
    }

    public function create()
    {
        return view('coberturas.create', $this->catalogos());
    }

    public function store(Request $request)
    {
        Cobertura::create($this->validated($request));
        return redirect()->route('coberturas.index')->with('success', 'Cobertura creada correctamente.');
    }

    public function edit(Cobertura $cobertura)
    {
        return view('coberturas.edit', array_merge(['cobertura' => $cobertura], $this->catalogos()));
    }

    public function update(Request $request, Cobertura $cobertura)
    {
        $cobertura->update($this->validated($request));
        return redirect()->route('coberturas.index')->with('success', 'Cobertura actualizada correctamente.');
    }

    public function destroy(Cobertura $cobertura)
    {
        $cobertura->update(['activa' => false]);
        return redirect()->route('coberturas.index')->with('success', 'Cobertura desactivada correctamente.');
    }

    private function catalogos()
    {
        return [
            'clientes' => Cliente::orderBy('nombre')->get(),
            'oficinas' => Oficina::with('transportadora')->orderBy('nombre')->get(),
            'transportadoras' => Transportadora::orderBy('nombre')->get(),
            'bodegas' => Bodega::orderBy('nombre')->get(),
        ];
    }

    private function validated(Request $request)
    {
        return $request->validate([
            'cliente_id' => 'required|integer|exists:clientes,id',
            'oficina_id' => 'nullable|integer|exists:oficinas,id',
            'transportadora_id' => 'nullable|integer|exists:transportadoras,id',
            'bodega_id' => 'nullable|integer|exists:bodegas,id',
            'modalidad_entrega' => 'required|in:domicilio,ocurre',
            'ciudad' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'activa' => 'required|boolean',
            'vigencia_desde' => 'nullable|date',
            'vigencia_hasta' => 'nullable|date|after_or_equal:vigencia_desde',
            'observaciones' => 'nullable|string',
        ]);
    }
}
