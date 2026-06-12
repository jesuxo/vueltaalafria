{{-- resources/views/home/gallery/index.blade.php --}}
@extends('home.layouts.master')

@section('css')
    <style>
        /* Estilos generales */
        .gallery-stage-tab {
            cursor: pointer;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }
        .gallery-stage-tab.active {
            border-bottom-color: #00ecfe;
            color: #00ecfe;
        }
        .gallery-photo-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .gallery-photo-card:hover {
            transform: scale(1.02);
        }
        .gallery-photo-card.selected {
            box-shadow: 0 0 0 3px #00ecfe;
        }
        .photo-checkmark {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            background: #00ecfe;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .gallery-photo-card.selected .photo-checkmark {
            opacity: 1;
        }
        .cart-sidebar {
            position: fixed;
            right: -380px;
            top: 0;
            width: 380px;
            height: 100vh;
            background: white;
            box-shadow: -5px 0 20px rgba(0,0,0,0.1);
            z-index: 1000;
            transition: right 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .cart-sidebar.open {
            right: 0;
        }
        .cart-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
        }
        .cart-overlay.open {
            display: block;
        }
        .cart-toggle-btn {
            position: fixed;
            right: 20px;
            bottom: 20px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #00ecfe 0%, #00c4d4 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            z-index: 1001;
            box-shadow: 0 4px 15px rgba(0,236,254,0.3);
            transition: transform 0.3s;
        }
        .cart-toggle-btn:hover {
            transform: scale(1.1);
        }
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .photo-price-badge {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
        }
        .photo-price-badge i {
            color: #ffd700;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .cart-toggle-btn.has-items {
            animation: pulse 1s infinite;
        }

        /* Estilos para el buscador - todo en una línea */
        .search-container {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
        }

        .search-input-wrapper {
            flex: 1;
            position: relative;
        }

        .search-photo-input {
            width: 100%;
            border-radius: 50px;
            padding: 12px 40px 12px 20px;
            border: 1px solid #ddd;
            transition: all 0.3s;
            font-size: 16px;
            background: white;
        }

        .search-photo-input:focus {
            outline: none;
            border-color: #00ecfe;
            box-shadow: 0 0 0 3px rgba(0,236,254,0.1);
        }

        .clear-search-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 18px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s;
            z-index: 10;
        }

        .clear-search-btn:hover {
            color: #dc3545;
        }

        #searchPhotoBtn {
            flex-shrink: 0;
            white-space: nowrap;
            border-radius: 50px;
            padding: 12px 25px;
            margin-left: 0 !important;
        }

        /* Estilos para el indicador de búsqueda */
        .search-loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #00ecfe;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
            vertical-align: middle;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .search-status {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-top: 15px;
            animation: fadeIn 0.3s ease;
        }

        .search-status i {
            font-size: 24px;
            margin-bottom: 5px;
            display: inline-block;
            margin-right: 10px;
            vertical-align: middle;
        }

        .search-status p {
            display: inline-block;
            margin: 0;
            vertical-align: middle;
        }

        .search-results-count {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
            text-align: center;
            padding: 8px;
            background: #e8f0fe;
            border-radius: 20px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive para móviles */
        @media (max-width: 768px) {
            .search-container {
                flex-direction: column;
                gap: 10px;
            }

            .search-input-wrapper {
                width: 100%;
            }

            #searchPhotoBtn {
                width: 100%;
            }
        }
        .navbar {
            background: rgba(0,0,0,0.9) !important;
        }
    </style>
@endsection

