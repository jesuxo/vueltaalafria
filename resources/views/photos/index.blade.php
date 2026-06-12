{{-- resources/views/photos/index.blade.php --}}
@extends('layouts.master')

@section('title')
    Panel de fotos
@endsection

@section('css')
    <style>
        /* Tus estilos existentes se mantienen igual */
        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            max-height: 400px;
            overflow-y: auto;
        }
        .preview-item {
            position: relative;
            width: 150px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            background: white;
            transition: transform 0.2s;
        }
        .preview-item:hover {
            transform: scale(1.05);
        }
        .preview-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }
        .preview-item .preview-info {
            padding: 5px;
            font-size: 11px;
            text-align: center;
            background: white;
        }
        .preview-item .remove-preview {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255,0,0,0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            transition: all 0.2s;
        }
        .preview-item .remove-preview:hover {
            background: red;
            transform: scale(1.1);
        }
        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .clear-all-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
            cursor: pointer;
        }
        .upload-stats {
            background: #e8f0fe;
            border-left: 4px solid #00ecfe;
            padding: 10px 15px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 13px;
        }
        .file-validation-error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
        .drop-zone {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            background: #fafafa;
            transition: all 0.3s;
            cursor: pointer;
        }
        .drop-zone.dragover {
            border-color: #00ecfe;
            background: #e8f0fe;
        }
        .select-files-btn {
            background: #00ecfe;
            color: #000;
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            cursor: pointer;
            margin-top: 10px;
            font-weight: bold;
        }
        .photo-card-img {
            height: 180px;
            object-fit: cover;
            width: 100%;
        }
        .btn-group-custom {
            display: flex;
            gap: 5px;
        }
        .btn-group-custom .btn {
            flex: 1;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-camera me-2"></i> Subir Fotos por Etapa</h3>
                    </div>
                    <div class="card-body">
                        <!-- Drop Zone -->
                        <div class="drop-zone" id="dropZone">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p><strong>Arrastra tus fotos aquí</strong> o haz clic para seleccionarlas</p>
                            <small class="text-muted">Formatos permitidos: JPG, JPEG, PNG (máx 5MB por foto)</small>
                            <div>
                                <button type="button" class="select-files-btn" onclick="document.getElementById('photos').click()">
                                    <i class="fas fa-folder-open me-2"></i> Seleccionar archivos
                                </button>
                            </div>
                        </div>

                        <form id="uploadForm" enctype="multipart/form-data" style="display: none;">
                            @csrf
                            <input type="file" name="photos[]" id="photos" multiple accept="image/jpeg,image/png,image/jpg">
                        </form>

                        <!-- Selectores visibles con datos desde BD -->
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label class="form-label">Etapa</label>
                                <select id="stageSelect" class="form-select" required>
                                    <option value="">Seleccionar Etapa</option>
                                    @isset($stages)
                                        @foreach($stages as $stage)
                                            <option value="{{ $stage->id }}">  <!-- Cambiado: value = id -->
                                                {{ $stage->name }}
                                                @if($stage->stage_number)
                                                    - Etapa {{ $stage->stage_number }}
                                                @endif
                                                @if($stage->date)
                                                    ({{ \Carbon\Carbon::parse($stage->date)->format('d/m/Y') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="1">1ra Etapa - Contrarreloj (12/06)</option>
                                        <option value="2">2da Etapa - Ruta Umuquena (13/06)</option>
                                        <option value="3">3ra Etapa - Circuito Cerrado (14/06)</option>
                                        <option value="4">Premiación (14/06)</option>
                                    @endisset
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Precio por foto (USD)</label>
                                <input type="number" id="priceInput" class="form-control" placeholder="Precio por foto (USD)" value="5" step="0.5">
                            </div>
                        </div>

                        <!-- Área de preview -->
                        <div id="previewArea" style="display: none;">
                            <div class="preview-header mt-3">
                                <h6><i class="fas fa-images me-2"></i> Fotos seleccionadas (<span id="previewCount">0</span>)</h6>
                                <button type="button" class="clear-all-btn" onclick="clearAllPreviews()">
                                    <i class="fas fa-trash-alt me-1"></i> Limpiar todo
                                </button>
                            </div>
                            <div id="previewContainer" class="preview-container"></div>

                            <div class="upload-stats" id="uploadStats" style="display: none;">
                                <i class="fas fa-info-circle me-2"></i>
                                <span id="statsMessage"></span>
                            </div>

                            <div class="file-validation-error" id="validationError"></div>

                            <div class="mt-3">
                                <button type="button" class="btn btn-primary" id="submitUploadBtn" onclick="uploadPhotos()">
                                    <i class="fas fa-upload me-2"></i> Subir <span id="uploadCount">0</span> fotos
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="clearAllPreviews()">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </button>
                            </div>
                        </div>

                        <!-- Barra de progreso -->
                        <div id="uploadProgress" class="mt-3" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
                            </div>
                            <p class="text-center mt-2" id="progressText">Subiendo 0 de 0 fotos...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Fotos Recientes</h3>
                    </div>
                    <div class="card-body">
                        @if(isset($recentPhotos) && $recentPhotos->count() > 0)
                            <div class="row" id="recentPhotosContainer">
                                @foreach($recentPhotos as $photo)
                                    <div class="col-md-3 mb-3" id="photo-card-{{ $photo->id }}">
                                        <div class="card h-100">
                                            <img src="/{{ $photo->thumbnail_path }}" class="card-img-top photo-card-img" alt="{{ $photo->original_name }}">
                                            <div class="card-body">
                                                <p class="mb-1"><strong>Etapa:</strong> {{ $photo->stage->name }}</p>
                                                <p class="mb-1"><strong>Precio:</strong> ${{ number_format($photo->price, 2) }}</p>
                                                <p class="mb-2"><small class="text-muted">{{ $photo->original_name }}</small></p>
                                                <div class="btn-group-custom">
                                                    <button class="btn btn-sm btn-warning" onclick="tagPhoto({{ $photo->id }})">
                                                        <i class="fas fa-tag"></i> Etiquetar
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deletePhoto({{ $photo->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle me-2"></i>
                                No hay fotos subidas aún. ¡Sube tus primeras fotos usando el formulario de arriba!
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para etiquetar fotos -->
    <div class="modal fade" id="tagPhotoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-tag me-2"></i> Etiquetar Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="currentPhotoId">
                    <div class="mb-3">
                        <label class="form-label">Dorsal</label>
                        <input type="text" id="photoDorsal" class="form-control" placeholder="Ej: D0123">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre del Ciclista</label>
                        <input type="text" id="photoName" class="form-control" placeholder="Ej: Juan Pérez">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Equipo/Escuela</label>
                        <input type="text" id="photoTeam" class="form-control" placeholder="Ej: Team Osorio">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea id="photoDescription" class="form-control" rows="2" placeholder="Descripción opcional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveTags()">Guardar Etiquetas</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Variables globales
        let selectedFiles = [];
        let filePreviews = [];

        // Configuración
        const MAX_FILE_SIZE = 5 * 1024 * 1024;
        const ALLOWED_TYPES = ['image/jpeg', 'image/jpg', 'image/png'];

        // Elementos del DOM
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('photos');
        const previewArea = document.getElementById('previewArea');
        const previewContainer = document.getElementById('previewContainer');
        const previewCount = document.getElementById('previewCount');
        const uploadCount = document.getElementById('uploadCount');
        const stageSelect = document.getElementById('stageSelect');
        const priceInput = document.getElementById('priceInput');
        const validationError = document.getElementById('validationError');
        const uploadStats = document.getElementById('uploadStats');
        const statsMessage = document.getElementById('statsMessage');

        function validateFile(file) {
            if (!ALLOWED_TYPES.includes(file.type)) {
                return { valid: false, error: `Formato no válido: ${file.name}. Solo JPG, JPEG, PNG` };
            }
            if (file.size > MAX_FILE_SIZE) {
                return { valid: false, error: `Archivo muy grande: ${file.name}. Máximo 5MB` };
            }
            return { valid: true };
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function addFilesToSelection(files) {
            const filesArray = Array.from(files);
            const newFiles = [];
            const errors = [];

            filesArray.forEach(file => {
                const validation = validateFile(file);
                if (validation.valid) {
                    const isDuplicate = selectedFiles.some(existing =>
                        existing.name === file.name && existing.size === file.size
                    );
                    if (!isDuplicate) {
                        newFiles.push(file);
                    }
                } else {
                    errors.push(validation.error);
                }
            });

            if (errors.length > 0) {
                validationError.style.display = 'block';
                validationError.innerHTML = errors.join('<br>');
                setTimeout(() => {
                    validationError.style.display = 'none';
                }, 5000);
            } else {
                validationError.style.display = 'none';
            }

            if (newFiles.length > 0) {
                selectedFiles = [...selectedFiles, ...newFiles];
                generatePreviews(newFiles);
                updatePreviewUI();
            }
            showUploadStats();
        }

        function generatePreviews(files) {
            files.forEach((file, index) => {
                const reader = new FileReader();
                const fileId = Date.now() + '_' + index + '_' + Math.random();

                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
                    previewItem.setAttribute('data-file-id', fileId);
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}">
                        <button class="remove-preview" onclick="removeFileFromSelection('${fileId}')">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="preview-info">
                            <div class="fw-bold text-truncate" style="max-width: 140px;">${file.name}</div>
                            <div class="file-size">${formatFileSize(file.size)}</div>
                        </div>
                    `;
                    previewContainer.appendChild(previewItem);

                    filePreviews.push({
                        id: fileId,
                        file: file,
                        element: previewItem
                    });
                };
                reader.readAsDataURL(file);
            });
        }

        function removeFileFromSelection(fileId) {
            const previewIndex = filePreviews.findIndex(p => p.id === fileId);
            if (previewIndex !== -1) {
                const fileToRemove = filePreviews[previewIndex].file;
                const fileIndex = selectedFiles.findIndex(f =>
                    f.name === fileToRemove.name && f.size === fileToRemove.size
                );
                if (fileIndex !== -1) {
                    selectedFiles.splice(fileIndex, 1);
                }
                filePreviews[previewIndex].element.remove();
                filePreviews.splice(previewIndex, 1);
                updatePreviewUI();
                showUploadStats();
            }
        }

        function clearAllPreviews() {
            selectedFiles = [];
            filePreviews = [];
            previewContainer.innerHTML = '';
            updatePreviewUI();
            validationError.style.display = 'none';
            uploadStats.style.display = 'none';
            fileInput.value = '';
        }

        function updatePreviewUI() {
            const count = selectedFiles.length;
            previewCount.innerText = count;
            uploadCount.innerText = count;
            previewArea.style.display = count === 0 ? 'none' : 'block';
        }

        function showUploadStats() {
            const validCount = selectedFiles.length;
            const totalSize = selectedFiles.reduce((sum, file) => sum + file.size, 0);
            if (validCount > 0) {
                uploadStats.style.display = 'block';
                statsMessage.innerHTML = `${validCount} foto(s) · Peso total: ${formatFileSize(totalSize)} · Precio: $${priceInput.value || 5} USD`;
            } else {
                uploadStats.style.display = 'none';
            }
        }

        async function uploadPhotos() {
            // Validaciones
            if (selectedFiles.length === 0) {
                Swal.fire('Error', 'No has seleccionado ninguna foto', 'error');
                return;
            }

            const stageId = stageSelect.value;  // Cambiado: ahora es el ID
            if (!stageId) {
                Swal.fire('Error', 'Por favor selecciona una etapa', 'error');
                return;
            }

            const price = priceInput.value;
            if (!price || price <= 0) {
                Swal.fire('Error', 'Por favor ingresa un precio válido', 'error');
                return;
            }

            // Confirmar subida
            const confirm = await Swal.fire({
                title: '¿Subir fotos?',
                html: `Vas a subir <strong>${selectedFiles.length}</strong> fotos para la etapa seleccionada con precio <strong>$${price} USD</strong> cada una.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, subir',
                cancelButtonText: 'Cancelar'
            });

            if (!confirm.isConfirmed) return;

            // Preparar FormData
            const formData = new FormData();
            formData.append('stage_id', stageId);  // Cambiado: stage_id
            formData.append('price', price);

            selectedFiles.forEach(file => {
                formData.append('photos[]', file);
            });

            // Mostrar barra de progreso
            const progressBar = document.querySelector('#uploadProgress .progress-bar');
            const progressText = document.getElementById('progressText');
            const uploadProgressDiv = document.getElementById('uploadProgress');

            uploadProgressDiv.style.display = 'block';
            submitUploadBtn.disabled = true;
            progressBar.style.width = '0%';
            progressText.innerText = `Subiendo 0 de ${selectedFiles.length} fotos...`;

            try {
                const response = await fetch('/admin/fotos/upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                progressBar.style.width = '100%';
                progressText.innerText = `¡Completado! ${result.success} fotos subidas correctamente.`;

                if (result.success > 0) {
                    Swal.fire({
                        title: '¡Subida completada!',
                        html: `Se subieron <strong>${result.success}</strong> fotos correctamente.${result.errors.length > 0 ? `<br>${result.errors.length} fotos con error.` : ''}`,
                        icon: 'success',
                        timer: 3000
                    });

                    // Recargar lista de fotos recientes
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    Swal.fire('Error', 'No se pudo subir ninguna foto', 'error');
                }

            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Error de conexión al subir las fotos', 'error');
            } finally {
                uploadProgressDiv.style.display = 'none';
                submitUploadBtn.disabled = false;
            }
        }

        function setupDragAndDrop() {
            if (!dropZone) return;
            dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('dragover'); });
            dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('dragover');
                if (e.dataTransfer.files.length) addFilesToSelection(e.dataTransfer.files);
            });
            dropZone.addEventListener('click', () => fileInput.click());
        }

        function setupFileInput() {
            if (!fileInput) return;
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length) addFilesToSelection(e.target.files);
                fileInput.value = '';
            });
        }

        function tagPhoto(photoId) {
            document.getElementById('currentPhotoId').value = photoId;
            $('#tagPhotoModal').modal('show');
        }

        async function saveTags() {
            const photoId = document.getElementById('currentPhotoId').value;
            try {
                const response = await fetch(`/admin/fotos/tag/${photoId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({
                        dorsal: document.getElementById('photoDorsal').value,
                        name: document.getElementById('photoName').value,
                        team: document.getElementById('photoTeam').value,
                        description: document.getElementById('photoDescription').value
                    })
                });
                const result = await response.json();
                if (result.success) {
                    Swal.fire('Éxito', 'Etiquetas guardadas', 'success');
                    $('#tagPhotoModal').modal('hide');
                }
            } catch (error) {
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        }

        async function deletePhoto(photoId) {
            const confirm = await Swal.fire({ title: '¿Eliminar foto?', text: 'No se puede deshacer', icon: 'warning', showCancelButton: true, confirmButtonText: 'Sí, eliminar' });
            if (!confirm.isConfirmed) return;
            try {
                const response = await fetch(`/admin/fotos/${photoId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                const result = await response.json();
                if (result.success) {
                    Swal.fire('Eliminada', 'Foto eliminada', 'success');
                    document.getElementById(`photo-card-${photoId}`)?.remove();
                }
            } catch (error) {
                Swal.fire('Error', 'Error de conexión', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            setupDragAndDrop();
            setupFileInput();
            if (priceInput) priceInput.addEventListener('input', () => showUploadStats());
        });
    </script>
@endsection
