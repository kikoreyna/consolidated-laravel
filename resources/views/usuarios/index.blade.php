@extends('layouts.app')

@section('content')
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h2>Usuarios</h2>
		<a href="{{ route('usuarios.create') }}" class="btn btn-primary">Nuevo usuario</a>
	</div>

	@if(session('success'))
		<div class="alert alert-success">{{ session('success') }}</div>
	@endif

	@if(session('error'))
		<div class="alert alert-danger">{{ session('error') }}</div>
	@endif

	<div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Nombre</th>
							<th>Correo</th>
							<th>Rol</th>
							<th>Estado</th>
							<th>Acciones</th>
						</tr>
					</thead>
					<tbody>
						@forelse($usuarios as $usuario)
							<tr>
								<td>{{ $usuario->name }}</td>
								<td>{{ $usuario->email }}</td>
								<td>{{ ucfirst(str_replace('_', ' ', $usuario->rol)) }}</td>
								<td>{{ $usuario->activo ? 'Activo' : 'Inactivo' }}</td>
								<td>
									<a href="{{ route('usuarios.show', $usuario) }}" class="btn btn-sm btn-info">Ver</a>
									<a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-sm btn-warning">Editar</a>

									@if(!$usuario->is(auth()->user()))
										<form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="d-inline">
											@csrf
											@method('DELETE')
											<button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar usuario?')">Eliminar</button>
										</form>
									@endif
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="5" class="text-center">No hay usuarios registrados.</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection
