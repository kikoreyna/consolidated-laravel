<?php

namespace App\Http\Controllers;

use App\Bodega;
use App\Cliente;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private $roles = [
        'bodega_usa',
        'bodega_mexico',
        'documentador',
        'supervisor',
        'cliente',
        'administrador',
        'superadministrador',
    ];

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:superadministrador');
    }

    public function index()
    {
        $usuarios = User::with(['bodegas', 'clientes'])->orderBy('name')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create', $this->catalogos());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);
        $bodegas = $data['bodegas'] ?? [];
        $clientes = $data['clientes'] ?? [];
        unset($data['bodegas'], $data['clientes']);

        $usuario = User::create($data);
        $usuario->bodegas()->sync($bodegas);
        $usuario->clientes()->sync($clientes);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function show(User $usuario)
    {
        $usuario->load(['bodegas', 'clientes']);
        return view('usuarios.show', compact('usuario'));
    }

    public function edit(User $usuario)
    {
        $usuario->load(['bodegas', 'clientes']);
        return view('usuarios.edit', array_merge(['usuario' => $usuario], $this->catalogos()));
    }

    public function update(Request $request, User $usuario)
    {
        $data = $this->validated($request, $usuario);
        $bodegas = $data['bodegas'] ?? [];
        $clientes = $data['clientes'] ?? [];
        unset($data['bodegas'], $data['clientes']);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $usuario->update($data);
        $usuario->bodegas()->sync($bodegas);
        $usuario->clientes()->sync($clientes);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->is(auth()->user())) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->bodegas()->detach();
        $usuario->clientes()->detach();
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }

    private function catalogos()
    {
        return [
            'bodegas' => Bodega::where('activa', true)->orderBy('nombre')->get(),
            'clientes' => Cliente::orderBy('nombre')->get(),
            'roles' => $this->roles,
        ];
    }

    private function validated(Request $request, User $usuario = null)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore(optional($usuario)->id)],
            'password' => $usuario ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed',
            'rol' => ['required', Rule::in($this->roles)],
            'activo' => 'required|boolean',
            'bodegas' => 'array',
            'bodegas.*' => 'integer|exists:bodegas,id',
            'clientes' => 'array',
            'clientes.*' => 'integer|exists:clientes,id',
        ]);
    }
}
