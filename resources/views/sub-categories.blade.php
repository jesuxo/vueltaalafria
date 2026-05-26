{{-- sub-categories.blade.php --}}
@extends('layouts.master')
@section('title')
    Instancias
@endsection
@section('css')
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        .table-card .table {
            margin-bottom: 0;
        }
        .badge-soft-success {
            background-color: rgba(25, 135, 84, 0.1);
            color: #198754;
            cursor: pointer;
        }
        .badge-soft-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            cursor: pointer;
        }
        .badge-soft-secondary {
            background-color: rgba(108, 117, 125, 0.1);
            color: #6c757d;
            cursor: not-allowed;
        }
        .btn-soft-success {
            background-color: rgba(25, 135, 84, 0.1);
            border-color: transparent;
            color: #198754;
        }
        .btn-soft-success:hover {
            background-color: #198754;
            color: #fff;
        }
        .btn-soft-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-color: transparent;
            color: #dc3545;
        }
        .btn-soft-danger:hover {
            background-color: #dc3545;
            color: #fff;
        }
        .btn-soft-secondary {
            background-color: rgba(108, 117, 125, 0.1);
            border-color: transparent;
            color: #6c757d;
        }
        /* Estilo para mantener la indentación en el select */
        select option {
            font-family: monospace;
        }
        .instancia-padre-indent {
            padding-left: 20px;
            font-weight: normal;
        }
        .instancia-hijo-indent {
            padding-left: 40px;
            font-weight: normal;
            font-size: 0.95em;
        }
    </style>
