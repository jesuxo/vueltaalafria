@extends('layouts.master')
@section('title')
    Actualización de producto
@endsection
@section('css')
    <style>
        .image-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }
        .image-card {
            position: relative;
            width: 150px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #e9ecef;
            background: #fff;
            transition: all 0.3s;
        }
        .image-card.principal {
            border-color: #198754;
            box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.25);
        }
        .image-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .image-badge {
            position: absolute;
            bottom: 5px;
            left: 5px;
            background: rgba(0,0,0,0.7);
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .image-actions {
            position: absolute;
            top: 5px;
            right: 5px;
            display: flex;
            gap: 5px;
        }
        .image-actions button {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }
        .loading-overlay .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        .dropzone-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .dropzone-area:hover, .dropzone-area.dragover {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }
        .progress-bar-container {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 10px;
            display: none;
        }
        .progress-bar-fill {
            height: 100%;
            background: #0d6efd;
            width: 0%;
            transition: width 0.3s;
        }
        select option {
            font-family: monospace;
        }
    </style>
@endsection
@section('content')
    <x-breadcrumb title="Modificación de producto" pagetitle="Productos" />

    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

    <form id="editproduct-form" method="post">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="col-xl-9 col-lg-8">
                <!-- Información básica del producto -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">Información</h5>
                                <p class="text-muted mb-0">Ingrese/Modifique los datos del producto.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div>
                            <div class="d-flex align-items-start">
                                <div class="flex-grow-1">
                                    <label class="form-label">Instancia de inventario <span class="text-danger">*</span></label>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="/instancias" class="float-end text-decoration-underline" target="_blank">+ Nueva Instancia</a>
                                </div>
                            </div>
                            <select class="form-select" id="codinst" name="codinst" required>
                                <option value="">Seleccione una instancia</option>
                                @foreach($instanciasProcesadas as $instancia)
                                    <option value="{{ $instancia->codinst }}"
                                            style="padding-left: {{ ($instancia->nivel - 1) * 20 }}px"
                                        {{ ($instancia->codinst == $producto->codinst) ? 'selected' : '' }}>
                                        {!! $instancia->label !!}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Por favor, seleccione una instancia del inventario</div>
                        </div>
                    </div>
                </div>

                <!-- Datos principales -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Código</label>
                                    <input type="text" class="form-control" id="codprod" name="codprod" disabled
                                           value="{{ $producto->codprod }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Referencia</label>
                                    <input type="text" class="form-control" id="refere" name="refere"
                                           value="{{ $producto->refere }}" placeholder="Ej: Código de barra">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nombre del producto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="descrip" name="descrip"
                                   value="{{ $producto->descrip }}" required>
                            <div class="invalid-feedback">Ingrese el nombre del producto</div>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" name="descrip2"
                                   value="{{ $producto->descrip2 }}" placeholder="Descripción 2">
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" name="descrip3"
                                   value="{{ $producto->descrip3 }}" placeholder="Descripción 3">
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Marca</label>
                                    <input type="text" class="form-control" name="marca"
                                           value="{{ $producto->marca }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Unidad de medida</label>
                                    <input type="text" class="form-control" name="unidad"
                                           value="{{ $producto->unidad }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            @if(Auth::user() && auth()->user()->type == 'admin')
                                <div class="col-lg-6">
                                    <div class="form-check form-switch mb-3">
                                        <input type="checkbox" class="form-check-input" id="esexento" name="esexento"
                                               {{ $producto->esexento ? 'checked' : '' }} value="1">
                                        <label class="form-check-label">¿Producto Exento?</label>
                                    </div>
                                </div>
                            @endif
                            <div class="col-lg-6">
                                <div class="form-check form-switch mb-3">
                                    <input type="checkbox" class="form-check-input" id="exdecimal" name="exdecimal"
                                           {{ $producto->exdecimal ? 'checked' : '' }} value="1">
                                    <label class="form-check-label">¿Usar decimales?</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Galería de imágenes -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-sm">
                                    <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                        <i class="bi bi-images"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="card-title mb-1">Galería de imágenes</h5>
                                <p class="text-muted mb-0">Las imágenes se guardan en public/productos/comercial_X/producto_X/</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Grid de imágenes existentes -->
                        <div id="imagenesGrid" class="image-grid"></div>

                        <!-- Área para subir imágenes -->
                        <div class="mt-4">
                            <label class="form-label fw-bold">Agregar imágenes</label>
                            <div id="dropzoneArea" class="dropzone-area">
                                <i class="bi bi-cloud-upload fs-1 text-muted"></i>
                                <p class="mb-0">Arrastra imágenes aquí o haz clic para seleccionar</p>
                                <small class="text-muted">Formatos: JPG, PNG (max 5MB cada una)</small>
                                <div class="progress-bar-container" id="progressBar">
                                    <div class="progress-bar-fill"></div>
                                </div>
                            </div>
                            <input type="file" id="fileInput" name="imagenes[]" multiple accept="image/jpeg,image/png,image/jpg" style="display: none;">

                            <div class="mt-2">
                                <button type="button" id="btnSubirPrincipal" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-star"></i> Establecer principal
                                </button>
                                <small class="text-muted ms-2">Haz clic en una imagen y luego en este botón para establecerla como principal</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mb-3">
                    <button type="submit" class="btn btn-success" id="btnSubmit">
                        <i class="ri-save-line"></i> Modificar
                    </button>
                    <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> Cancelar
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Estado</h5>
                    </div>
                    <div class="card-body">
                        <select class="form-select" name="activo">
                            <option value="1" {{ $producto->activo ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ !$producto->activo ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                </div>

                @if(Auth::user() && auth()->user()->type == 'admin')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-3">Precios</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Costo</label>
                                <input type="text" class="form-control price-input" name="preciod"
                                       value="{{  $producto->preciod +0 }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Precio 1</label>
                                <input type="text" class="form-control price-input" name="costod"
                                       value="{{  $producto->costod + 0}}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Precio 2</label>
                                <input type="text" class="form-control price-input" name="costod2"
                                       value="{{  $producto->costod2 +0 }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Precio 3 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control price-input" name="costod3"
                                       value="{{  $producto->costod3 +0 }}" required>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Observaciones</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" name="observaciones" rows="4">{{ $producto->observaciones }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const productoId = {{ $producto->id }};
        let imagenes = [];
        let selectedImageId = null;

        // Mostrar loading
        function showLoading() { document.getElementById('loadingOverlay').style.display = 'flex'; }
        function hideLoading() { document.getElementById('loadingOverlay').style.display = 'none'; }

        // Formatear números
        function formatNumberInput(input) {
           /* let value = input.value.replace(/\./g, '').replace(',', '.');
            if (!isNaN(parseFloat(value)) && isFinite(value)) {
                let num = parseFloat(value);
                input.value = num.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }*/
        }

        // Cargar imágenes existentes
        async function loadImagenes() {
            try {
                const response = await fetch(`/productos/${productoId}/imagenes`);
                const data = await response.json();
                if (data.success) {
                    imagenes = data.imagenes;
                    renderImageGrid();
                }
            } catch (error) {
                console.error('Error loading images:', error);
            }
        }

        // Renderizar grid de imágenes
        function renderImageGrid() {
            const grid = document.getElementById('imagenesGrid');
            if (!imagenes.length) {
                grid.innerHTML = '<div class="text-muted text-center py-4">No hay imágenes cargadas</div>';
                return;
            }

            grid.innerHTML = imagenes.map(img => `
            <div class="image-card ${img.es_principal ? 'principal' : ''}" data-id="${img.id}" onclick="selectImage(${img.id})">
                <img src="${img.url}?t=${Date.now()}" alt="Imagen producto">
                ${img.es_principal ? '<div class="image-badge"><i class="bi bi-star-fill"></i> Principal</div>' : ''}
                <div class="image-actions">
                    <button type="button" class="btn btn-danger btn-sm" onclick="event.stopPropagation(); deleteImage(${img.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
        }

        // Seleccionar imagen
        function selectImage(id) {
            selectedImageId = id;
            document.querySelectorAll('.image-card').forEach(card => {
                card.style.outline = card.getAttribute('data-id') == id ? '3px solid #0d6efd' : '';
            });
        }

        // Eliminar imagen
        async function deleteImage(imagenId) {
            const result = await Swal.fire({
                title: '¿Eliminar imagen?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar'
            });

            if (result.isConfirmed) {
                showLoading();
                try {
                    const response = await fetch(`/productos/${productoId}/imagen/${imagenId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await response.json();
                    if (data.success) {
                        await loadImagenes();
                        Swal.fire('Eliminada', 'Imagen eliminada correctamente', 'success');
                    }
                } catch (error) {
                    Swal.fire('Error', 'No se pudo eliminar la imagen', 'error');
                } finally {
                    hideLoading();
                }
            }
        }

        // Establecer imagen principal
        // Reemplazar la función setPrincipalImage

        // Establecer imagen principal
        async function setPrincipalImage() {
            if (!selectedImageId) {
                Swal.fire('Atención', 'Selecciona una imagen primero', 'warning');
                return;
            }

            showLoading();
            try {
                const response = await fetch(`/productos/${productoId}/imagen/${selectedImageId}/principal`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    await loadImagenes();
                    Swal.fire('Éxito', 'Imagen principal actualizada', 'success');
                } else {
                    Swal.fire('Error', data.message || 'No se pudo establecer como principal', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudo establecer como principal', 'error');
            } finally {
                hideLoading();
            }
        }

        // Subir imágenes (drag & drop o selección)
        async function uploadImages(files) {
            if (!files.length) return;

            const formData = new FormData();
            for (let file of files) {
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire('Error', `La imagen ${file.name} excede 5MB`, 'warning');
                    continue;
                }
                formData.append('imagenes[]', file);
            }

            showLoading();
            const progressBar = document.getElementById('progressBar');
            const progressFill = document.querySelector('.progress-bar-fill');
            progressBar.style.display = 'block';

            try {
                const response = await fetch(`/productos/${productoId}/imagenes-adicionales`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    await loadImagenes();
                    Swal.fire('Éxito', data.message, 'success');
                }
            } catch (error) {
                Swal.fire('Error', 'Error al subir imágenes', 'error');
            } finally {
                hideLoading();
                progressBar.style.display = 'none';
                progressFill.style.width = '0%';
            }
        }

        // Subir imagen principal (reemplaza la actual)
        async function uploadPrincipalImage(file) {
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire('Error', 'La imagen excede 5MB', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('imagen', file);

            showLoading();
            try {
                const response = await fetch(`/productos/${productoId}/imagen-principal`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    await loadImagenes();
                    Swal.fire('Éxito', 'Imagen principal actualizada', 'success');
                }
            } catch (error) {
                Swal.fire('Error', 'Error al subir imagen principal', 'error');
            } finally {
                hideLoading();
            }
        }

        // Submit del formulario principal
        // Reemplazar la función submitForm en el blade

        // Submit del formulario principal
        async function submitForm(event) {
            event.preventDefault();

            const form = document.getElementById('editproduct-form');
            if (!form.checkValidity()) {
                event.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            const codinst = document.getElementById('codinst').value;
            if (!codinst) {
                Swal.fire('Error', 'Seleccione una instancia de inventario', 'error');
                return;
            }

            showLoading();

            const formData = new FormData(form);

            try {
                const response = await fetch(`{{ route("productos.update", $producto->id) }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-HTTP-Method-Override': 'PUT',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                // Verificar si la respuesta es JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire('Éxito', 'Producto actualizado correctamente', 'success');
                        setTimeout(() => {
                            // Recargar la página para mostrar los cambios
                            window.location.reload();
                        }, 1500);
                    } else {
                        Swal.fire('Error', data.message || 'Error al actualizar', 'error');
                    }
                } else {
                    // Si no es JSON, es una redirección (éxito)
                    Swal.fire('Éxito', 'Producto actualizado correctamente', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Error de conexión', 'error');
            } finally {
                hideLoading();
            }
        }

        // Configurar drag & drop
        function setupDragAndDrop() {
            const dropzone = document.getElementById('dropzoneArea');
            const fileInput = document.getElementById('fileInput');

            dropzone.addEventListener('click', () => fileInput.click());
            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('dragover');
            });
            dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
                const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
                uploadImages(files);
            });

            fileInput.addEventListener('change', (e) => {
                uploadImages(Array.from(e.target.files));
                fileInput.value = '';
            });
        }

        // Inicializar
        document.addEventListener('DOMContentLoaded', () => {
            loadImagenes();
            setupDragAndDrop();

            document.querySelectorAll('.price-input').forEach(input => {
                input.addEventListener('blur', () => formatNumberInput(input));
            });

            document.getElementById('btnSubirPrincipal').addEventListener('click', () => {
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/*';
                input.onchange = (e) => uploadPrincipalImage(e.target.files[0]);
                input.click();
            });

            document.getElementById('editproduct-form').addEventListener('submit', submitForm);
        });
    </script>
@endsection
