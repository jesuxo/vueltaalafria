// public/js/inscriptions.js

// Variables globales
let existingStructureId = null;
let existingStructureName = null;
let vehicleIndex = 1;
let searchTimeout = null;

// ============================================
// FUNCIONES DE EDAD Y CATEGORÍA
// ============================================

function getAgeFromBirthDate(birthDate) {
    if (!birthDate) return null;
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return age;
}

function displayAge() {
    const birthDate = document.getElementById('birth_date')?.value;
    const ageDisplay = document.getElementById('ageDisplay');
    if (birthDate && ageDisplay) {
        const age = getAgeFromBirthDate(birthDate);
        if (age !== null) {
            ageDisplay.innerHTML = '📅 Edad: ' + age + ' años';
            if (age < 3) {
                ageDisplay.style.color = 'red';
                ageDisplay.innerHTML += ' - No cumple con la edad mínima (3 años)';
            } else if (age > 18) {
                ageDisplay.style.color = 'red';
                ageDisplay.innerHTML += ' - Edad máxima permitida es 18 años';
            } else {
                ageDisplay.style.color = 'green';
            }
        }
    }
}

function filterCategoriesByAge() {
    const birthDate = document.getElementById('birth_date')?.value;
    const categorySelect = document.getElementById('categorySelect');
    if (!birthDate || !categorySelect) return;
    const age = getAgeFromBirthDate(birthDate);
    if (age === null) return;

    const ageRanges = {
        'Compota Strider': [3,4], 'Compota Pedales': [3,4],
        'Iniciación A': [5,6], 'Iniciación B': [7,8], 'Iniciación C': [9,10],
        'Pre-Infantil': [11,12], 'Infantil': [13,14],
        'Pre-Juvenil': [15,16], 'Juvenil': [17,18]
    };

    const options = categorySelect.querySelectorAll('option');
    options.forEach(opt => {
        const value = opt.value;
        if (!value) return;
        const range = ageRanges[value];
        if (range) {
            if (age >= range[0] && age <= range[1]) {
                opt.style.display = '';
                opt.disabled = false;
            } else {
                opt.style.display = 'none';
                opt.disabled = true;
            }
        }
    });

    const currentValue = categorySelect.value;
    const currentOption = Array.from(options).find(opt => opt.value === currentValue);
    if (currentOption && currentOption.disabled) {
        categorySelect.value = '';
    }
}

// ============================================
// FUNCIONES DE FORMULARIO
// ============================================