@section('content')
    <section class="section" style="padding-top: 120px;">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>GALERÍA DE FOTOS</h2>
                <p>Selecciona las fotos que quieras comprar de cada etapa</p>
            </div>

            <!-- Buscador por dorsal/nombre -->
            <div class="row mb-4" data-aos="fade-up">
                <div class="col-md-8 mx-auto">
                    <div class="search-container">
                        <div class="search-input-wrapper">
                            <input type="text" id="searchPhotoInput" class="search-photo-input" placeholder="🔍 Buscar por dorsal, nombre o equipo...">
                            <button class="clear-search-btn" id="clearSearchBtn" style="display: none;">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                        <button class="btn-custom" id="searchPhotoBtn">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                    <div id="searchStatus" class="search-status" style="display: none;"></div>
                    <div id="searchResultsCount" class="search-results-count" style="display: none;"></div>
                </div>
            </div>

            <!-- Pestañas de etapas -->
            <div class="row mb-4" data-aos="fade-up">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-center gap-4">
                        @foreach($stages as $stage)
                            <div class="gallery-stage-tab py-2 px-3 {{ $loop->first ? 'active' : '' }}" data-stage-id="{{ $stage->id }}">
                                <h5 class="mb-0">{{ $stage->name }}</h5>
                                <small class="text-muted">
                                    @if($stage->stage_number)
                                        Etapa {{ $stage->stage_number }}
                                    @endif
                                    @if($stage->date)
                                        - {{ \Carbon\Carbon::parse($stage->date)->format('d/m/Y') }}
                                    @endif
                                </small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Grid de fotos -->
            <div class="row g-4" id="photosGrid" data-aos="fade-up">
                <div class="col-12 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </div>

            <!-- Indicador de carga infinita -->
            <div id="loadMoreTrigger" style="height: 20px;"></div>
        </div>
    </section>

    <!-- Carrito Sidebar -->
    <div class="cart-overlay" id="cartOverlay"></div>
    <div class="cart-sidebar" id="cartSidebar">
        <div class="p-3 border-bottom" style="background: #f8f9fa;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-cart me-2"></i> Mis Fotos
                    <span id="cartItemCount" class="badge bg-primary ms-2">0</span>
                </h5>
                <button class="btn-close" id="closeCartBtn"></button>
            </div>
            <small class="text-muted">Cada foto: <strong>$5 USD</strong></small>
        </div>
        <div class="flex-grow-1 overflow-auto p-3" id="cartItemsList">
            <div class="text-center text-muted py-5">
                <i class="fas fa-camera fa-3x mb-3"></i>
                <p>No has seleccionado ninguna foto</p>
                <small>Haz clic en las fotos que te gusten para agregarlas</small>
            </div>
        </div>
        <div class="p-3 border-top" style="background: #f8f9fa;">
            <div class="d-flex justify-content-between mb-3">
                <strong>Total:</strong>
                <strong id="cartTotal">$0.00 USD</strong>
            </div>
            <button class="btn-custom w-100" id="checkoutBtn">
                <i class="fas fa-credit-card me-2"></i> Solicitar Fotos
            </button>
            <button class="btn-outline-custom w-100 mt-2" id="clearCartBtn">
                <i class="fas fa-trash me-2"></i> Vaciar Carrito
            </button>
        </div>
    </div>

    <!-- Botón flotante del carrito -->
    <div class="cart-toggle-btn" id="cartToggleBtn">
        <i class="fas fa-shopping-cart fa-2x"></i>
        <span class="cart-count" id="floatingCartCount">0</span>
    </div>

    <!-- Modal de Checkout -->
    <div class="modal fade" id="checkoutModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #00ecfe 0%, #00c4d4 100%);">
                    <h5 class="modal-title text-white">Completar Pedido</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="checkoutForm">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Cada foto tiene un costo de <strong>$5 USD</strong>. Recibirás las fotos en alta resolución por email.
                        </div>
                        <div class="mb-3">
                            <label class="form-label required-field">Nombre Completo</label>
                            <input type="text" id="customer_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required-field">Email</label>
                            <input type="email" id="customer_email" class="form-control" required>
                            <small>Las fotos se enviarán a este correo</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required-field">Teléfono/WhatsApp</label>
                            <input type="text" id="customer_phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required-field">Método de Pago</label>
                            <select id="payment_method" class="form-select" required>
                                <option value="">Seleccionar</option>
                                <option value="transferencia">Transferencia Bancaria (VES/USD)</option>
                                <option value="bancolombia">Bancolombia (COP)</option>
                                <option value="usdt">USDT (Cripto)</option>
                            </select>
                        </div>
                        <div id="bankAccountsInfo" class="mb-3" style="display: none;">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-university me-2"></i> Cuentas Bancarias</h6>
                                    <hr>
                                    <p><strong>🏦 Banco Provincial:</strong> 0108-0133-8001-0004-2510<br>Beneficiario: Ruben Osorio</p>
                                    <p><strong>🏦 Bancolombia (COP):</strong> 901275648<br>Beneficiario: INVERSIONES OSORIO MOTOS S.A.S</p>
                                    <p><strong>🪙 USDT:</strong> Rubenaosorioe@gmail.com</p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3" id="referenceField" style="display: none;">
                            <label class="form-label required-field">Número de Referencia/Transacción</label>
                            <input type="text" id="payment_reference" class="form-control">
                        </div>
                        <div class="mb-3" id="proofField" style="display: none;">
                            <label class="form-label required-field">Comprobante de Pago</label>
                            <input type="file" id="payment_proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            <small class="text-muted">Sube el comprobante de tu transferencia (JPG, PNG o PDF, máx 2MB)</small>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="acceptTerms" required>
                            <label class="form-check-label">
                                Acepto que las fotos serán entregadas en un plazo máximo de 5 días hábiles después de confirmado el pago.
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-outline-custom" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn-custom">Enviar Solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ============================================
        // CARRITO CON LOCALSTORAGE
        // ============================================

        let cart = [];
        let currentStageId = null;
        let currentPage = 1;
        let isLoading = false;
        let hasMore = true;
        let currentSearchQuery = '';

        // Cargar carrito desde localStorage
        function loadCart() {
            const saved = localStorage.getItem('photoCart');
            if (saved) {
                cart = JSON.parse(saved);
                cart = cart.map(item => ({
                    ...item,
                    price: typeof item.price === 'number' ? item.price : parseFloat(item.price) || 5
                }));
                updateCartUI();
            }
        }

        // Guardar carrito en localStorage
        function saveCart() {
            const cleanCart = cart.map(item => ({
                id: item.id,
                filename: item.filename,
                thumbnail: item.thumbnail,
                price: typeof item.price === 'number' ? item.price : parseFloat(item.price) || 5
            }));
            localStorage.setItem('photoCart', JSON.stringify(cleanCart));
            updateCartUI();
        }

        // Agregar foto al carrito
        function addToCart(photo) {
            if (!cart.find(item => item.id === photo.id)) {
                let price = photo.price;
                if (typeof price === 'string') {
                    price = parseFloat(price);
                }
                if (isNaN(price)) {
                    price = 5;
                }

                cart.push({
                    id: photo.id,
                    filename: photo.filename,
                    thumbnail: photo.thumbnail_path,
                    price: price
                });
                saveCart();
                showNotification('Foto agregada al carrito', 'success');
                document.querySelector(`.gallery-photo-card[data-photo-id="${photo.id}"]`)?.classList.add('selected');
            } else {
                showNotification('Esta foto ya está en tu carrito', 'warning');
            }
        }

        // Quitar foto del carrito
        function removeFromCart(photoId) {
            cart = cart.filter(item => item.id !== photoId);
            saveCart();
            document.querySelector(`.gallery-photo-card[data-photo-id="${photoId}"]`)?.classList.remove('selected');
        }

        // Actualizar UI del carrito
        function updateCartUI() {
            const cartItems = document.getElementById('cartItemsList');
            const cartCount = document.getElementById('cartItemCount');
            const floatingCount = document.getElementById('floatingCartCount');
            const cartTotalSpan = document.getElementById('cartTotal');
            const cartToggleBtn = document.getElementById('cartToggleBtn');

            let total = 0;
            for (let i = 0; i < cart.length; i++) {
                let price = cart[i].price;
                if (typeof price === 'string') {
                    price = parseFloat(price);
                }
                if (isNaN(price)) {
                    price = 5;
                }
                total += price;
            }

            if (cartCount) cartCount.innerText = cart.length;
            if (floatingCount) {
                floatingCount.innerText = cart.length;
                if (cart.length > 0) {
                    cartToggleBtn.classList.add('has-items');
                } else {
                    cartToggleBtn.classList.remove('has-items');
                }
            }
            if (cartTotalSpan) {
                cartTotalSpan.innerText = `$${total.toFixed(2)} USD`;
            }

            if (cart.length === 0) {
                if (cartItems) {
                    cartItems.innerHTML = `
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-camera fa-3x mb-3"></i>
                            <p>No has seleccionado ninguna foto</p>
                            <small>Haz clic en las fotos que te gusten para agregarlas</small>
                        </div>
                    `;
                }
            } else {
                if (cartItems) {
                    cartItems.innerHTML = cart.map(item => {
                        let price = item.price;
                        if (typeof price === 'string') {
                            price = parseFloat(price);
                        }
                        if (isNaN(price)) {
                            price = 5;
                        }
                        return `
                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                <img src="/${item.thumbnail}" class="rounded" style="width: 60px; height: 50px; object-fit: cover;">
                                <div class="ms-3 flex-grow-1">
                                    <small class="text-muted d-block">Foto #${item.id}</small>
                                    <strong>$${price.toFixed(2)} USD</strong>
                                </div>
                                <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }).join('');
                }
            }
        }

        // Limpiar carrito
        function clearCart() {
            if (cart.length > 0 && confirm('¿Estás seguro de que quieres vaciar el carrito?')) {
                cart = [];
                saveCart();
                showNotification('Carrito vaciado', 'info');
                document.querySelectorAll('.gallery-photo-card').forEach(card => {
                    card.classList.remove('selected');
                });
            }
        }

        // Mostrar estado de búsqueda
        function showSearchStatus(status, message) {
            const searchStatus = document.getElementById('searchStatus');
            const searchResultsCount = document.getElementById('searchResultsCount');

            if (status === 'loading') {
                searchStatus.style.display = 'block';
                searchStatus.innerHTML = `
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="search-loading"></div>
                        <span class="ms-2">${message || 'Buscando fotos...'}</span>
                    </div>
                `;
                searchResultsCount.style.display = 'none';
            } else if (status === 'results') {
                searchStatus.style.display = 'block';
                searchStatus.innerHTML = `
                    <i class="fas fa-check-circle text-success"></i>
                    <p>${message}</p>
                `;
                setTimeout(() => {
                    if (searchStatus.style.display !== 'none') {
                        searchStatus.style.display = 'none';
                    }
                }, 3000);
                searchResultsCount.style.display = 'block';
                searchResultsCount.innerHTML = message;
                setTimeout(() => {
                    if (searchResultsCount.style.display !== 'none') {
                        searchResultsCount.style.display = 'none';
                    }
                }, 4000);
            } else if (status === 'error') {
                searchStatus.style.display = 'block';
                searchStatus.innerHTML = `
                    <i class="fas fa-exclamation-triangle text-warning"></i>
                    <p>${message}</p>
                `;
                setTimeout(() => {
                    searchStatus.style.display = 'none';
                }, 3000);
                searchResultsCount.style.display = 'none';
            } else if (status === 'clear') {
                searchStatus.style.display = 'none';
                searchResultsCount.style.display = 'none';
                searchStatus.innerHTML = '';
                searchResultsCount.innerHTML = '';
            }
        }

        // Limpiar búsqueda
        function clearSearch() {
            const searchInput = document.getElementById('searchPhotoInput');
            searchInput.value = '';
            currentSearchQuery = '';
            document.getElementById('clearSearchBtn').style.display = 'none';
            showSearchStatus('clear');
            hasMore = true;
            currentPage = 1;
            if (currentStageId) {
                loadPhotos(true);
            }
        }

        // ============================================
        // CARGAR FOTOS
        // ============================================

        async function loadPhotos(reset = true) {
            if (currentSearchQuery && currentSearchQuery.length >= 2) return;
            if (isLoading || !currentStageId) return;

            if (reset) {
                currentPage = 1;
                hasMore = true;
                document.getElementById('photosGrid').innerHTML = '';
            }

            isLoading = true;

            if (reset) {
                const grid = document.getElementById('photosGrid');
                grid.innerHTML = `
                    <div class="col-12 text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted">Cargando fotos...</p>
                    </div>
                `;
            }

            try {
                const response = await fetch(`/galeria/stage/${currentStageId}?page=${currentPage}`);
                const photos = await response.json();
                const grid = document.getElementById('photosGrid');

                if (reset) grid.innerHTML = '';

                if (!Array.isArray(photos)) {
                    if (reset) {
                        grid.innerHTML = `
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                                <p>Error al cargar las fotos. Por favor, intenta de nuevo.</p>
                            </div>
                        `;
                    }
                    hasMore = false;
                } else if (photos.length === 0) {
                    if (reset) {
                        grid.innerHTML = `
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-camera-slash fa-4x text-muted mb-3"></i>
                                <p>No hay fotos disponibles para esta etapa aún.</p>
                                <small>Las fotos se irán subiendo durante el evento.</small>
                            </div>
                        `;
                    }
                    hasMore = false;
                } else {
                    photos.forEach(photo => {
                        const isSelected = cart.some(item => item.id === photo.id);
                        const col = document.createElement('div');
                        col.className = 'col-md-4 col-lg-3';
                        col.innerHTML = `
                            <div class="gallery-photo-card ${isSelected ? 'selected' : ''}" data-photo-id="${photo.id}">
                                <img src="/${photo.thumbnail_path}" class="w-100" style="height: 200px; object-fit: cover;" alt="Foto">
                                <div class="photo-checkmark">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="photo-price-badge">
                                    <i class="fas fa-dollar-sign"></i> ${photo.price || 5}
                                </div>
                            </div>
                        `;
                        col.querySelector('.gallery-photo-card').addEventListener('click', () => {
                            if (isSelected) {
                                removeFromCart(photo.id);
                            } else {
                                addToCart(photo);
                            }
                        });
                        grid.appendChild(col);
                    });

                    if (photos.length < 20) hasMore = false;
                    else currentPage++;
                }
            } catch (error) {
                console.error('Error loading photos:', error);
                const grid = document.getElementById('photosGrid');
                if (reset) {
                    grid.innerHTML = `
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                            <p>Error de conexión. Por favor, intenta de nuevo.</p>
                        </div>
                    `;
                }
            } finally {
                isLoading = false;
            }
        }

        // Infinite scroll
        function setupInfiniteScroll() {
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting && hasMore && !isLoading && !currentSearchQuery) {
                    loadPhotos(false);
                }
            }, { threshold: 0.1 });
            const trigger = document.getElementById('loadMoreTrigger');
            if (trigger) observer.observe(trigger);
        }

        // ============================================
        // BÚSQUEDA
        // ============================================

        async function performSearch() {
            const searchInput = document.getElementById('searchPhotoInput');
            const query = searchInput.value.trim();

            if (query.length === 0) {
                if (currentSearchQuery) clearSearch();
                return;
            }

            if (query.length < 2) {
                showSearchStatus('error', 'Ingresa al menos 2 caracteres para buscar');
                return;
            }

            currentSearchQuery = query;
            showSearchStatus('loading', `Buscando "${query}"...`);

            document.querySelectorAll('.gallery-stage-tab').forEach(tab => {
                tab.style.opacity = '0.5';
                tab.style.pointerEvents = 'none';
            });

            currentPage = 1;
            hasMore = false;

            const grid = document.getElementById('photosGrid');
            grid.innerHTML = `
                <div class="col-12 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Buscando fotos que coincidan con "${query}"...</p>
                </div>
            `;

            try {
                const response = await fetch(`/galeria/search?q=${encodeURIComponent(query)}`);
                const photos = await response.json();
                grid.innerHTML = '';

                if (!Array.isArray(photos) || photos.length === 0) {
                    grid.innerHTML = `
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-camera-slash fa-4x text-muted mb-3"></i>
                            <p>No se encontraron fotos con "${query}"</p>
                            <small>Intenta con otro término de búsqueda (dorsal, nombre o equipo)</small>
                            <div class="mt-3">
                                <button class="btn-outline-custom" onclick="clearSearch()">
                                    <i class="fas fa-arrow-left me-2"></i> Volver a la galería
                                </button>
                            </div>
                        </div>
                    `;
                    showSearchStatus('results', `No se encontraron resultados para "${query}"`);
                } else {
                    photos.forEach(photo => {
                        const isSelected = cart.some(item => item.id === photo.id);
                        const col = document.createElement('div');
                        col.className = 'col-md-4 col-lg-3';
                        col.innerHTML = `
                            <div class="gallery-photo-card ${isSelected ? 'selected' : ''}" data-photo-id="${photo.id}">
                                <img src="/${photo.thumbnail_path}" class="w-100" style="height: 200px; object-fit: cover;" alt="Foto">
                                <div class="photo-checkmark">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="photo-price-badge">
                                    <i class="fas fa-dollar-sign"></i> ${photo.price || 5}
                                </div>
                            </div>
                        `;
                        col.querySelector('.gallery-photo-card').addEventListener('click', () => {
                            if (isSelected) removeFromCart(photo.id);
                            else addToCart(photo);
                        });
                        grid.appendChild(col);
                    });
                    showSearchStatus('results', `Se encontraron ${photos.length} foto(s) para "${query}"`);
                }
            } catch (error) {
                console.error('Error en búsqueda:', error);
                grid.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                        <p>Error de conexión al buscar</p>
                        <small>Por favor, intenta nuevamente</small>
                    </div>
                `;
                showSearchStatus('error', 'Error de conexión. Intenta nuevamente.');
            } finally {
                document.querySelectorAll('.gallery-stage-tab').forEach(tab => {
                    tab.style.opacity = '1';
                    tab.style.pointerEvents = 'auto';
                });
            }
        }

        // ============================================
        // CHECKOUT
        // ============================================

        async function submitOrder() {
            if (cart.length === 0) {
                showNotification('No has seleccionado ninguna foto', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('customer_name', document.getElementById('customer_name').value);
            formData.append('customer_email', document.getElementById('customer_email').value || '');
            formData.append('customer_phone', document.getElementById('customer_phone').value);
            formData.append('payment_method', document.getElementById('payment_method').value);
            formData.append('payment_reference', document.getElementById('payment_reference').value || '');
            formData.append('photos', JSON.stringify(cart.map(p => ({ id: p.id }))));

            const proofFile = document.getElementById('payment_proof').files[0];
            if (proofFile) {
                formData.append('payment_proof', proofFile);
            }

            const submitBtn = document.querySelector('#checkoutForm button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Procesando...';

            try {
                const response = await fetch('/galeria/order', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    cart = [];
                    saveCart();
                    bootstrap.Modal.getInstance(document.getElementById('checkoutModal')).hide();
                    document.getElementById('checkoutForm').reset();

                    Swal.fire({
                        title: '¡Pedido creado!',
                        html: `
                            <div class="text-center">
                                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                                <p><strong>Tu pedido ha sido creado exitosamente</strong></p>
                                <div class="alert alert-info">
                                    <strong>Código de pedido:</strong><br>
                                    <code style="font-size: 18px;">${result.public_code}</code>
                                </div>
                                <p>Guarda este código para consultar el estado de tu pedido</p>
                                <hr>
                                <div class="mt-3">
                                    <a href="${result.public_url}" class="btn btn-custom" target="_blank">
                                        <i class="fas fa-external-link-alt me-2"></i> Ver mi pedido
                                    </a>
                                </div>
                                ${result.qr_code ? `
                                <div class="mt-3">
                                    <img src="data:image/png;base64,${result.qr_code}" style="max-width: 150px;">
                                </div>
                                ` : ''}
                            </div>
                        `,
                        icon: 'success',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#00ecfe',
                        width: '500px'
                    });
                } else {
                    showNotification(result.message || 'Error al procesar el pedido', 'error');
                }
            } catch (error) {
                showNotification('Error de conexión. Intenta nuevamente.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }

        // ============================================
        // INICIALIZACIÓN
        // ============================================

        document.addEventListener('DOMContentLoaded', () => {
            loadCart();

            const firstTab = document.querySelector('.gallery-stage-tab.active');
            if (firstTab) {
                currentStageId = firstTab.dataset.stageId;
                loadPhotos();
                setupInfiniteScroll();
            }

            // Pestañas de etapas
            document.querySelectorAll('.gallery-stage-tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    if (currentSearchQuery) clearSearch();
                    document.querySelectorAll('.gallery-stage-tab').forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    currentStageId = tab.dataset.stageId;
                    loadPhotos(true);
                });
            });

            // Búsqueda
            const searchInput = document.getElementById('searchPhotoInput');
            const searchBtn = document.getElementById('searchPhotoBtn');
            const clearSearchBtn = document.getElementById('clearSearchBtn');

            searchInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    clearSearchBtn.style.display = 'flex';
                    if (this.value.length === 0 && currentSearchQuery) clearSearch();
                } else {
                    clearSearchBtn.style.display = 'none';
                    if (currentSearchQuery) clearSearch();
                }
            });

            clearSearchBtn.addEventListener('click', clearSearch);
            searchBtn.addEventListener('click', performSearch);
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') performSearch();
            });

            // Carrito sidebar
            const cartSidebar = document.getElementById('cartSidebar');
            const cartOverlay = document.getElementById('cartOverlay');
            const cartToggleBtn = document.getElementById('cartToggleBtn');
            const closeCartBtn = document.getElementById('closeCartBtn');

            function openCart() {
                cartSidebar.classList.add('open');
                cartOverlay.classList.add('open');
                document.body.style.overflow = 'hidden';
            }

            function closeCart() {
                cartSidebar.classList.remove('open');
                cartOverlay.classList.remove('open');
                document.body.style.overflow = '';
            }

            cartToggleBtn.addEventListener('click', openCart);
            closeCartBtn.addEventListener('click', closeCart);
            cartOverlay.addEventListener('click', closeCart);

            document.getElementById('clearCartBtn').addEventListener('click', clearCart);
            document.getElementById('checkoutBtn').addEventListener('click', () => {
                if (cart.length === 0) {
                    showNotification('No has seleccionado ninguna foto', 'warning');
                    return;
                }
                closeCart();
                new bootstrap.Modal(document.getElementById('checkoutModal')).show();
            });

            // Métodos de pago
            document.getElementById('payment_method').addEventListener('change', function() {
                const bankAccounts = document.getElementById('bankAccountsInfo');
                const referenceField = document.getElementById('referenceField');
                const proofField = document.getElementById('proofField');
                const method = this.value;

                if (method === 'transferencia' || method === 'bancolombia' || method === 'usdt') {
                    bankAccounts.style.display = 'block';
                    referenceField.style.display = 'block';
                    proofField.style.display = 'block';
                } else {
                    bankAccounts.style.display = 'none';
                    referenceField.style.display = 'none';
                    proofField.style.display = 'none';
                }
            });

            document.getElementById('checkoutForm').addEventListener('submit', (e) => {
                e.preventDefault();
                submitOrder();
            });
        });

        function showNotification(message, type = 'info') {
            Swal.fire({
                text: message,
                icon: type,
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    </script>
@endsection