@endsection
@section('content')
    <x-breadcrumb title="Instancias" pagetitle="Productos" />

    <div class="row">
        <div class="col-xxl-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0" id="addCategoryLabel">Crear Instancia</h6>
                </div>
                <div class="card-body">
                    <form autocomplete="off" class="needs-validation" id="createCategory-form" novalidate>
                        @csrf
                        <input type="hidden" id="categoryid-input" class="form-control" value="">
                        <div class="row">
                            <div class="col-xxl-12 col-lg-6">
                                <div class="mb-3">
                                    <label for="SubcategoryTitle" class="form-label">Descripción <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="SubcategoryTitle"
                                           placeholder="Ingrese la descripción..." required>
                                    <div class="invalid-feedback">Ingrese la descripción de la instancia</div>
                                </div>
                            </div>
                            <div class="col-xxl-12 col-lg-6">
                                <div class="mb-3">
                                    <label for="categorySelect" class="form-label">Instancia Padre</label>
                                    <select class="form-control" name="categorySelect" id="categorySelect">
                                        <option value="0">Seleccione</option>
                                        @if(isset($instanciaspadre))
                                            @foreach($instanciaspadre as $item)
                                                <option value="{{ $item->descrip }}">{!! $item->label !!}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="text-end">
                                    <button type="submit" id="addNewCategory" class="btn btn-success">Ingresar</button>
                                    <button type="button" id="cancelEdit" class="btn btn-secondary d-none">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-xxl-9">
            <div class="row justify-content-between mb-4">
                <div class="col-xxl-6 col-lg-6">
                    <div class="search-box mb-3 mb-lg-0">
                        <input type="text" class="form-control" id="searchResultList" autocomplete="off"
                               placeholder="Buscar instancia de inventario...">
                        <i class="ri-search-line search-icon"></i>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div id="product-sub-categories" class="table-card">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- removeItemModal -->
    <div id="removeItemModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body p-md-5">
                    <div class="text-center">
                        <div class="text-danger">
                            <i class="bi bi-trash display-4"></i>
                        </div>
                        <div class="mt-4 fs-15">
                            <h4 class="mb-1">¿Está seguro?</h4>
                            <p class="text-muted mx-3 fs-16 mb-0">¿Desea eliminar esta instancia?</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                        <button type="button" class="btn w-sm btn-light btn-hover"
                                data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn w-sm btn-danger btn-hover" id="remove-category">Borrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
        // Variables globales
        let instanciasData = [];
        let currentEditId = null;
        let currentDeleteId = null;

        // Función para escapar HTML
        function escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        // Función para mostrar errores
        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                timer: 3000,
                showConfirmButton: true
            });
        }

        // Función para mostrar éxito
        function showSuccess(message, title = 'Éxito') {
            Swal.fire({
                icon: 'success',
                title: title,
                text: message,
                timer: 2000,
                showConfirmButton: false
            });
        }

        // Función para obtener la indentación visual basada en el nivel (para la tabla)
        function getIndentation(nivel) {
            const spaces = (nivel - 1) * 4;
            return '&nbsp;'.repeat(spaces);
        }

        // Cargar instancias desde el servidor
        function loadInstancias() {
            $.ajax({
                url: '{{ route("sainsta.json") }}',
                type: 'GET',
                success: function(data) {
                    instanciasData = data;
                    renderTable();
                },
                error: function(xhr) {
                    console.error('Error cargando instancias:', xhr);
                    showError('No se pudieron cargar las instancias');
                    $('#product-sub-categories').html(`
                        <div class="text-center py-4">
                            <div class="text-danger">Error al cargar los datos</div>
                            <button class="btn btn-primary mt-2" onclick="loadInstancias()">Reintentar</button>
                        </div>
                    `);
                }
            });
        }

        // Renderizar tabla (mostrando la jerarquía visualmente)
        function renderTable() {
            const container = document.getElementById('product-sub-categories');
            const searchTerm = document.getElementById('searchResultList')?.value.toLowerCase() || '';

            // Filtrar datos
            let filteredData = instanciasData;
            if (searchTerm) {
                filteredData = instanciasData.filter(item =>
                    item.subcategory.toLowerCase().includes(searchTerm) ||
                    (item.category && item.category.toLowerCase().includes(searchTerm))
                );
            }

            if (filteredData.length === 0 && instanciasData.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <div class="avatar-md mx-auto mb-4">
                            <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-24">
                                <i class="bi bi-folder"></i>
                            </div>
                        </div>
                        <h5>No hay instancias creadas</h5>
                        <p class="text-muted">Comience creando una nueva instancia</p>
                    </div>
                `;
                return;
            }

            if (filteredData.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <div class="avatar-md mx-auto mb-4">
                            <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-24">
                                <i class="bi bi-search"></i>
                            </div>
                        </div>
                        <h5>No se encontraron resultados</h5>
                        <p class="text-muted">No hay instancias que coincidan con "${escapeHtml(searchTerm)}"</p>
                    </div>
                `;
                return;
            }

            // Generar HTML de la tabla con indentación visual
            let html = `
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="80">ID</th>
                                <th>Instancia</th>
                                <th>Instancia Padre</th>
                                <th width="150">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            filteredData.forEach(item => {
                const canDelete = !item.hijos && !item.productos && !item.servicios;
                const hasRelations = item.hijos || item.productos || item.servicios;
                const titleDisabled = hasRelations ? 'title="No se puede eliminar porque tiene elementos asociados"' : '';
                const nivel = item.nivel || 1;
                const indent = getIndentation(nivel);

                html += `
                    <tr>
                        <td class="fw-medium">${item.id}</td>
                        <td><strong>${indent}${escapeHtml(item.subcategory)}</strong></td>
                        <td>${item.category ? escapeHtml(item.category) : '<span class="text-muted">-</span>'}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-soft-success edit-instancia" data-id="${item.id}">
                                    <i class="ri-edit-line"></i> Editar
                                </button>
                                ${canDelete ? `
                                <button class="btn btn-sm btn-soft-danger delete-instancia" data-id="${item.id}">
                                    <i class="ri-delete-bin-line"></i> Borrar
                                </button>
                                ` : `
                                <button class="btn btn-sm btn-soft-secondary" disabled ${titleDisabled}>
                                    <i class="ri-delete-bin-line"></i> Borrar
                                </button>
                                `}
                            </div>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 text-muted">
                    Mostrando ${filteredData.length} de ${instanciasData.length} instancia(s)
                </div>
            `;

            container.innerHTML = html;

            // Reasignar eventos
            document.querySelectorAll('.edit-instancia').forEach(btn => {
                btn.addEventListener('click', () => editInstancia(parseInt(btn.dataset.id)));
            });

            document.querySelectorAll('.delete-instancia').forEach(btn => {
                btn.addEventListener('click', () => {
                    currentDeleteId = parseInt(btn.dataset.id);
                    const modal = new bootstrap.Modal(document.getElementById('removeItemModal'));
                    modal.show();
                });
            });
        }

        // Guardar instancia (crear o actualizar)
        function saveInstancia(isEdit = false) {
            const descripcion = document.getElementById('SubcategoryTitle').value.trim();
            const instanciaPadreSelect = document.getElementById('categorySelect');
            const instanciaPadre = instanciaPadreSelect.value;

            // Validaciones
            if (!descripcion) {
                showError('La descripción es requerida');
                return false;
            }

            const formData = {
                descrip: descripcion,
                insPadre: (!instanciaPadre || instanciaPadre === '0') ? '' : instanciaPadre
            };

            const url = isEdit ? `/instancias/${currentEditId}` : '/instancias';
            const method = isEdit ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                success: function(response) {
                    showSuccess(
                        isEdit ? 'La instancia se ha actualizado correctamente' : 'La instancia se ha creado correctamente',
                        isEdit ? 'Instancia actualizada' : 'Instancia creada'
                    );

                    // Recargar datos y limpiar formulario
                    loadInstancias();
                    clearForm();
                },
                error: function(xhr) {
                    let errorMsg = 'Error al guardar la instancia';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    showError(errorMsg);
                }
            });

            return true;
        }

        // Editar instancia
        function editInstancia(id) {
            const instancia = instanciasData.find(item => item.id === id);
            if (!instancia) return;

            currentEditId = id;
            document.getElementById('addCategoryLabel').innerHTML = 'Editar Instancia';
            document.getElementById('addNewCategory').innerHTML = 'Actualizar';
            document.getElementById('cancelEdit').classList.remove('d-none');
            document.getElementById('SubcategoryTitle').value = instancia.subcategory;

            // Seleccionar el padre en el select
            const padreValue = instancia.category || '0';
            const selectElement = document.getElementById('categorySelect');
            selectElement.value = padreValue;

            // Scroll al formulario
            document.querySelector('.col-xxl-3').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Eliminar instancia
        function deleteInstancia() {
            if (!currentDeleteId) return;

            $.ajax({
                url: `/instancias/${currentDeleteId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Cerrar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('removeItemModal'));
                    if (modal) modal.hide();

                    showSuccess('La instancia se ha eliminado correctamente', 'Instancia eliminada');

                    loadInstancias();
                    currentDeleteId = null;
                },
                error: function(xhr) {
                    let errorMsg = 'No se pudo eliminar la instancia';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMsg = xhr.responseJSON.error;
                    }
                    showError(errorMsg);
                }
            });
        }

        // Limpiar formulario
        function clearForm() {
            currentEditId = null;
            document.getElementById('addCategoryLabel').innerHTML = 'Crear Instancia';
            document.getElementById('addNewCategory').innerHTML = 'Ingresar';
            document.getElementById('cancelEdit').classList.add('d-none');
            document.getElementById('SubcategoryTitle').value = '';

            const selectElement = document.getElementById('categorySelect');
            if (selectElement) {
                selectElement.value = '0';
            }

            const form = document.getElementById('createCategory-form');
            form.classList.remove('was-validated');
        }

        // Inicializar todo
        $(document).ready(function() {
            // Cargar datos iniciales
            loadInstancias();

            // Evento de búsqueda
            $('#searchResultList').on('keyup', function() {
                renderTable();
            });

            // Evento del formulario
            $('#createCategory-form').on('submit', function(e) {
                e.preventDefault();

                if (!this.checkValidity()) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }

                const isEdit = currentEditId !== null;
                saveInstancia(isEdit);
            });

            // Botón cancelar edición
            $('#cancelEdit').on('click', function() {
                clearForm();
            });

            // Evento de eliminación
            $('#remove-category').on('click', function() {
                deleteInstancia();
            });

            // Resetear al cerrar modal
            $('#removeItemModal').on('hidden.bs.modal', function() {
                currentDeleteId = null;
            });
        });
    </script>
@endsection
