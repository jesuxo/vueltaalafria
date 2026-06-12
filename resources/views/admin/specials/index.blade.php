{{-- resources/views/admin/specials/index.blade.php --}}
@extends('layouts.master')

@section('title')
    Gestionar Eventos Especiales
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-star me-2"></i> Eventos Especiales</h3>
                <p class="text-muted">Aquí puedes agregar categorías para fotos que no son de etapas (previas, premiaciones, etc.)</p>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.specials.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="name" class="form-control" placeholder="Nombre del evento (ej: Fotos Previas)" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="icon" class="form-control" placeholder="Icono (ej: fas fa-camera)" value="fas fa-camera">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date" class="form-control" placeholder="Fecha (opcional)">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Icono</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($specials as $special)
                            <tr>
                                <td><i class="{{ $special->icon }}"></i></td>
                                <td>{{ $special->name }}</td>
                                <td><span class="badge bg-info">Especial</span></td>
                                <td>{{ $special->date ? \Carbon\Carbon::parse($special->date)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger" onclick="deleteSpecial({{ $special->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
