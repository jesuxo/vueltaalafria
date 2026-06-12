{{-- resources/views/admin/specials/edit.blade.php --}}
@extends('layouts.master')

@section('title')
    Editar Momento Especial - Vuelta a la Fría
@endsection

@section('css')
    <style>
        .icon-preview {
            width: 60px;
            height: 60px;
            background: #f8f9fa;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .icon-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
        .icon-suggestion {
            width: 40px;
            height: 40px;
            background: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 20px;
        }
        .icon-suggestion:hover {
            background: #00ecfe;
            color: white;
            transform: scale(1.1);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-edit me-2"></i> Editar Momento Especial</h3>
                        <p class="text-muted mb-0">Modifica los datos del evento especial</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.specials.update', $special->id) }}" method="POST" id="specialForm">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label required-field">Nombre del evento</label>
                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                               required placeholder="Ej: Fotos Previas a la Carrera" value="{{ old('name', $special->name) }}">
                                        <small class="text-muted">Nombre que verán los usuarios en la galería</small>
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Fecha (opcional)</label>
                                        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror"
                                               value="{{ old('date', $special->date ? \Carbon\Carbon::parse($special->date)->format('Y-m-d') : '') }}">
                                        @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Icono (FontAwesome)</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="iconPreview"><i class="{{ $special->icon ?? 'fas fa-camera' }}"></i></span>
                                            <input type="text" name="icon" id="icon" class="form-control @error('icon') is-invalid @enderror"
                                                   placeholder="fas fa-camera" value="{{ old('icon', $special->icon ?? 'fas fa-camera') }}">
                                        </div>
                                        <small class="text-muted">Ej: fas fa-trophy, fas fa-calendar-day, fas fa-users, fas fa-camera-retro</small>
                                        @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Iconos sugeridos</label>
                                        <div class="icon-suggestions">
                                            <div class="icon-suggestion" onclick="setIcon('fas fa-camera')">
                                                <i class="fas fa-camera"></i>
                                            </div>
                                            <div class="icon-suggestion" onclick="setIcon('fas fa-calendar-day')">
                                                <i class="fas fa-calendar-day"></i>
                                            </div>
                                            <div class="icon-suggestion" onclick="setIcon('fas fa-trophy')">
                                                <i class="fas fa-trophy"></i>
                                            </div>
                                            <div class="icon-suggestion" onclick="setIcon('fas fa-utensils')">
                                                <i class="fas fa-utensils"></i>
                                            </div>
                                            <div class="icon-suggestion" onclick="setIcon('fas fa-camera-retro')">
                                                <i class="fas fa-camera-retro"></i>
                                            </div>
                                            <div class="icon-suggestion" onclick="setIcon('fas fa-users')">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div class="icon-suggestion" onclick="setIcon('fas fa-bicycle')">
                                                <i class="fas fa-bicycle"></i>
                                            </div>
                                            <div class="icon-suggestion" onclick="setIcon('fas fa-medal')">
                                                <i class="fas fa-medal"></i>
                                            </div>
                                            <div class="icon-suggestion" onclick="setIcon('fas fa-star')">
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <div class="icon-suggestion" onclick="setIcon('fas fa-heart')">
                                                <i class="fas fa-heart"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Descripción (opcional)</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                          rows="3" placeholder="Describe este momento especial...">{{ old('description', $special->description) }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', $special->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Activo (visible en la galería)
                                </label>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Guardar Cambios
                                </button>
                                <a href="{{ route('admin.specials.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function setIcon(iconClass) {
            document.getElementById('icon').value = iconClass;
            document.getElementById('iconPreview').innerHTML = `<i class="${iconClass}"></i>`;
        }

        document.getElementById('icon').addEventListener('input', function() {
            const icon = this.value || 'fas fa-camera';
            document.getElementById('iconPreview').innerHTML = `<i class="${icon}"></i>`;
        });
    </script>
@endsection
