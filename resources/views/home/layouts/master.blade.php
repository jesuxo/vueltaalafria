<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Vuelta a la Fría 2026 | La Vuelta Menor Más Importante de Venezuela</title>

    <link rel="shortcut icon" href="{{asset('img/logo.png')}}" type="image/x-icon">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts - Más juveniles -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <script src="https://www.google.com/recaptcha/api.js"></script>

    <style>
        .text-danger {
            --bs-text-opacity: 1;
            color: rgba(var(--primary),var(--bs-text-opacity)) !important;
        }

        /* ============================================ */
        /* RESET Y VARIABLES */
        /* ============================================ */
        :root {
            --primary: #00ecfe;
            --primary-dark: #00c4d4;
            --secondary: #1a1a2e;
            --dark: #0a0a0a;
            --light: #f8f9fa;
            --gray: #6c757d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            color: #2c3e50;
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
        }

        /* ============================================ */
        /* NAVBAR - Más moderno */
        /* ============================================ */
        .navbar {
            background: rgba(0,0,0,0.1);
            padding: 20px 0;
            transition: all 0.4s ease;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar.scrolled {
            background: #242424;
            padding: 12px 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-size: 2rem;
            font-weight: 900;
            color: var(--primary) !important;
            letter-spacing: -2px;
            text-transform: uppercase;
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 600;
            transition: 0.3s;
            padding: 10px 18px !important;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary) !important;
        }

        /* Botón inscripción navbar */
        .btn-navbar {
            background: var(--primary);
            color: #000 !important;
            padding: 8px 25px !important;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
            border: none;
        }

        .btn-navbar:hover {
            background: #00c4d4;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,236,254,0.3);
            color: #000 !important;
        }

        /* Fecha en navbar */
        .navbar-date {
            font-size: 0.8rem;
            font-weight: 500;
            letter-spacing: 1px;
        }

        /* ============================================ */
        /* HERO SECTION - Full width épico */
        /* ============================================ */
        .hero {
            min-height: 100vh;
            background: url('/images/background_01.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            text-align: left !important;
        }

        .hero h1 {
            font-size: 6rem;
            font-weight: 900;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: -3px;
            text-align: left !important;
        }

        .hero h1 span {
            color: var(--primary);
            text-align: left !important;
        }

        .hero .fecha {
            font-size: 1.2rem;
            letter-spacing: 5px;
            margin-bottom: 1rem;
            text-transform: uppercase;
        }

        .hero .subtitulo {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            font-weight: 400;
        }

        /* Botones modernos */
        .btn-custom {
            background: var(--primary);
            color: #000;
            padding: 14px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            transition: 0.3s;
            display: inline-block;
            margin: 5px;
            border: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .btn-custom:hover {
            background: var(--primary-dark);
            color: #000;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,236,254,0.3);
        }

        .btn-outline-custom {
            background: transparent;
            border: 2px solid white;
            color: white;
        }

        .btn-outline-custom:hover {
            background: white;
            color: var(--primary);
            border-color: white;
        }

        /* ============================================ */
        /* SECTIONS - Más amplias */
        /* ============================================ */
        .section {
            padding: 90px 0;
        }

        .section-lg {
            padding: 120px 0;
        }

        .section-dark {
            background: var(--light);
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: -1px;
        }

        .section-title p {
            font-size: 1.2rem;
            color: var(--gray);
            max-width: 600px;
            margin: 0 auto;
        }

        /* ============================================ */
        /* ABOUT SECTION - Imagen flotante */
        /* ============================================ */
        .about-img {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            width: 100%;
            height: auto;
            transition: 0.3s;
        }

        .about-img:hover {
            transform: scale(1.02);
        }

        /* ============================================ */
        /* STATS SECTION - Cards modernas */
        /* ============================================ */
        .stat-card {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: 0.3s;
            border-bottom: 4px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            border-bottom-color: var(--primary);
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 900;
            color: var(--primary);
            line-height: 1;
        }

        .stat-label {
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            color: var(--gray);
            margin-top: 10px;
        }

        /* ============================================ */
        /* VIDEO SECTION - Con overlay */
        /* ============================================ */
        .video-section {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 100%);
            position: relative;
        }

        .video-wrapper {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        /* ============================================ */
        /* SCHEDULE - Timeline moderno */
        /* ============================================ */
        .schedule-timeline {
            max-width: 800px;
            margin: 0 auto;
        }

        .schedule-item {
            display: flex;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
            transition: 0.3s;
        }

        .schedule-item:hover {
            background: #f8f9fa;
            padding-left: 20px;
        }

        .schedule-time {
            min-width: 180px;
            font-weight: 800;
            color: var(--primary);
            font-size: 1.1rem;
        }

        .schedule-event {
            flex: 1;
            font-weight: 500;
        }

        /* ============================================ */
        /* ROUTE CARDS - Modernas */
        /* ============================================ */
        .route-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: 0.3s;
            height: 100%;
            text-align: center;
        }

        .route-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .route-card i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .route-card h4 {
            font-size: 1.3rem;
            margin-bottom: 15px;
        }

        /* ============================================ */
        /* TESTIMONIALS - Slider moderno */
        /* ============================================ */
        .testimonial-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            margin: 20px;
        }

        .testimonial-card i {
            font-size: 3rem;
            color: var(--primary);
            opacity: 0.4;
            margin-bottom: 20px;
        }

        .testimonial-card .quote {
            font-size: 1.1rem;
            font-style: italic;
            color: #555;
            line-height: 1.8;
        }

        .testimonial-card .author {
            margin-top: 20px;
            font-weight: 700;
            color: var(--primary);
        }

        /* ============================================ */
        /* PRICE CARDS */
        /* ============================================ */
        .price-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            transition: 0.3s;
            height: 100%;
        }

        .price-card:hover {
            transform: translateY(-10px);
        }

        .price-card.featured {
            transform: scale(1.05);
            box-shadow: 0 20px 50px rgba(0,236,254,0.2);
        }

        .price-card .price-header {
            background: var(--primary);
            color: #000;
            padding: 30px;
            text-align: center;
        }

        .price-card .price-header h3 {
            font-size: 2rem;
            margin-bottom: 0;
        }

        .price-card .price-body {
            padding: 30px;
            text-align: center;
        }

        .price-card .price {
            font-size: 3rem;
            font-weight: 900;
            color: var(--primary);
        }

        .price-card .price small {
            font-size: 1rem;
            font-weight: 400;
        }

        /* ============================================ */
        /* SPONSORS */
        /* ============================================ */
        .sponsor-item {
            text-align: center;
            padding: 20px;
            transition: 0.3s;
        }

        .sponsor-item img {
            max-height: 80px;
            filter: grayscale(100%);
            opacity: 0.6;
            transition: 0.3s;
        }

        .sponsor-item:hover img {
            filter: grayscale(0);
            opacity: 1;
            transform: scale(1.05);
        }

        /* ============================================ */
        /* CONTACT SECTION */
        /* ============================================ */
        .contact-info {
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }

        .contact-info i {
            width: 50px;
            height: 50px;
            background: var(--primary);
            color: #000;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .contact-form .form-control {
            padding: 12px 20px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }

        .contact-form .form-control:focus {
            border-color: var(--primary);
            box-shadow: none;
        }

        /* ============================================ */
        /* FOOTER */
        /* ============================================ */
        .footer {
            background: #242424;
            color: #999;
            padding: 70px 0 20px;
        }

        .footer h4 {
            color: white;
            margin-bottom: 25px;
            font-size: 1.3rem;
        }

        .footer .social-links a {
            color: white;
            margin-right: 15px;
            font-size: 1.3rem;
            transition: 0.3s;
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
        }

        .footer .social-links a:hover {
            background: var(--primary);
            color: #000;
            transform: translateY(-3px);
        }

        /* ============================================ */
        /* BACK TO TOP */
        /* ============================================ */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--primary);
            color: #000;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            cursor: pointer;
            transition: 0.3s;
            z-index: 99;
            opacity: 0;
            visibility: hidden;
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background: var(--primary-dark);
            transform: translateY(-5px);
        }

        /* ============================================ */
        /* ANIMACIONES */
        /* ============================================ */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ============================================ */
        /* RESPONSIVE */
        /* ============================================ */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 4rem;
            }
            .section {
                padding: 70px 0;
            }
            .section-title h2 {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
                letter-spacing: -1px;
            }
            .hero .subtitulo {
                font-size: 1rem;
            }
            .btn-custom {
                padding: 10px 25px;
                font-size: 0.8rem;
            }
            .schedule-item {
                flex-direction: column;
                text-align: center;
            }
            .schedule-time {
                margin-bottom: 10px;
            }
        }

        /* ============================================ */
        /* FIX MENÚ RESPONSIVE - BLANCO */
        /* ============================================ */

        /* Navbar en mobile */
        @media (max-width: 991.98px) {
            /* Fondo del navbar en mobile */
            .navbar {
                background-color: #181d34 !important;
            }

            /* Color del texto del menú */
            .navbar-nav .nav-link {
                color: #ffffff !important;
            }

            .navbar-nav .nav-link:hover {
                color: #00ecfe !important;
            }

            /* Ícono hamburguesa - blanco */
            .navbar-toggler {
                border-color: rgba(255, 255, 255, 0.5) !important;
            }

            .navbar-toggler-icon {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
            }

            /* Cerrar el menú al hacer click */
            .navbar-collapse {
                background-color: #181d34;
                padding: 15px;
                border-radius: 10px;
                margin-top: 10px;
            }

            /* Botón navbar en mobile */
            .btn-navbar {
                display: inline-block;
                width: auto;
                text-align: center;
                margin: 15px 15px 5px;
            }

            .navbar-date {
                text-align: center;
                padding: 10px 0;
                justify-content: center !important;
            }
        }

        /* Para pantallas aún más pequeñas */
        @media (max-width: 576px) {
            .navbar-brand {
                color: white !important;
            }

            .navbar-brand span {
                color: white !important;
            }

            .hero .fecha {
                font-size: 0.9rem;
                letter-spacing: 3px;
            }
        }

        /* Estilos para los tabs de inscripción */
        .nav-pills .nav-link {
            background: rgba(255,255,255,0.2) !important;
            border-radius: 50px !important;
            padding: 10px 25px !important;
            color: white !important;
        }

        .nav-pills .nav-link.active {
            background: #00ecfe !important;
            color: #000 !important;
        }

        @media (max-width: 768px) {
            .nav-pills .nav-link {
                font-size: 0.8rem;
                padding: 8px 15px !important;
            }
        }

        .btn-inscription-type {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 20px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-inscription-type:hover {
            border-color: #00ecfe;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .btn-inscription-type i {
            color: #00ecfe;
        }

        .btn-inscription-type h4 {
            color: #333;
            margin-bottom: 5px;
        }

        .btn-inscription-type p {
            color: #666;
        }

        /* Botón activo */
        .btn-inscription-type.active {
            background: linear-gradient(135deg, #00ecfe 0%, #00c4d4 100%);
            border-color: #00ecfe;
        }

        .btn-inscription-type.active i,
        .btn-inscription-type.active h4,
        .btn-inscription-type.active p {
            color: white;
        }

        /* Contenedor de formularios con animación */
        .form-container {
            transition: all 0.5s ease;
        }

        .form-container.show {
            display: block;
            animation: fadeInUp 0.5s ease;
        }

        /* Botón outline custom */
        .btn-outline-custom {
            background: transparent;
            border: 2px solid #00ecfe;
            color: #00ecfe;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            transition: 0.3s;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .btn-outline-custom:hover {
            background: #00ecfe;
            color: #000;
            transform: translateY(-3px);
        }
    </style>

    @yield('css')
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="/inicio">
            <img src="/images/logomenu.png" alt="Vuelta a la Fría 2026" style="max-width: 100%; max-height: 45px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#inicio">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="#informacion">La Carrera</a></li>
                <li class="nav-item"><a class="nav-link" href="#programa">Etapas</a></li>
                <li class="nav-item d-none"><a class="nav-link" href="#categorias">Categorías</a></li>
                <li class="nav-item d-none"><a class="nav-link" href="#clasificaciones">Premios</a></li>
                <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
            </ul>

            <!-- Fecha del evento -->
            <div class="navbar-date text-white ms-lg-3 mt-3 mt-lg-0 d-flex align-items-center">
                <i class="fas fa-calendar-alt me-1" style="color: #00ecfe;"></i>
                <small class="d-none d-lg-block">11-14 JUN 2026</small>
                <small class="d-lg-none">11-14 Junio 2026</small>
            </div>

            <!-- Botón inscripción -->
            <a href="#preinscripcion" class="btn-navbar ms-lg-3 mt-3 mt-lg-0">
                <i class="fas fa-user-plus me-1"></i> Inscribirme
            </a>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main>
    @yield('content')
</main>

@include('home.layouts.footer')

<!-- Back to Top -->
<div class="back-to-top">
    <i class="fas fa-arrow-up"></i>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    // Inicializar AOS
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });

    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        const backToTop = document.querySelector('.back-to-top');

        if (window.scrollY > 100) {
            navbar.classList.add('scrolled');
            backToTop.classList.add('show');
        } else {
            navbar.classList.remove('scrolled');
            backToTop.classList.remove('show');
        }
    });

    // Smooth scroll y cierre de menú en mobile
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#' || href === '#inicio') {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }

            // Cerrar menú en mobile
            const navbarCollapse = document.querySelector('.navbar-collapse');
            const navbarToggler = document.querySelector('.navbar-toggler');

            if (window.innerWidth <= 991 && navbarCollapse.classList.contains('show')) {
                navbarToggler.click();
            }
        });
    });

    // Back to top
    const backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Cerrar menú al hacer clic fuera
    document.addEventListener('click', function(event) {
        const navbar = document.querySelector('.navbar');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        const navbarToggler = document.querySelector('.navbar-toggler');

        if (window.innerWidth <= 991 && navbarCollapse && navbarCollapse.classList.contains('show')) {
            if (!navbar.contains(event.target) && event.target !== navbarToggler && !navbarToggler.contains(event.target)) {
                navbarToggler.click();
            }
        }
    });
</script>

@yield('scripts')

</body>
</html>