function showIndividualForm() {
    const individualForm = document.getElementById('individualForm');
    const teamForm = document.getElementById('teamForm');
    const btnIndividual = document.getElementById('btnIndividual');
    const btnTeam = document.getElementById('btnTeam');

    if (individualForm) individualForm.style.display = 'block';
    if (teamForm) teamForm.style.display = 'none';
    if (btnIndividual) btnIndividual.classList.add('active');
    if (btnTeam) btnTeam.classList.remove('active');

    setTimeout(() => {
        if (individualForm) {
            individualForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }, 100);
}

function showTeamForm() {
    const individualForm = document.getElementById('individualForm');
    const teamForm = document.getElementById('teamForm');
    const btnIndividual = document.getElementById('btnIndividual');
    const btnTeam = document.getElementById('btnTeam');

    if (individualForm) individualForm.style.display = 'none';
    if (teamForm) teamForm.style.display = 'block';
    if (btnIndividual) btnIndividual.classList.remove('active');
    if (btnTeam) btnTeam.classList.add('active');

    setTimeout(() => {
        if (teamForm) {
            teamForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }, 100);
}

function hideForms() {
    const individualForm = document.getElementById('individualForm');
    const teamForm = document.getElementById('teamForm');
    const btnIndividual = document.getElementById('btnIndividual');
    const btnTeam = document.getElementById('btnTeam');

    if (individualForm) individualForm.style.display = 'none';
    if (teamForm) teamForm.style.display = 'none';
    if (btnIndividual) btnIndividual.classList.remove('active');
    if (btnTeam) btnTeam.classList.remove('active');
}

function toggleStructureField() {
    const structureYes = document.getElementById('structure_yes');
    const structureField = document.getElementById('structureField');
    if (structureField) {
        structureField.style.display = (structureYes && structureYes.checked) ? 'block' : 'none';
    }
}

// ============================================
// BUSCADOR PREDICTIVO
// ============================================

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function escapeRegex(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function highlightText(text, query) {
    if (!query) return escapeHtml(text);
    const regex = new RegExp('(' + escapeRegex(query) + ')', 'gi');
    return escapeHtml(text).replace(regex, '<span class="suggestion-highlight">$1</span>');
}

function selectStructure(id, name) {
    existingStructureId = id;
    existingStructureName = name;
    document.getElementById('structure_search').value = name;
    document.getElementById('structure_id').value = id;
    document.getElementById('structure_name_hidden').value = name;
    document.getElementById('structureSuggestions').style.display = 'none';
    document.getElementById('newStructureOption').style.display = 'none';

    var infoHtml = '<div class="alert alert-success" style="font-size: 0.9rem;">' +
        '<i class="fas fa-check-circle me-2"></i>' +
        '<strong>' + escapeHtml(name) + '</strong> (Estructura existente)' +
        '<br><small>Se asignará automáticamente a este equipo.</small>' +
        '</div>';

    document.getElementById('selectedStructureInfo').innerHTML = infoHtml;
    document.getElementById('structure_search').classList.remove('is-invalid');
}

function showNewStructureOption() {
    const searchValue = document.getElementById('structure_search').value;
    if (searchValue.length >= 2) {
        if (existingStructureId && existingStructureName && existingStructureName.toLowerCase() === searchValue.toLowerCase()) {
            var errorHtml = '<div class="alert alert-danger" style="font-size: 0.9rem;">' +
                '<i class="fas fa-exclamation-triangle me-2"></i>' +
                '<strong>"' + escapeHtml(searchValue) + '"</strong> ya existe como estructura.' +
                '<br><small>Por favor selecciona la estructura existente de la lista.</small>' +
                '</div>';
            document.getElementById('selectedStructureInfo').innerHTML = errorHtml;
            document.getElementById('newStructureOption').style.display = 'none';
            document.getElementById('structure_search').classList.add('is-invalid');
            return;
        }
        document.getElementById('newStructureName').innerText = searchValue;
        document.getElementById('newStructureOption').style.display = 'block';
        document.getElementById('structureSuggestions').style.display = 'none';
    }
}

function searchStructures(query) {
    if (query.length < 2) {
        document.getElementById('structureSuggestions').style.display = 'none';
        document.getElementById('newStructureOption').style.display = 'none';
        return;
    }

    const suggestionsDiv = document.getElementById('structureSuggestions');
    suggestionsDiv.innerHTML = '<div class="list-group-item text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Buscando...</div>';
    suggestionsDiv.style.display = 'block';

    fetch('/buscar-estructuras?q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            existingStructureId = null;
            existingStructureName = null;

            const exactMatch = data.find(team => team.name.toLowerCase() === query.toLowerCase());
            if (exactMatch) {
                existingStructureId = exactMatch.id;
                existingStructureName = exactMatch.name;
            }

            if (data.length === 0) {
                suggestionsDiv.style.display = 'none';
                document.getElementById('newStructureName').innerText = query;
                document.getElementById('newStructureOption').style.display = 'block';
                document.getElementById('createNewStructure').checked = false;
            } else {
                let html = '';
                for (let i = 0; i < data.length; i++) {
                    const team = data[i];
                    const isExactMatch = team.name.toLowerCase() === query.toLowerCase();
                    const badgeClass = isExactMatch ? 'bg-warning' : 'bg-success';
                    const badgeText = isExactMatch ? 'Coincidencia exacta' : 'Existente';

                    html += '<div class="list-group-item list-group-item-action" onclick="selectStructure(' + team.id + ', \'' + escapeHtml(team.name) + '\')">' +
                        '<div class="d-flex justify-content-between align-items-center">' +
                        '<div>' +
                        '<strong>' + highlightText(team.name, query) + '</strong>';

                    if (team.city) {
                        html += '<br><small class="text-muted"><i class="fas fa-map-marker-alt"></i> ' + escapeHtml(team.city) + '</small>';
                    }

                    html += '</div>' +
                        '<span class="badge ' + badgeClass + '">' + badgeText + '</span>' +
                        '</div>' +
                        '</div>';
                }

                if (!exactMatch) {
                    html += '<div class="list-group-item list-group-item-action text-primary" onclick="showNewStructureOption()">' +
                        '<i class="fas fa-plus-circle me-2"></i> Crear nueva estructura "' + escapeHtml(query) + '"' +
                        '</div>';
                    document.getElementById('newStructureOption').style.display = 'none';
                } else {
                    html += '<div class="list-group-item list-group-item-action text-warning">' +
                        '<i class="fas fa-exclamation-triangle me-2"></i>' +
                        '<strong>"' + escapeHtml(query) + '"</strong> ya existe. Selecciona la opción de arriba.' +
                        '</div>';
                    document.getElementById('newStructureOption').style.display = 'none';
                }

                suggestionsDiv.innerHTML = html;
                suggestionsDiv.style.display = 'block';
            }
        }).catch(function() {
        suggestionsDiv.innerHTML = '<div class="list-group-item text-danger">Error al buscar. Intenta nuevamente.</div>';
    });
}

// ============================================
// VEHÍCULOS
// ============================================

function addVehicleRow() {
    const tbody = document.getElementById('vehiclesBody');
    if (tbody) {
        const currentIndex = document.querySelectorAll('#vehiclesBody tr').length;
        const newRow = '<tr>' +
            '<td><input type="text" name="vehicles[' + currentIndex + '][brand]" class="form-control form-control-sm" placeholder="Marca"></td>' +
            '<td><input type="text" name="vehicles[' + currentIndex + '][model]" class="form-control form-control-sm" placeholder="Modelo"></td>' +
            '<td><input type="text" name="vehicles[' + currentIndex + '][plate]" class="form-control form-control-sm" placeholder="Placa"></td>' +
            '<td><input type="number" name="vehicles[' + currentIndex + '][year]" class="form-control form-control-sm" placeholder="Año"></td>' +
            '<td><input type="text" name="vehicles[' + currentIndex + '][color]" class="form-control form-control-sm" placeholder="Color"></td>' +
            '<td><button type="button" class="btn btn-sm btn-danger" onclick="removeVehicleRow(this)"><i class="fas fa-trash"></i></button></td>' +
            '</tr>';
        tbody.insertAdjacentHTML('beforeend', newRow);
    }
}

function removeVehicleRow(button) {
    const row = button.closest('tr');
    const tbody = document.getElementById('vehiclesBody');
    if (row && tbody && tbody.children.length > 1) {
        row.remove();
    } else if (row) {
        const inputs = row.querySelectorAll('input');
        for (let i = 0; i < inputs.length; i++) {
            inputs[i].value = '';
        }
    }
}

function updateFileName(name) {
    const fileNameDiv = document.getElementById('fileName');
    if (fileNameDiv) {
        fileNameDiv.innerHTML = '<i class="fas fa-check-circle text-success"></i> Archivo seleccionado: ' + name;
    }
}

function showStructureError(message) {
    const errorDiv = document.getElementById('structureError');
    const errorMsg = document.getElementById('structureErrorMessage');
    if (errorDiv && errorMsg) {
        errorMsg.innerHTML = message;
        errorDiv.style.display = 'block';
        document.getElementById('structure_search').classList.add('is-invalid');
        setTimeout(() => {
            errorDiv.style.display = 'none';
            document.getElementById('structure_search').classList.remove('is-invalid');
        }, 5000);
    }
}

// ============================================
// EVENT LISTENERS
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Eventos de estructura
    const structureNo = document.getElementById('structure_no');
    const structureYes = document.getElementById('structure_yes');
    if (structureNo) structureNo.addEventListener('change', toggleStructureField);
    if (structureYes) structureYes.addEventListener('change', toggleStructureField);

    // Evento fecha de nacimiento
    const birthDateInput = document.getElementById('birth_date');
    if (birthDateInput) {
        birthDateInput.addEventListener('change', function() {
            displayAge();
            filterCategoriesByAge();
        });
    }

    // Evento búsqueda de estructuras
    const structureSearch = document.getElementById('structure_search');
    if (structureSearch) {
        structureSearch.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value;
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => searchStructures(query), 300);
            } else {
                const suggestionsDiv = document.getElementById('structureSuggestions');
                if (suggestionsDiv) suggestionsDiv.style.display = 'none';
                const newOptionDiv = document.getElementById('newStructureOption');
                if (newOptionDiv) newOptionDiv.style.display = 'none';
            }
        });
    }

    // Evento checkbox crear nueva estructura
    const createNewStructure = document.getElementById('createNewStructure');
    if (createNewStructure) {
        createNewStructure.addEventListener('change', function(e) {
            const searchValue = document.getElementById('structure_search').value;
            const checkbox = e.target;

            if (checkbox.checked) {
                if (existingStructureId && existingStructureName && existingStructureName.toLowerCase() === searchValue.toLowerCase()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Nombre duplicado',
                        text: 'La estructura "' + searchValue + '" ya existe. No puedes crear una nueva con el mismo nombre.',
                        confirmButtonColor: '#00ecfe'
                    });
                    checkbox.checked = false;
                    return;
                }

                fetch('/verificar-estructura?nombre=' + encodeURIComponent(searchValue))
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Nombre duplicado',
                                text: 'La estructura "' + searchValue + '" ya existe en el sistema.',
                                confirmButtonColor: '#00ecfe'
                            });
                            checkbox.checked = false;
                            document.getElementById('structure_id').value = data.id;
                            document.getElementById('structure_name_hidden').value = searchValue;
                            document.getElementById('structure_search').value = searchValue;
                            document.getElementById('selectedStructureInfo').innerHTML = '<div class="alert alert-warning">' + escapeHtml(searchValue) + ' ya existe. Se usará la estructura existente.</div>';
                        } else {
                            document.getElementById('structure_id').value = '';
                            document.getElementById('structure_name_hidden').value = searchValue;
                            document.getElementById('selectedStructureInfo').innerHTML = '<div class="alert alert-info">Se creará: <strong>' + escapeHtml(searchValue) + '</strong></div>';
                        }
                    });
            } else {
                document.getElementById('structure_id').value = '';
                document.getElementById('structure_name_hidden').value = '';
                document.getElementById('selectedStructureInfo').innerHTML = '';
            }
        });
    }

    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        const suggestions = document.getElementById('structureSuggestions');
        const search = document.getElementById('structure_search');
        if (suggestions && search && !search.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.style.display = 'none';
        }
    });

    // Drag and drop para archivo
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('excelFile');
    const fileNameDiv = document.getElementById('fileName');

    if (uploadArea) {
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function() {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                if (fileNameDiv) {
                    fileNameDiv.innerHTML = '<i class="fas fa-check-circle text-success"></i> Archivo: ' + e.dataTransfer.files[0].name;
                }
            }
        });

        uploadArea.addEventListener('click', function() {
            if (fileInput) fileInput.click();
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length && fileNameDiv) {
                fileNameDiv.innerHTML = '<i class="fas fa-check-circle text-success"></i> Archivo: ' + e.target.files[0].name;
            }
        });
    }
});
