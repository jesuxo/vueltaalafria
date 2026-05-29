@extends('home.layouts.master')

@section('css')
    <style>



        .route-map-img {
            border-radius: 20px;
            width: 100%;
            height: auto;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .gallery-img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 15px;
            transition: 0.4s;
            cursor: pointer;
        }

        .gallery-img:hover {
            transform: scale(1.03);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
    </style>

    <style>
        /* ============================================ */
        /* TESTIMONIALS - CORREGIDO */
        /* ============================================ */
        .testimonial-swiper {
            width: 100%;
            padding: 20px 0 50px 0 !important;
            overflow: hidden !important;
        }

        .swiper-wrapper {
            display: flex;
            align-items: stretch;
        }

        .swiper-slide {
            height: auto !important;
            display: flex !important;
        }

        .testimonial-card {
            background: white;
            padding: 30px 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: transform 0.3s;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
        }

        .testimonial-card i {
            font-size: 2rem;
            color: #00ecfe;
            opacity: 0.4;
            margin-bottom: 15px;
        }

        .testimonial-card .quote {
            font-size: 0.95rem;
            font-style: italic;
            color: #555;
            line-height: 1.6;
            flex: 1;
        }

        .testimonial-card .author {
            margin-top: 15px;
            font-weight: 700;
            color: #00ecfe;
            font-size: 0.85rem;
        }

        /* Paginación */
        .swiper-pagination {
            position: relative !important;
            bottom: auto !important;
            margin-top: 30px !important;
        }

        .swiper-pagination-bullet {
            width: 10px !important;
            height: 10px !important;
            background: #ccc !important;
            opacity: 1 !important;
            margin: 0 5px !important;
        }

        .swiper-pagination-bullet-active {
            background: #00ecfe !important;
        }

        /* Asegurar que el contenedor no corte nada */
        .testimonials-wrapper {
            overflow: visible !important;
        }

        .route-map-img {
            border-radius: 20px;
            width: 100%;
            height: auto;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .gallery-img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 15px;
            transition: 0.4s;
            cursor: pointer;
        }

        .gallery-img:hover {
            transform: scale(1.03);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .testimonial-card {
                padding: 20px 15px;
            }
            .testimonial-card .quote {
                font-size: 0.85rem;
            }
        }
        .btn-danger {
            --bs-btn-color: #fff;
            --bs-btn-bg: #00ecfe;
            --bs-btn-border-color: #00ecfe;
        }

        @media (max-width: 768px) {
            .hero .container .row img {
                max-height: 50px !important;
            }
        }
    </style>
@endsection

@section('content')
    @section('content')
        <!-- Hero Section -->
        <section class="hero" id="inicio" style="text-align: left !important;">
            <div class="container text-center text-white">
                <div data-aos="fade-up" style="text-align: left !important;">
                    <p class="fecha" style="font-size: 30px; padding: 20px 0px">11 - 14 JUNIO 2026</p>
                    <h1 style="margin:0px;">VUELTA A LA <br><span>FRÍA</span></h1>
                    <p class="subtitulo" style="font-size: 20px;">LA VUELTA MENOR MÁS IMPORTANTE DE VENEZUELA</p>
                    <div>
                        <a href="#preinscripcion" class="btn-custom">Inscribirme Ahora</a>
                        <a href="#informacion" class="btn-custom btn-outline-custom">Más Información</a>
                    </div>
                    <div class="row mt-5">
                        <div class="col-12 mt-2 col-md-6">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-4 col-md-4 text-center">
                                    <img src="/img/gob1.png" class="img-fluid" style="max-height: 70px; width: auto;">
                                </div>
                                <div class="col-4 col-md-3 text-center">
                                    <img src="/img/gob2.png" class="img-fluid" style="max-height: 70px; width: auto; margin-left: 0;">
                                </div>
                                <div class="col-4 col-md-4 text-left">
                                    <img src="/img/gob3.png" class="img-fluid" style="max-height: 70px; width: auto;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Divisor de Montañas -->
        <div class="mountain-divider-modern" style="position: relative; height: 150px; overflow: hidden; margin-top: -84px;">
            <div style="position: absolute; bottom: 50px; left: 0; width: 100%; height: 100px; background: url('/images/mountains_pattern_01.png') repeat-x bottom; background-size: auto 100%;"></div>
        </div>

        <!-- Información Section -->
        <section id="informacion" class="section" style="text-align: left !important;">
            <div class="container" style="text-align: left !important;">
                <div class="section-title" data-aos="fade-up">
                    <h2>INFORMACIÓN</h2>
                    <p>Carrera de montaña legendaria desde 1997</p>
                </div>
                <div class="row align-items-center g-5">
                    <div class="col-lg-6" data-aos="fade-right">
                        <img src="https://images.unsplash.com/photo-1541625602330-2277a4c46182?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Ciclismo Montaña" class="about-img">
                    </div>
                    <div class="col-lg-6" data-aos="fade-left">
                        <p class="lead">La Vuelta Menor a La Fría es un evento de ruta por etapas que se realizará del 11 al 14 de junio de 2026 en el municipio García de Hevia, estado Táchira, Venezuela.</p>
                        <p>El evento está regido por el reglamento de la Unión Ciclista Internacional (UCI) y la Federación Venezolana de Ciclismo (FVC). La participación es abierta para ciclistas independientes y equipos organizados como clubes y escuelas, mediante invitación para las categorías menores desde Stryder hasta Juvenil.</p>
                        <p class="mt-3">La carrera cuenta con el aval de la Asociación Tachirense de Ciclismo, la Comisión Nacional de Ciclismo Menor y Juvenil, la Federación Venezolana de Ciclismo (FVC), el Instituto Municipal de Deporte (IMDERE) y el Instituto del Deporte Tachirense (IDT).</p>
                        <div class="mt-4">
                            <div class="d-flex gap-4">
                                <div>
                                    <i class="fas fa-trophy text-danger fs-1"></i>
                                    <p class="fw-bold mt-2">Clase Mundial</p>
                                </div>
                                <div>
                                    <i class="fas fa-mountain text-danger fs-1"></i>
                                    <p class="fw-bold mt-2">Paisajes Épicos</p>
                                </div>
                                <div>
                                    <i class="fas fa-users text-danger fs-1"></i>
                                    <p class="fw-bold mt-2">Múltiples Categorías</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="section section-dark">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-3 col-6" data-aos="fade-up">
                        <div class="stat-card">
                            <div class="stat-number">4</div>
                            <div class="stat-label">DÍAS</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="stat-card">
                            <div class="stat-number">70+</div>
                            <div class="stat-label">KM MÁXIMO</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-card">
                            <div class="stat-number">10+</div>
                            <div class="stat-label">CATEGORÍAS</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="stat-card">
                            <div class="stat-number">2026</div>
                            <div class="stat-label">EDICIÓN</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categorías Section -->
        <section class="section">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>CATEGORÍAS</h2>
                    <p>Categorías por edad y modalidad</p>
                </div>
                <div class="row g-4">
                    <div class="col-lg-6" data-aos="fade-right">
                        <div class="p-4 bg-white rounded-4 h-100 shadow-sm">
                            <h3 class="text-danger mb-4"><i class="fas fa-child me-2"></i> Categorías por Etapas (3 días)</h3>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check-circle text-danger me-2"></i> <strong>Pre-Infantil</strong> (11-12 años) - Nacidos 2015-2014</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-danger me-2"></i> <strong>Infantil</strong> (13-14 años) - Nacidos 2013-2012</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-danger me-2"></i> <strong>Pre-Juvenil</strong> (15-16 años) - Nacidos 2011-2010</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-danger me-2"></i> <strong>Juvenil</strong> (17-18 años)</li>
                            </ul>
                            <div class="mt-3 p-3 bg-light rounded">
                                <small><strong>Rueda máxima:</strong> RIN 700 | <strong>Pedales:</strong> Automáticos</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-left">
                        <div class="p-4 bg-white rounded-4 h-100 shadow-sm">
                            <h3 class="text-danger mb-4"><i class="fas fa-bicycle me-2"></i> Categorías 1 Día (Exhibición)</h3>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check-circle text-danger me-2"></i> <strong>Compota Strider</strong> (3-4 años)</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-danger me-2"></i> <strong>Compota Pedales</strong> (3-4 años)</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-danger me-2"></i> <strong>Iniciación "A"</strong> (5-6 años) - Nacidos 2021-2020</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-danger me-2"></i> <strong>Iniciación "B"</strong> (7-8 años) - Nacidos 2019-2018</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-danger me-2"></i> <strong>Exhibición</strong> (9-10 años) - Nacidos 2017-2016</li>
                            </ul>
                            <div class="mt-3 p-3 bg-light rounded">
                                <small><strong>Rueda máxima:</strong> RIN 21 (Iniciación) | <strong>Pedales:</strong> Plataforma o Calapié</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Programa Section - Etapas -->
        <section id="programa" class="section section-dark">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>ETAPAS</h2>
                    <p>Recorrido y programación</p>
                </div>
                <div class="schedule-timeline">
                    <div class="schedule-item" data-aos="fade-up">
                        <div class="schedule-time">Jueves 11/06</div>
                        <div class="schedule-event">2:00 pm - Revisión de licencias, documentos y uniformes (Sede Osorio Group)</div>
                    </div>
                    <div class="schedule-item" data-aos="fade-up">
                        <div class="schedule-time">Viernes 12/06 - 8:00 am</div>
                        <div class="schedule-event">1ra Etapa: Contrarreloj Individual (Pre-Infantil, Infantil, Pre-Juvenil, Juvenil) - Distancia 5.1 km</div>
                    </div>
                    <div class="schedule-item" data-aos="fade-up">
                        <div class="schedule-time">Sábado 13/06 - 9:00 am</div>
                        <div class="schedule-event">2da Etapa: Ruta La Fría - Umuquena (Llegada en Alto) - Distancias variables por categoría</div>
                    </div>
                    <div class="schedule-item" data-aos="fade-up">
                        <div class="schedule-time">Domingo 14/06 - 8:00 am</div>
                        <div class="schedule-event">3ra Etapa: Circuito Cerrado La Fría (Sprint Masivo) - Av. Aeropuerto - Autopista</div>
                    </div>
                    <div class="schedule-item" data-aos="fade-up">
                        <div class="schedule-time">Domingo 14/06 - 12:00 m</div>
                        <div class="schedule-event">Prueba en Línea: Categorías Exhibición (Circuito Corto 900m por vuelta)</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Distancias por Categoría Section -->
        <section class="section">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>DISTANCIAS POR CATEGORÍA</h2>
                    <p>Recorridos según categoría</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4" data-aos="fade-up">
                        <div class="route-card">
                            <i class="fas fa-road"></i>
                            <h4>Pre-Infantil</h4>
                            <p>1ra Etapa: 5.1 km (Ruta Grupo)<br>2da Etapa: 12.5 km<br>3ra Etapa: 16.4 km (M) / 8.2 km (F)</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="route-card">
                            <i class="fas fa-road"></i>
                            <h4>Infantil</h4>
                            <p>1ra Etapa: 5.1 km (CRI)<br>2da Etapa: 21.7 km<br>3ra Etapa: 24.6 km (M) / 16.4 km (F)</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="route-card">
                            <i class="fas fa-road"></i>
                            <h4>Pre-Juvenil</h4>
                            <p>1ra Etapa: 5.1 km (CRI)<br>2da Etapa: 36.8 km (F) / 55.6 km (M)<br>3ra Etapa: 24.6 km (F) / 41 km (M)</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="route-card">
                            <i class="fas fa-road"></i>
                            <h4>Juvenil</h4>
                            <p>1ra Etapa: 5.1 km (CRI)<br>2da Etapa: 51.9 km (F) / 70.7 km (M)<br>3ra Etapa: 41 km (F) / 57.4 km (M)</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="400">
                        <div class="route-card">
                            <i class="fas fa-road"></i>
                            <h4>Exhibición</h4>
                            <p>Domingo 14/06: 7.2 km (Circuito Corto 900m)</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="500">
                        <div class="route-card">
                            <i class="fas fa-road"></i>
                            <h4>Iniciación A y B</h4>
                            <p>Iniciación "A": 1.8 km<br>Iniciación "B": 3.6 km</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Clasificaciones Section -->
        <section class="section section-dark">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>CLASIFICACIONES Y PREMIOS</h2>
                    <p>Maillots y distinciones</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="text-center p-4">
                            <div class="bg-warning rounded-circle p-3 d-inline-block mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-tshirt fa-3x text-dark" style="margin-left: -6px;"></i>
                            </div>
                            <h4>Maillot Amarillo</h4>
                            <p>Clasificación General Individual por Tiempos</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="text-center p-4">
                            <div class="bg-success rounded-circle p-3 d-inline-block mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-mountain fa-3x text-white"></i>
                            </div>
                            <h4>Maillot Verde Esmeralda</h4>
                            <p>Clasificación General Premios de Montaña</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="text-center p-4">
                            <div class="bg-danger rounded-circle p-3 d-inline-block mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-tachometer-alt fa-3x text-white"></i>
                            </div>
                            <h4>Maillot Rojo</h4>
                            <p>Clasificación General Sprint</p>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12" data-aos="fade-up">
                        <div class="p-4 bg-white rounded-4">
                            <h4 class="text-danger text-center mb-4">Premio Especial - Mejor Escuela o Club</h4>
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded">
                                        <h5>🥇 1er Lugar</h5>
                                        <p>Moto Forza Nova 150<br>+ Trofeo</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded">
                                        <h5>🥈 2do Lugar</h5>
                                        <p>Kit Material Deportivo<br>+ Trofeo</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded">
                                        <h5>🥉 3er Lugar</h5>
                                        <p>Kit Material Deportivo<br>+ Trofeo</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Inscripción Section -->
        <section id="preinscripcion" class="section">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>INSCRIPCIONES</h2>
                    <p>Preinscripción del 25/05 al 08/06/2026</p>
                </div>
                <div class="row g-4 justify-content-center">
                    <div class="col-lg-4 col-md-6" data-aos="fade-right">
                        <div class="price-card">
                            <div class="price-header" style="background: #00ecfe;">
                                <h3>3 Etapas</h3>
                            </div>
                            <div class="price-body">
                                <div class="price">$30</div>
                                <hr>
                                <p><i class="fas fa-check-circle text-success me-2"></i> Pre-Infantil</p>
                                <p><i class="fas fa-check-circle text-success me-2"></i> Infantil</p>
                                <p><i class="fas fa-check-circle text-success me-2"></i> Pre-Juvenil</p>
                                <p><i class="fas fa-check-circle text-success me-2"></i> Juvenil</p>
                                <p><i class="fas fa-check-circle text-success me-2"></i> 3 días de competencia</p>
                                <p><i class="fas fa-check-circle text-success me-2"></i> Premiación por etapa y general</p>
                                <a href="#" class="btn-custom mt-3 d-inline-block">Preinscribirme</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6" data-aos="fade-left">
                        <div class="price-card featured">
                            <div class="price-header" style="background: #00ecfe;">
                                <h3>1 Día</h3>
                            </div>
                            <div class="price-body">
                                <div class="price">$15</div>
                                <hr>
                                <p><i class="fas fa-check-circle text-success me-2"></i> Iniciación "A"</p>
                                <p><i class="fas fa-check-circle text-success me-2"></i> Iniciación "B"</p>
                                <p><i class="fas fa-check-circle text-success me-2"></i> Exhibición</p>
                                <p><i class="fas fa-check-circle text-success me-2"></i> Compota Strider</p>
                                <p><i class="fas fa-check-circle text-success me-2"></i> Compota Pedales</p>
                                <p><i class="fas fa-check-circle text-success me-2"></i> Domingo 14/06</p>
                                <a href="#" class="btn-custom mt-3 d-inline-block">Preinscribirme</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-5" data-aos="fade-up">
                    <p class="text-muted">Pago a tasa oficial BCV. Para más información contactar a Jhoana Noriega: +58 424-7371101</p>
                    <p class="text-muted">Forma de pago: Cuentas bancarias disponibles al solicitar planilla de preinscripción</p>
                </div>
            </div>
        </section>

        <!-- Contacto Section -->
        <section id="contacto" class="section section-dark">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>CONTACTO</h2>
                    <p>Comité Organizador</p>
                </div>
                <div class="row g-5">
                    <div class="col-lg-6" data-aos="fade-right">
                        <div class="contact-info">
                            <i class="fas fa-user-tie"></i>
                            <div>
                                <strong>Rubén Osorio Espinoza</strong><br>
                                Dirección General de la Carrera
                            </div>
                        </div>
                        <div class="contact-info">
                            <i class="fas fa-wrench"></i>
                            <div>
                                <strong>Valentín Durán</strong><br>
                                Dirección Técnica - +58 412-6851119
                            </div>
                        </div>
                        <div class="contact-info">
                            <i class="fas fa-file-alt"></i>
                            <div>
                                <strong>Gustavo Uzcátegui Rosales</strong><br>
                                Secretario General - +58 424-7038594
                            </div>
                        </div>
                        <div class="contact-info">
                            <i class="fas fa-user-check"></i>
                            <div>
                                <strong>Jhoana Noriega</strong><br>
                                Coordinadora de Preinscripción - +58 424-7371101
                            </div>
                        </div>
                        <div class="contact-info">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong>Email</strong><br>
                                vueltalafria@gmail.com
                            </div>
                        </div>
                        <div class="contact-info">
                            <i class="fas fa-flag-checkered"></i>
                            <div>
                                <strong>Sede Principal</strong><br>
                                Osorio Group - Av. Aeropuerto, vía autopista La Fría - San Cristóbal
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-left">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.0!2d-72.25!3d8.2167!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e5f5e5e5e5e5e5e%3A0x5e5e5e5e5e5e5e5e!2sLa%20Fria%2C%20Tachira!5e0!3m2!1sen!2sve!4v1700000000000!5m2!1sen!2sve" width="100%" height="350" style="border:0; border-radius:15px;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
                <div class="row mt-5" data-aos="fade-up">
                    <div class="col-lg-8 mx-auto">
                        <div class="contact-form">
                            <h3 class="text-center mb-4">Contáctanos</h3>
                            <form>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" placeholder="Tu nombre">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control" placeholder="Tu email">
                                    </div>
                                    <div class="col-12">
                                        <textarea class="form-control" rows="5" placeholder="Tu mensaje"></textarea>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn-custom">Enviar mensaje</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endsection
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @section('scripts')
        @section('scripts')
            <script>
                // Inicializar Swiper para testimonials
                const testimonialSwiper = new Swiper('.testimonial-swiper', {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    loop: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    breakpoints: {
                        0: {
                            slidesPerView: 1,
                            spaceBetween: 15,
                        },
                        640: {
                            slidesPerView: 1,
                            spaceBetween: 20,
                        },
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 25,
                        },
                        992: {
                            slidesPerView: 3,
                            spaceBetween: 30,
                        },
                    },
                    // Asegurar que las tarjetas tengan altura uniforme
                    autoHeight: false,
                    // Permitir que el wrapper use flex
                    setWrapperSize: true,
                });

                // Forzar actualización después de cargar
                window.addEventListener('load', function() {
                    testimonialSwiper.update();
                });
            </script>
        @endsection
    @endsection
@endsection
