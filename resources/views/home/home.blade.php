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

        .btn-inscription-type.active {
            background: linear-gradient(135deg, #00ecfe 0%, #00c4d4 100%);
            border-color: #00ecfe;
        }

        .btn-inscription-type.active i,
        .btn-inscription-type.active h4,
        .btn-inscription-type.active p {
            color: white;
        }

        .form-container {
            transition: all 0.5s ease;
        }

        .form-container.show {
            display: block;
            animation: fadeInUp 0.5s ease;
        }

        .required-field::after {
            content: " *";
            color: red;
        }

        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #fafafa;
        }

        .upload-area:hover {
            border-color: #00ecfe;
            background: #f0f7ff;
        }

        .upload-area.dragover {
            border-color: #00ecfe;
            background: #e8f0fe;
        }

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

        .btn-custom {
            background: #00ecfe;
            color: #000;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            transition: 0.3s;
            display: inline-block;
            border: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .btn-custom:hover {
            background: #00c4d4;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,236,254,0.3);
        }

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

        @keyframes scale {
            0% {
                transform: scale(0);
            }
            100% {
                transform: scale(1);
            }
        }

        /* Estilos para el autocompletado */
        #structureSuggestions .list-group-item {
            cursor: pointer;
            transition: all 0.2s;
            border-left: none;
            border-right: none;
        }

        #structureSuggestions .list-group-item:first-child {
            border-top: none;
        }

        #structureSuggestions .list-group-item:hover {
            background-color: #e8f0fe;
            color: #00ecfe;
        }

        #structureSuggestions .list-group-item.active {
            background-color: #00ecfe;
            border-color: #00ecfe;
            color: white;
        }

        .suggestion-highlight {
            font-weight: bold;
            color: #00ecfe;
        }

        /* Estilos para la sección de precios */
        .price-card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            height: 100%;
        }

        .price-card:hover {
            transform: translateY(-10px);
        }

        .price-card.featured {
            transform: scale(1.02);
            box-shadow: 0 20px 50px rgba(0,236,254,0.2);
        }

        .price-card .price-header {
            padding: 30px;
            text-align: center;
            color: white;
        }

        .price-card .price-header h3 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }

        .price-card .price-body {
            padding: 30px;
            background: white;
        }

        .price-card .price {
            font-size: 3rem;
            font-weight: 900;
            color: var(--primary);
            text-align: center;
        }

        .price-card .price small {
            font-size: 1rem;
            font-weight: 400;
        }

        .bank-card {
            background: #f8f9fa;
            border-left: 4px solid #00ecfe;
            transition: all 0.3s;
        }

        .bank-card:hover {
            background: #ffffff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .price-card.featured {
                transform: scale(1);
            }
        }
    </style>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero" id="inicio" style="text-align: left !important;">
        <div class="container text-center text-white">
            <div data-aos="fade-up" style="text-align: left !important;">
                <p class="fecha" style="font-size: 30px; padding: 20px 0px">12 - 14 JUNIO 2026</p>
                <h1 style="margin:0px;">VUELTA A LA <br><span>FRÍA</span></h1>
                <p class="subtitulo" style="font-size: 20px;">LA VUELTA MENOR MÁS IMPORTANTE DE VENEZUELA</p>
                <div>
                    <a href="#pricing" class="btn-custom">Ver Costos</a>
                    <a href="#preinscripcion" class="btn-custom btn-outline-custom">Inscribirme Ahora</a>
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
                <p>La primera edición fué en 1992</p>
            </div>
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right" style="margin-top: 10px">
                    <img src="/images/home1.jpg" style="border-radius: 6px;" alt="Ciclismo Menor" class="about-img">
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <p>La Vuelta Menor a La Fría es un evento de ruta por etapas que se realizará del 11 al 14 de junio de 2026 en el municipio García de Hevia, estado Táchira, Venezuela.</p>
                    <p>El evento está regido por el reglamento de la Unión Ciclista Internacional (UCI) y la Federación Venezolana de Ciclismo (FVC). La participación es abierta para ciclistas independientes y equipos organizados como clubes y escuelas, mediante invitación para las categorías menores desde Strider hasta Juvenil.</p>
                    <p class="mt-3">La carrera cuenta con el aval de la Asociación Tachirense de Ciclismo, la Comisión Nacional de Ciclismo Menor y Juvenil, la Federación Venezolana de Ciclismo (FVC), el Instituto Municipal de Deporte (IMDERE) y el Instituto del Deporte Tachirense (IDT).</p>
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-sm-4 text-center">
                                <i class="fas fa-trophy text-danger fs-1"></i>
                                <p class="fw-bold mt-2">Clase Mundial</p>
                            </div>
                            <div class="col-sm-4 text-center">
                                <i class="fas fa-mountain text-danger fs-1"></i>
                                <p class="fw-bold mt-2">Paisajes Épicos</p>
                            </div>
                            <div class="col-sm-4 text-center">
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
                        <div class="stat-number">3</div>
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
                            <li class="mb-2"><i class="fas fa-check-circle text-danger me-2"></i> <strong>Juvenil</strong> (17-18 años) - Nacidos 2009-2008</li>
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
                            <li class="mb-2"><i class="fas fa-check-circle text-danger me-2"></i> <strong>Compota Strider</strong> (3-4 años - Nacidos 2023-2022)</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-danger me-2"></i> <strong>Compota Pedales</strong> (3-4 años - Nacidos 2023-2022)</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-danger me-2"></i> <strong>Iniciación "A"</strong> (5-6 años) - Nacidos 2021-2020</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-danger me-2"></i> <strong>Iniciación "B"</strong> (7-8 años) - Nacidos 2019-2018</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-danger me-2"></i> <strong>Iniciación "C"</strong> (9-10 años) - Nacidos 2017-2016</li>
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
                    <div class="schedule-event">2:00 pm - Revisión de licencias, documentos y uniformes (Sede Osorio Group Av. Aeropuerto La Fria)</div>
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
                        <p>Strider<br>Compota</p>
                        <p>Domingo 14/06: 100mts</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="route-card">
                        <i class="fas fa-road"></i>
                        <h4>Iniciación A, B y C</h4>
                        <p>Iniciación "A": 1.8 km<br>Iniciación "B": 3.6 km<br>Iniciación "C": 7.2 km</p>
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

    <!-- ============================================ -->
    <!-- SECCIÓN DE PRECIOS - PRICING CON CUENTAS BANCARIAS -->
    <!-- ============================================ -->
    <section id="pricing" class="section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>COSTOS DE INSCRIPCIÓN</h2>
                <p>Elige tu categoría y conoce el costo</p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Tarjeta de 3 Días -->
                <div class="col-lg-5 col-md-6" data-aos="fade-right" data-aos-delay="100">
                    <div class="price-card featured">
                        <div class="price-header" style="background: linear-gradient(135deg, #00ecfe 0%, #00c4d4 100%);">
                            <h3>🚴‍♂️ 3 DÍAS DE COMPETENCIA</h3>
                            <p class="mb-0 text-white-50">Viernes 12, Sábado 13 y Domingo 14 de junio</p>
                        </div>
                        <div class="price-body">
                            <div class="price">$30 <small>USD</small></div>
                            <hr>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Pre-Infantil (11-12 años)</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Infantil (13-14 años)</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Pre-Juvenil (15-16 años)</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Juvenil (17-18 años)</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> 3 etapas completas</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Premiación por etapa y general</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Maillot conmemorativo</p>
                            <div class="mt-4">
                                <a href="#preinscripcion" class="btn-custom" onclick="selectRegistrationType('individual')">
                                    <i class="fas fa-user me-2"></i> Inscribirme Individual
                                </a>
                                <a href="#preinscripcion" class="btn-outline-custom mt-2 d-inline-block" onclick="selectRegistrationType('team')">
                                    <i class="fas fa-users me-2"></i> Inscribir Equipo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tarjeta de 1 Día -->
                <div class="col-lg-5 col-md-6" data-aos="fade-left" data-aos-delay="200">
                    <div class="price-card">
                        <div class="price-header" style="background: linear-gradient(135deg, #00a3b3 0%, #008a99 100%);">
                            <h3>🚲 1 DÍA DE COMPETENCIA</h3>
                            <p class="mb-0 text-white-50">Domingo 14 de junio - Exhibición</p>
                        </div>
                        <div class="price-body">
                            <div class="price">$15 <small>USD</small></div>
                            <hr>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Compota Strider (3-4 años)</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Compota Pedales (3-4 años)</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Iniciación A (5-6 años)</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Iniciación B (7-8 años)</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Iniciación C (9-10 años)</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Medalla de participación</p>
                            <div class="mt-4">
                                <a href="#preinscripcion" class="btn-custom" onclick="selectRegistrationType('individual')">
                                    <i class="fas fa-user me-2"></i> Inscribirme Individual
                                </a>
                                <a href="#preinscripcion" class="btn-outline-custom mt-2 d-inline-block" onclick="selectRegistrationType('team')">
                                    <i class="fas fa-users me-2"></i> Inscribir Equipo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nota de Staff -->
            <div class="row mt-4" data-aos="fade-up">
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-users me-2"></i>
                        <strong>Personal de apoyo (Staff):</strong> Sin costo de inscripción
                        <br><small>Entrenadores, médicos, mecánicos y acompañantes no pagan inscripción</small>
                    </div>
                </div>
            </div>

            <!-- CUENTAS BANCARIAS -->
            <div class="row mt-4 d-none" data-aos="fade-up">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white text-center py-3">
                            <h4 class="mb-0" style="color: var(--primary);">
                                <i class="fas fa-university me-2"></i> Cuentas Bancarias para el Pago
                            </h4>
                            <p class="text-muted mt-2 mb-0">Realiza el pago a nombre de: <strong>Vuelta a la Fría 2026</strong></p>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="bank-card p-3 rounded">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-university fa-2x me-3" style="color: #00ecfe;"></i>
                                            <h5 class="mb-0">Banco Provincial</h5>
                                        </div>
                                        <p class="mb-1"><strong>Tipo de Cuenta:</strong> Corriente</p>
                                        <p class="mb-1"><strong>Número de Cuenta:</strong>0108-0133-8001-0004-2510</p>
                                        <p class="mb-1"><strong>Cédula/RIF:</strong> V-15184480</p>
                                        <p class="mb-0"><strong>Beneficiario:</strong> Ruben Osorio</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="bank-card p-3 rounded">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-university fa-2x me-3" style="color: #00ecfe;"></i>
                                            <h5 class="mb-0">Transferencia Pesos COP</h5>
                                        </div>
                                        <p class="mb-1"><strong>Banco:</strong> Bancolombia</p>
                                        <p class="mb-1"><strong>Cuenta:</strong> Ahorro</p>
                                        <p class="mb-1"><strong>Nro:</strong>901275648</p>
                                        <p class="mb-0"><strong>Beneficiario:</strong> INVERSIONES OSORIO MOTOS S.A.S</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="bank-card p-3 rounded">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-university fa-2x me-3" style="color: #00ecfe;"></i>
                                            <h5 class="mb-0">Pagos En USDT</h5>
                                        </div>
                                        <p class="mb-1"><strong>Correo:</strong>  Rubenaosorioe@gmail.com</p>

                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Importante:</strong> Luego de realizar el pago, sube tu comprobante en el formulario de inscripción.
                                Tu inscripción será confirmada en un plazo máximo de 48 horas.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección de Inscripción -->
    <section id="preinscripcion" class="section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>FORMULARIO DE INSCRIPCIÓN</h2>
                <p>Completa el formulario para participar en la Vuelta a la Fría 2026</p>
            </div>

            <!-- Selección de tipo de inscripción -->
            <div class="row justify-content-center mb-5" data-aos="fade-up">
                <div class="col-md-8">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button type="button" class="btn-inscription-type w-100" id="btnIndividual" onclick="showIndividualForm()">
                                <i class="fas fa-user fa-2x mb-2"></i>
                                <h4 class="mb-1">Inscripción Individual</h4>
                                <p class="mb-0 small">Para ciclistas independientes</p>
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn-inscription-type w-100" id="btnTeam" onclick="showTeamForm()">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <h4 class="mb-1">Inscripción por Equipos</h4>
                                <p class="mb-0 small">Para clubes, escuelas y equipos</p>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORMULARIO INDIVIDUAL -->
            <div id="individualForm" class="form-container" style="display: {{ session('form_error') == 'individual' ? 'block' : 'none' }};">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <i class="fas fa-bicycle fa-3x" style="color: #00ecfe;"></i>
                                    <h4 class="mt-3">Inscripción Individual</h4>
                                    <p class="text-muted">Completa el formulario para inscribirte como ciclista independiente</p>
                                </div>

                                @if(session('individual_success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle me-2"></i> {{ session('individual_success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if($errors->any() && session('form_error') == 'individual')
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong><i class="fas fa-exclamation-triangle me-2"></i> Por favor, corrige los siguientes errores:</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <form action="{{ route('registration.individual.submit') }}" method="POST" id="individualFormSubmit">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required-field">Nombres</label>
                                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" required value="{{ old('first_name') }}">
                                            @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required-field">Apellidos</label>
                                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" required value="{{ old('last_name') }}">
                                            @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required-field">Email</label>
                                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" required value="{{ old('email') }}">
                                            @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required-field">Teléfono/WhatsApp</label>
                                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" required value="{{ old('phone') }}">
                                            @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required-field">Género</label>
                                            <select name="gender" id="genderSelect" class="form-select @error('gender') is-invalid @enderror" required>
                                                <option value="">Seleccionar</option>
                                                <option value="Masculino" {{ old('gender') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                                <option value="Femenino" {{ old('gender') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                            </select>
                                            @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label required-field">Fecha de Nacimiento</label>
                                            <input type="date" name="birth_date" id="birth_date" class="form-control @error('birth_date') is-invalid @enderror" required value="{{ old('birth_date') }}">
                                            <small id="ageDisplay" class="text-muted"></small>
                                            @error('birth_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Estructura -->
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">¿Representas a alguna Estructura?</label>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="has_structure" id="structure_no" value="0" checked onchange="toggleStructureField()">
                                                <label class="form-check-label" for="structure_no">No, soy ciclista independiente</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="has_structure" id="structure_yes" value="1" onchange="toggleStructureField()">
                                                <label class="form-check-label" for="structure_yes">Sí, represento a una Escuela/Club/Fundación/Sponsor</label>
                                            </div>
                                        </div>

                                        <div id="structureError" class="alert alert-danger mt-2" style="display: none; font-size: 0.9rem;">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <span id="structureErrorMessage"></span>
                                        </div>

                                        <div class="col-md-12 mb-3" id="structureField" style="display: none;">
                                            <label class="form-label">Nombre de la Escuela/Club/Fundación/Sponsor</label>
                                            <div class="position-relative">
                                                <input type="text" id="structure_search" class="form-control" placeholder="Escribe el nombre de la estructura..." autocomplete="off">
                                                <input type="hidden" name="structure_id" id="structure_id" value="{{ old('structure_id') }}">
                                                <input type="hidden" name="structure_name" id="structure_name_hidden" value="{{ old('structure_name') }}">
                                                <div id="structureSuggestions" class="list-group position-absolute w-100" style="display: none; z-index: 1000; max-height: 300px; overflow-y: auto; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);"></div>
                                            </div>
                                            <div class="form-check mt-2" id="newStructureOption" style="display: none;">
                                                <input class="form-check-input" type="checkbox" id="createNewStructure">
                                                <label class="form-check-label" for="createNewStructure">
                                                    <i class="fas fa-plus-circle text-success"></i> Crear Escuela/Club/Fundación/Sponsor "<span id="newStructureName"></span>"
                                                </label>
                                            </div>
                                            <small class="text-muted">Ingresa el nombre de la escuela, club, fundación o sponsor que representas</small>
                                            <div id="selectedStructureInfo" class="mt-2"></div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label required-field">Categoría</label>
                                            <select name="category" id="categorySelect" class="form-select @error('category') is-invalid @enderror" required>
                                                <option value="">Seleccionar categoría</option>
                                                <optgroup label="3 Etapas (Viernes a Domingo)">
                                                    <option value="Pre-Infantil" {{ old('category') == 'Pre-Infantil' ? 'selected' : '' }}>Pre-Infantil (11-12 años)</option>
                                                    <option value="Infantil" {{ old('category') == 'Infantil' ? 'selected' : '' }}>Infantil (13-14 años)</option>
                                                    <option value="Pre-Juvenil" {{ old('category') == 'Pre-Juvenil' ? 'selected' : '' }}>Pre-Juvenil (15-16 años)</option>
                                                    <option value="Juvenil" {{ old('category') == 'Juvenil' ? 'selected' : '' }}>Juvenil (17-18 años)</option>
                                                </optgroup>
                                                <optgroup label="1 Día (Domingo) - Exhibición">
                                                    <option value="Iniciación A" {{ old('category') == 'Iniciación A' ? 'selected' : '' }}>Iniciación A (5-6 años)</option>
                                                    <option value="Iniciación B" {{ old('category') == 'Iniciación B' ? 'selected' : '' }}>Iniciación B (7-8 años)</option>
                                                    <option value="Iniciación C" {{ old('category') == 'Iniciación C' ? 'selected' : '' }}>Iniciación C (9-10 años)</option>
                                                    <option value="Compota Strider" {{ old('category') == 'Compota Strider' ? 'selected' : '' }}>Compota Strider (3-4 años)</option>
                                                    <option value="Compota Pedales" {{ old('category') == 'Compota Pedales' ? 'selected' : '' }}>Compota Pedales (3-4 años)</option>
                                                </optgroup>
                                            </select>
                                            <div id="ageError" class="alert alert-warning mt-2" style="display: none; font-size: 0.9rem;"></div>
                                            @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label required-field">Contacto de Emergencia</label>
                                            <input type="text" name="emergency_contact" class="form-control @error('emergency_contact') is-invalid @enderror" required placeholder="Nombre completo y teléfono de contacto" value="{{ old('emergency_contact') }}">
                                            @error('emergency_contact')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Ej: Juan Pérez - 0414XXXXXX</small>
                                        </div>

                                        <div class="col-12 mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input @error('accept_terms') is-invalid @enderror" id="acceptTermsIndividual" name="accept_terms" required>
                                                <label class="form-check-label" for="acceptTermsIndividual">
                                                    Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">términos y condiciones</a> de la competencia
                                                </label>
                                                @error('accept_terms')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 text-center">
                                            <div class="g-recaptcha mb-3" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                                            <button type="submit" class="btn-custom">Enviar Inscripción</button>
                                            <button type="button" class="btn-outline-custom ms-2" onclick="hideForms()">Cancelar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORMULARIO POR EQUIPOS -->
            <div id="teamForm" class="form-container" style="display: {{ session('form_error') == 'team' ? 'block' : 'none' }};">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-4">
                                <div class="text-center mb-4">
                                    <i class="fas fa-users fa-3x" style="color: #00ecfe;"></i>
                                    <h4 class="mt-3">Inscripción por Equipos</h4>
                                    <p class="text-muted">Descarga la plantilla, completa los datos de tus atletas y súbela</p>
                                </div>

                                @if(session('team_success'))
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-2"></i> {{ session('team_success') }}
                                        <br>
                                        <strong>📊 Resumen:</strong> {{ session('athletes_count') }} atletas, {{ session('staff_count') }} personal de apoyo
                                        <br>
                                        <strong>Código de acceso:</strong> <code>{{ session('access_code') }}</code>
                                        <br>
                                        <a href="{{ route('team.login') }}" class="btn btn-sm btn-primary mt-2">Acceder al Panel</a>
                                    </div>
                                @endif

                                @if(session('import_errors'))
                                    <div class="alert alert-danger">
                                        <strong>Errores en el archivo:</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach(session('import_errors') as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('registration.team.submit') }}" method="POST" enctype="multipart/form-data" id="teamRegistrationForm">
                                    @csrf

                                    <div class="bg-light p-3 rounded mb-4">
                                        <h5 class="mb-3" style="color: var(--primary);"><i class="fas fa-building me-2"></i> Datos del Equipo</h5>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Nombre del Equipo/Club</label>
                                                <input type="text" name="team_name" class="form-control" required value="{{ old('team_name') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">País de origen</label>
                                                <select name="team_country" class="form-select" required>
                                                    <option value="">Seleccionar</option>
                                                    <option value="Venezuela">Venezuela</option>
                                                    <option value="Colombia">Colombia</option>
                                                    <option value="Ecuador">Ecuador</option>
                                                    <option value="Perú">Perú</option>
                                                    <option value="Panamá">Panamá</option>
                                                    <option value="Costa Rica">Costa Rica</option>
                                                    <option value="Chile">Chile</option>
                                                    <option value="Argentina">Argentina</option>
                                                    <option value="México">México</option>
                                                    <option value="Estados Unidos">Estados Unidos</option>
                                                    <option value="España">España</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Ciudad/Estado</label>
                                                <input type="text" name="team_city" class="form-control" value="{{ old('team_city') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Región</label>
                                                <input type="text" name="region" class="form-control" value="{{ old('region') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-light p-3 rounded mb-4">
                                        <h5 class="mb-3" style="color: var(--primary);"><i class="fas fa-user-tie me-2"></i> Datos del Delegado/Entrenador</h5>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Nombre Completo</label>
                                                <input type="text" name="delegate_name" class="form-control" required value="{{ old('delegate_name') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Email</label>
                                                <input type="email" name="delegate_email" class="form-control" required value="{{ old('delegate_email') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Teléfono/WhatsApp</label>
                                                <input type="text" name="delegate_phone" class="form-control" required value="{{ old('delegate_phone') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">WhatsApp (alternativo)</label>
                                                <input type="text" name="delegate_whatsapp" class="form-control" value="{{ old('delegate_whatsapp') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-light p-3 rounded mb-4">
                                        <h5 class="mb-3" style="color: var(--primary);">
                                            <i class="fas fa-passport me-2"></i> Datos Migratorios
                                            <small class="text-muted fw-light">(Para equipos internacionales)</small>
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Fecha de arribo a la frontera</label>
                                                <input type="date" name="arrival_date" class="form-control" value="{{ old('arrival_date') }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Frontera de entrada</label>
                                                <input type="text" name="arrival_border" class="form-control" placeholder="Ej: San Antonio/Táchira" value="{{ old('arrival_border') }}">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Medio de transporte</label>
                                                <select name="transport_type" class="form-select">
                                                    <option value="">Seleccionar</option>
                                                    <option value="Aéreo">Aéreo</option>
                                                    <option value="Terrestre">Terrestre</option>
                                                    <option value="Marítimo">Marítimo</option>
                                                    <option value="Mixto">Mixto</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Fecha de retorno</label>
                                                <input type="date" name="return_date" class="form-control" value="{{ old('return_date') }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Hora de vuelo (si aplica)</label>
                                                <input type="time" name="return_flight_time" class="form-control" value="{{ old('return_flight_time') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-light p-3 rounded mb-4">
                                        <h5 class="mb-3" style="color: var(--primary);">
                                            <i class="fas fa-truck me-2"></i> Vehículos de la Delegación
                                            <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="addVehicleRow()">
                                                <i class="fas fa-plus"></i> Agregar
                                            </button>
                                        </h5>
                                        <div class="table-responsive">
                                            <table class="table table-sm" id="vehiclesTable">
                                                <thead>
                                                <tr>
                                                    <th>Marca</th>
                                                    <th>Modelo</th>
                                                    <th>Matrícula/Placa</th>
                                                    <th>Año</th>
                                                    <th>Color</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody id="vehiclesBody">
                                                <tr>
                                                    <td><input type="text" name="vehicles[0][brand]" class="form-control form-control-sm" placeholder="Marca"></td>
                                                    <td><input type="text" name="vehicles[0][model]" class="form-control form-control-sm" placeholder="Modelo"></td>
                                                    <td><input type="text" name="vehicles[0][plate]" class="form-control form-control-sm" placeholder="Placa"></td>
                                                    <td><input type="number" name="vehicles[0][year]" class="form-control form-control-sm" placeholder="Año"></td>
                                                    <td><input type="text" name="vehicles[0][color]" class="form-control form-control-sm" placeholder="Color"></td>
                                                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeVehicleRow(this)"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="bg-light p-3 rounded mb-4">
                                        <h5 class="mb-3" style="color: var(--primary);"><i class="fas fa-file-excel me-2"></i> Lista de Atletas</h5>
                                        <div class="alert alert-info">
                                            <i class="fas fa-download me-2"></i>
                                            <strong>Descarga la plantilla:</strong>
                                            <a href="{{ route('registration.team.download-template') }}" class="btn btn-sm btn-primary ms-2">
                                                <i class="fas fa-download"></i> Descargar Plantilla Excel
                                            </a>
                                            <hr class="my-2">
                                            <small class="d-block">La plantilla debe contener: ID, NOMBRES, APELLIDOS, FECHA_DE_NACIMIENTO, TIPO_DOCUMENTO, NÚMERO_DOCUMENTO, UCI ID, CATEGORÍA, GÉNERO, ROL</small>
                                        </div>
                                        <div class="upload-area" id="uploadArea">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <p>Arrastra tu archivo aquí o haz clic para seleccionar</p>
                                            <input type="file" name="excel_file" id="excelFile" accept=".xlsx,.xls,.csv" style="display: none;" required>
                                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('excelFile').click()">Seleccionar archivo</button>
                                            <div id="fileName" class="mt-2 small text-muted"></div>
                                        </div>
                                        <small class="text-muted">Formatos aceptados: .xlsx, .xls, .csv (máx 5MB)</small>
                                    </div>

                                    <!-- SECCIÓN DE PAGO -->
                                    <div class="bg-light p-3 rounded mb-4">
                                        <h5 class="mb-3" style="color: var(--primary);">
                                            <i class="fas fa-credit-card me-2"></i> Información de Pago
                                        </h5>

                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <div class="alert alert-info">
                                                    <strong>💰 Costos de inscripción:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        <li>🚴‍♂️ <strong>Categorías 3 días</strong> (Pre-Infantil, Infantil, Pre-Juvenil, Juvenil): <strong class="text-success">$30 USD</strong> por atleta</li>
                                                        <li>🚲 <strong>Categorías 1 día</strong> (Iniciación A, B, C y Compotas): <strong class="text-success">$15 USD</strong> por atleta</li>
                                                        <li>👥 <strong>Staff/Personal de apoyo</strong>: Sin costo</li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Monto Total a Pagar (USD)</label>
                                                <input type="number" name="amount" id="total_amount" class="form-control" step="0.01" readonly required
                                                       value="0" style="background-color: #e8f0fe; font-weight: bold; font-size: 1.2rem;">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Método de Pago</label>
                                                <select name="payment_method" id="payment_method" class="form-select" required>
                                                    <option value="">Seleccionar método de pago</option>
                                                    <option value="transferencia">Transferencia Bancaria</option>
                                                    <option value="pago_movil">Pago Móvil</option>
                                                    <option value="paypal">PayPal</option>
                                                    <option value="zelle">Zelle</option>
                                                    <option value="efectivo">Efectivo (el día del evento)</option>
                                                </select>
                                            </div>

                                            <div class="col-md-12 mb-3" id="bankAccounts" style="display: none;">
                                                <div class="card bg-white">
                                                    <div class="card-body">
                                                        <h6 class="card-title"><i class="fas fa-university me-2"></i> Cuentas Bancarias Disponibles</h6>
                                                        <div class="row g-3">
                                                            <div class="col-md-4">
                                                                <div class="bank-card p-3 rounded">
                                                                    <div class="d-flex align-items-center mb-2">
                                                                        <i class="fas fa-university fa-2x me-3" style="color: #00ecfe;"></i>
                                                                        <h5 class="mb-0">Banco Provincial</h5>
                                                                    </div>
                                                                    <p class="mb-1"><strong>Tipo de Cuenta:</strong> Corriente</p>
                                                                    <p class="mb-1"><strong>Número de Cuenta:</strong>0108-0133-8001-0004-2510</p>
                                                                    <p class="mb-1"><strong>Cédula/RIF:</strong> V-15184480</p>
                                                                    <p class="mb-0"><strong>Beneficiario:</strong> Ruben Osorio</p>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="bank-card p-3 rounded">
                                                                    <div class="d-flex align-items-center mb-2">
                                                                        <i class="fas fa-university fa-2x me-3" style="color: #00ecfe;"></i>
                                                                        <h5 class="mb-0">Transferencia Pesos COP</h5>
                                                                    </div>
                                                                    <p class="mb-1"><strong>Banco:</strong> Bancolombia</p>
                                                                    <p class="mb-1"><strong>Cuenta:</strong> Ahorro</p>
                                                                    <p class="mb-1"><strong>Nro:</strong>901275648</p>
                                                                    <p class="mb-0"><strong>Beneficiario:</strong> INVERSIONES OSORIO MOTOS S.A.S</p>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="bank-card p-3 rounded">
                                                                    <div class="d-flex align-items-center mb-2">
                                                                        <i class="fas fa-university fa-2x me-3" style="color: #00ecfe;"></i>
                                                                        <h5 class="mb-0">Pagos En USDT</h5>
                                                                    </div>
                                                                    <p class="mb-1"><strong>Correo:</strong> Rubenaosorioe@gmail.com</p>

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-3" id="paymentReferenceField" style="display: none;">
                                                <label class="form-label required-field">Número de Referencia/Transacción</label>
                                                <input type="text" name="payment_reference" id="payment_reference" class="form-control"
                                                       placeholder="Ingresa el número de referencia de tu transferencia/pago">
                                            </div>

                                            <!-- CAMPO PARA SUBIR COMPROBANTE -->
                                            <div class="col-md-12 mb-3" id="paymentProofField" style="display: none;">
                                                <label class="form-label">Comprobante de Pago</label>
                                                <div class="upload-area" id="paymentUploadArea" style="padding: 15px;">
                                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                                    <p>Arrastra tu comprobante aquí o haz clic para seleccionar</p>
                                                    <small class="text-muted">Formatos: JPG, PNG, PDF (Máx 2MB)</small>
                                                    <input type="file" name="payment_proof" id="paymentProof" accept=".jpg,.jpeg,.png,.pdf" style="display: none;">
                                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="document.getElementById('paymentProof').click()">
                                                        Seleccionar comprobante
                                                    </button>
                                                    <div id="paymentFileName" class="mt-2 small text-muted"></div>
                                                </div>
                                                <small class="text-muted">El comprobante se comprimirá automáticamente. Formatos permitidos: JPG, PNG, PDF (Máx 2MB)</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-center mb-4">
                                        <div class="g-recaptcha mb-3" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="acceptTermsTeam" name="accept_terms" required>
                                            <label class="form-check-label" for="acceptTermsTeam">
                                                Confirmo que la información proporcionada es verídica y acepto los
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">términos y condiciones</a> de la competencia.
                                            </label>
                                        </div>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn-custom btn-lg">Enviar Inscripción del Equipo</button>
                                        <button type="button" class="btn-outline-custom ms-2" onclick="hideForms()">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5" data-aos="fade-up">
                <p class="text-muted">Pago a tasa oficial BCV. Para más información contactar a Jhoana Noriega: <strong>+58 424-7371101</strong></p>
                <p class="text-muted">Forma de pago: Cuentas bancarias disponibles arriba</p>
            </div>
        </div>
    </section>

    <!-- Modal de Términos -->
    <div class="modal fade" id="termsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Términos y Condiciones - Vuelta a la Fría 2026</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>Requisitos de Participación:</h6>
                    <ul>
                        <li>Todos los participantes deben contar con licencia de la FVC vigente o adquirir licencia de un día en el registro.</li>
                        <li>Los ciclistas menores de 18 años deben presentar autorización firmada por sus representantes legales.</li>
                        <li>Cada equipo es responsable de la seguridad y documentos de sus integrantes durante el traslado y competencia.</li>
                        <li>El uso de casco es OBLIGATORIO durante toda la competencia.</li>
                        <li>Las bicicletas deben cumplir con las normas UCI para competencias de ruta.</li>
                    </ul>
                    <h6 class="mt-3">Política de Reembolsos:</h6>
                    <p>Las inscripciones no son reembolsables, pero pueden ser transferidas a otro participante hasta 3 días antes del evento.</p>
                    <h6 class="mt-3">Protección de Datos:</h6>
                    <p>Los datos proporcionados serán utilizados exclusivamente para fines organizativos y estadísticos de la Vuelta a la Fría 2026.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Éxito -->
    <div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #00ecfe 0%, #00c4d4 100%);">
                    <div class="modal-title text-center w-100">
                        <i class="fas fa-check-circle fa-4x text-white mb-2"></i>
                        <h4 class="text-white mb-0">¡Inscripción Registrada!</h4>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <h5 class="mt-4" id="successMessage"></h5>
                    <div class="alert alert-success mt-3">
                        <table class="table table-borderless mb-0">
                            <tbody id="successDetailsTable"></tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <p class="text-muted">Se ha enviado un correo con los detalles de tu inscripción.</p>
                        <small class="text-muted">Guarda tu número de dorsal para el día del evento.</small>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn-custom" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Estado -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="modal-title text-center w-100">
                        <i class="fas fa-clipboard-list fa-4x text-white mb-2"></i>
                        <h4 class="text-white mb-0">Estado de Inscripción</h4>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="statusContent"></div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn-custom" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

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
                        <div><strong>Rubén Osorio Espinoza</strong><br>Dirección General de la Carrera</div>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-wrench"></i>
                        <div><strong>Valentín Durán</strong><br>Dirección Técnica - +58 412-6851119</div>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-file-alt"></i>
                        <div><strong>Gustavo Uzcátegui Rosales</strong><br>Secretario General - +58 424-7038594</div>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-user-check"></i>
                        <div><strong>Jhoana Noriega</strong><br>Coordinadora de Preinscripción - +58 424-7371101</div>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-envelope"></i>
                        <div><strong>Email</strong><br>vueltalafria@gmail.com</div>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-flag-checkered"></i>
                        <div><strong>Sede Principal</strong><br>Osorio Group - Av. Aeropuerto, vía autopista La Fría - San Cristóbal</div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.0!2d-72.25!3d8.2167!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e5f5e5e5e5e5e5e%3A0x5e5e5e5e5e5e5e5e!2sLa%20Fria%2C%20Tachira!5e0!3m2!1sen!2sve!4v1700000000000!5m2!1sen!2sve" width="100%" height="350" style="border:0; border-radius:15px;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let existingStructureId = null;
        let existingStructureName = null;
        let vehicleIndex = 1;
        let searchTimeout = null;

        // Inicializar Swiper
        const testimonialSwiper = new Swiper('.testimonial-swiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: { delay: 5000, disableOnInteraction: false },
            pagination: { el: '.swiper-pagination', clickable: true },
            breakpoints: {
                0: { slidesPerView: 1 },
                640: { slidesPerView: 1 },
                768: { slidesPerView: 2 },
                992: { slidesPerView: 3 }
            }
        });

        // FUNCIONES DE EDAD Y CATEGORÍA
        function getAgeFromBirthDate(birthDate) {
            if (!birthDate) return null;
            const today = new Date();
            const birth = new Date(birthDate);
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) age--;
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
            if (currentOption && currentOption.disabled) categorySelect.value = '';
        }

        document.getElementById('birth_date')?.addEventListener('change', function() {
            displayAge();
            filterCategoriesByAge();
        });

        // FUNCIONES DE FORMULARIO
        function selectRegistrationType(type) {
            if (type === 'individual') {
                showIndividualForm();
            } else if (type === 'team') {
                showTeamForm();
            }
            setTimeout(() => {
                document.getElementById('preinscripcion').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }

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
                if (individualForm) individualForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
                if (teamForm) teamForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
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

        document.getElementById('structure_no')?.addEventListener('change', toggleStructureField);
        document.getElementById('structure_yes')?.addEventListener('change', toggleStructureField);

        // BUSCADOR PREDICTIVO
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
                        var html = '';
                        for (var i = 0; i < data.length; i++) {
                            var team = data[i];
                            var isExactMatch = team.name.toLowerCase() === query.toLowerCase();
                            var badgeClass = isExactMatch ? 'bg-warning' : 'bg-success';
                            var badgeText = isExactMatch ? 'Coincidencia exacta' : 'Existente';
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

        document.getElementById('structure_search')?.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value;
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => searchStructures(query), 300);
            } else {
                document.getElementById('structureSuggestions').style.display = 'none';
                document.getElementById('newStructureOption').style.display = 'none';
            }
        });

        document.getElementById('createNewStructure')?.addEventListener('change', function(e) {
            const searchValue = document.getElementById('structure_search').value;
            const checkbox = e.target;
            if (checkbox.checked) {
                if (existingStructureId && existingStructureName && existingStructureName.toLowerCase() === searchValue.toLowerCase()) {
                    Swal.fire({ icon: 'error', title: 'Nombre duplicado', text: 'La estructura "' + searchValue + '" ya existe. No puedes crear una nueva con el mismo nombre.', confirmButtonColor: '#00ecfe' });
                    checkbox.checked = false;
                    return;
                }
                fetch('/verificar-estructura?nombre=' + encodeURIComponent(searchValue))
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            Swal.fire({ icon: 'error', title: 'Nombre duplicado', text: 'La estructura "' + searchValue + '" ya existe en el sistema.', confirmButtonColor: '#00ecfe' });
                            checkbox.checked = false;
                            document.getElementById('structure_id').value = data.id;
                            document.getElementById('structure_name_hidden').value = searchValue;
                            document.getElementById('structure_search').value = searchValue;
                            document.getElementById('selectedStructureInfo').innerHTML = '<div class="alert alert-warning">' + escapeHtml(searchValue) + ' ya existe. Se usará la estructura existente.</div>';
                        } else {
                            document.getElementById('structure_id').value = '';
                            document.getElementById('structure_name_hidden').value = searchValue;
                            document.getElementById('selectedStructureInfo').innerHTML = '<div class="alert alert-info">Se creará: <strong>' + escapeHtml(searchValue) + '</strong></div>';
                            document.getElementById('structure_search').classList.remove('is-invalid');
                        }
                    });
            } else {
                document.getElementById('structure_id').value = '';
                document.getElementById('structure_name_hidden').value = '';
                document.getElementById('selectedStructureInfo').innerHTML = '';
            }
        });

        document.addEventListener('click', function(e) {
            const suggestions = document.getElementById('structureSuggestions');
            const search = document.getElementById('structure_search');
            if (suggestions && search && !search.contains(e.target) && !suggestions.contains(e.target)) {
                suggestions.style.display = 'none';
            }
        });

        // VEHÍCULOS
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
                row.querySelectorAll('input').forEach(input => input.value = '');
            }
        }

        // PAGO
        document.getElementById('payment_method')?.addEventListener('change', function() {
            const method = this.value;
            const bankAccounts = document.getElementById('bankAccounts');
            const paymentReference = document.getElementById('paymentReferenceField');
            const paymentProof = document.getElementById('paymentProofField');

            if (method === 'transferencia' || method === 'pago_movil' || method === 'paypal' || method === 'zelle') {
                if (bankAccounts) bankAccounts.style.display = 'block';
                if (paymentReference) paymentReference.style.display = 'block';
                if (paymentProof) paymentProof.style.display = 'block';
            } else if (method === 'efectivo') {
                if (bankAccounts) bankAccounts.style.display = 'none';
                if (paymentReference) paymentReference.style.display = 'none';
                if (paymentProof) paymentProof.style.display = 'none';
            } else {
                if (bankAccounts) bankAccounts.style.display = 'none';
                if (paymentReference) paymentReference.style.display = 'none';
                if (paymentProof) paymentProof.style.display = 'none';
            }
        });

        // DRAG AND DROP
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('excelFile');
        const fileNameDiv = document.getElementById('fileName');
        if (uploadArea) {
            uploadArea.addEventListener('dragover', e => { e.preventDefault(); uploadArea.classList.add('dragover'); });
            uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));
            uploadArea.addEventListener('drop', e => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    if (fileNameDiv) fileNameDiv.innerHTML = '<i class="fas fa-check-circle text-success"></i> Archivo: ' + e.dataTransfer.files[0].name;
                }
            });
            uploadArea.addEventListener('click', () => fileInput.click());
        }
        if (fileInput) {
            fileInput.addEventListener('change', e => {
                if (e.target.files.length && fileNameDiv) fileNameDiv.innerHTML = '<i class="fas fa-check-circle text-success"></i> Archivo: ' + e.target.files[0].name;
            });
        }

        // Mostrar formulario según errores
        @if($errors->any() && !session('import_errors'))
        showIndividualForm();
        @endif
        @if(session('import_errors') || ($errors->any() && session('import_errors')))
        showTeamForm();
        @endif
        @if(session('form_error') == 'individual')
        showIndividualForm();
        @endif
        @if(session('form_error') == 'team')
        showTeamForm();
        @endif

        // Modal de éxito
        @if(session('success_modal'))
        document.addEventListener('DOMContentLoaded', function() {
            var details = @json(session('success_details'));
            var table = document.getElementById('successDetailsTable');
            if (table && details) {
                var html = '<tr><td class="fw-bold">👤 Ciclista:</td><td>' + (details.nombre || '') + '</td><tr>' +
                    '<tr><td class="fw-bold">🔢 Dorsal:</td><td><span class="badge bg-primary">' + (details.dorsal || '') + '</span></td></tr>' +
                    '<tr><td class="fw-bold">🏆 Categoría:</td><td>' + (details.categoria || '') + '</td></tr>' +
                    '<tr><td class="fw-bold">🏢 Estructura:</td><td>' + (details.estructura || 'Independiente') + '</td></tr>' +
                    '<tr><td class="fw-bold">📧 Email:</td><td>' + (details.email || '') + '</td></tr>';
                table.innerHTML = html;
            }
            document.getElementById('successMessage').innerHTML = '{{ session('success_message') }}';
            new bootstrap.Modal(document.getElementById('successModal')).show();
            hideForms();
        });
        @endif

        // Modal de estado
        @if(session('show_status_modal'))
        document.addEventListener('DOMContentLoaded', function() {
            var statusData = @json(session('status_data'));
            var statusContent = document.getElementById('statusContent');
            if (statusContent && statusData) {
                var statusBadge = '';
                switch(statusData.status) {
                    case 'pending': statusBadge = '<span class="badge bg-warning text-dark">⏳ Pendiente de revisión</span>'; break;
                    case 'approved': statusBadge = '<span class="badge bg-success">✅ Aprobada</span>'; break;
                    case 'rejected': statusBadge = '<span class="badge bg-danger">❌ Rechazada</span>'; break;
                    case 'paid': statusBadge = '<span class="badge bg-info">💰 Pagada</span>'; break;
                    default: statusBadge = '<span class="badge bg-secondary">📝 Registrada</span>';
                }
                var eventName = statusData.event ? statusData.event.name : 'Vuelta a la Fría 2026';
                var registeredDate = new Date(statusData.registered_at).toLocaleDateString('es-VE');
                var html = '<div class="text-center mb-4">' +
                    '<i class="fas fa-user-circle fa-4x text-primary"></i>' +
                    '<h5 class="mt-2">' + (statusData.registration_type === 'individual' ? 'Ciclista Individual' : 'Equipo') + '</h5>' +
                    '</div>' +
                    '<div class="alert alert-info">' +
                    '<strong>📅 Evento:</strong> ' + eventName + '<br>' +
                    '<strong>📧 Email registrado:</strong> ' + statusData.email + '<br>' +
                    '<strong>📞 Teléfono:</strong> ' + statusData.phone + '<br>' +
                    '<strong>📌 Estado:</strong> ' + statusBadge + '<br>' +
                    '<strong>📅 Fecha de registro:</strong> ' + registeredDate +
                    '</div>';
                if (statusData.athlete) {
                    html += '<div class="alert alert-success">' +
                        '<strong>👤 Ciclista:</strong> ' + statusData.athlete.first_name + ' ' + statusData.athlete.last_name + '<br>' +
                        '<strong>🔢 Dorsal:</strong> <span class="badge bg-primary">' + statusData.athlete.dorsal_number + '</span><br>' +
                        '<strong>🏆 Categoría:</strong> ' + statusData.athlete.category +
                        '</div>';
                }
                statusContent.innerHTML = html;
            }
            new bootstrap.Modal(document.getElementById('statusModal')).show();
        });
        @endif

        @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Error', text: {!! json_encode(session('error')) !!}, confirmButtonColor: '#00ecfe' });
        @endif
    </script>
@endsection
