{{-- resources/views/vehiculos/partials/modal-editar.blade.php --}}
<!-- Modal para editar vehículo -->
<div class="modal fade" id="editarVehiculoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="" method="POST" id="formEditarVehiculo" onsubmit="return validarFormVehiculo()">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Vehículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo *</label>
                            <select class="form-select" name="fk_tipo" id="edit_fk_tipo" required>
                                <option value="">Seleccione tipo</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->tipo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca *</label>
                            <input type="text" class="form-control" name="marca" id="edit_marca" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Modelo *</label>
                            <input type="text" class="form-control" name="modelo" id="edit_modelo" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Año</label>
                            <input type="number" class="form-control" name="year" id="edit_year" min="1900" max="2100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Placa/Identificación *</label>
                            <input type="text" class="form-control" name="identificacion" id="edit_identificacion" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Serial Motor</label>
                            <input type="text" class="form-control" name="serialmotor" id="edit_serialmotor">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Serial Chasis</label>
                            <input type="text" class="form-control" name="serialchasis" id="edit_serialchasis">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea class="form-control" name="observaciones" id="edit_observaciones" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
