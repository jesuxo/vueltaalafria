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
                    <p class="fecha" style="font-size: 30px; padding: 20px 0px">12 - 14 JUNIO 2026</p>
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
                    <p>La primera edición fué en 1992 </p>
                </div>
                <div class="row align-items-center g-5">
                    <div class="col-lg-6" data-aos="fade-right" style="margin-top: 10px">
                        <img src="/images/home1.jpg" style="border-radius: 6px;" alt="Ciclismo  Menor" class="about-img">
                    </div>
                    <div class="col-lg-6" data-aos="fade-left">
                        <p >La Vuelta Menor a La Fría es un evento de ruta por etapas que se realizará del 11 al 14 de junio de 2026 en el municipio García de Hevia, estado Táchira, Venezuela.</p>
                        <p>El evento está regido por el reglamento de la Unión Ciclista Internacional (UCI) y la Federación Venezolana de Ciclismo (FVC). La participación es abierta para ciclistas independientes y equipos organizados como clubes y escuelas, mediante invitación para las categorías menores desde Strider hasta Juvenil.</p>
                        <p class="mt-3">La carrera cuenta con el aval de la Asociación Tachirense de Ciclismo, la Comisión Nacional de Ciclismo Menor y Juvenil, la Federación Venezolana de Ciclismo (FVC), el Instituto Municipal de Deporte (IMDERE) y el Instituto del Deporte Tachirense (IDT).</p>
                        <div class="mt-4">
                            <div class="row  ">
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
                            <p>  Strider  <br>  Compota  </p>
                            <p>Domingo 14/06: 100mts  </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="500">
                        <div class="route-card">
                            <i class="fas fa-road"></i>
                            <h4>Iniciación A, B y C</h4>
                            <p>     Iniciación "A": 1.8 km
                                <br>Iniciación "B": 3.6 km
                                <br>Iniciación "C": 7.2 km</p>
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

        <!-- Sección de Inscripción Mejorada -->
        <section id="preinscripcion" class="section">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>INSCRIPCIONES</h2>
                    <p>Preinscripción del 25/05 al 08/06/2026</p>
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

                <!-- FORMULARIO INDIVIDUAL (oculto inicialmente) -->
                <div id="individualForm" class="form-container" style="display: {{ session('form_error') == 'individual' || session('form_success') == 'individual' ? 'block' : 'none' }};">
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

                                    {{-- Mostrar errores específicos --}}
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
                                                <input type="text" name="first_name"
                                                       class="form-control @error('first_name') is-invalid @enderror"
                                                       required value="{{ old('first_name') }}">
                                                @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Apellidos</label>
                                                <input type="text" name="last_name"
                                                       class="form-control @error('last_name') is-invalid @enderror"
                                                       required value="{{ old('last_name') }}">
                                                @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Email</label>
                                                <input type="email" name="email"
                                                       class="form-control @error('email') is-invalid @enderror"
                                                       required value="{{ old('email') }}">
                                                @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Teléfono/WhatsApp</label>
                                                <input type="text" name="phone"
                                                       class="form-control @error('phone') is-invalid @enderror"
                                                       required value="{{ old('phone') }}">
                                                @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label required-field">Género</label>
                                                <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
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
                                                <input type="date" name="birth_date"
                                                       class="form-control @error('birth_date') is-invalid @enderror"
                                                       required value="{{ old('birth_date') }}">
                                                @error('birth_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label required-field">Categoría</label>
                                                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                                    <option value="">Seleccionar categoría</option>
                                                    <optgroup label="3 Etapas (Viernes a Domingo)">
                                                        @if(old('gender') == 'Masculino' || !old('gender'))
                                                            <option value="Pre-Infantil" {{ old('category') == 'Pre-Infantil' ? 'selected' : '' }}>Pre-Infantil Masculino (11-12 años - Nacidos 2015-2014)</option>
                                                            <option value="Infantil" {{ old('category') == 'Infantil' ? 'selected' : '' }}>Infantil Masculino (13-14 años - Nacidos 2013-2012)</option>
                                                            <option value="Pre-Juvenil" {{ old('category') == 'Pre-Juvenil' ? 'selected' : '' }}>Pre-Juvenil Masculino (15-16 años - Nacidos 2011-2010)</option>
                                                            <option value="Juvenil" {{ old('category') == 'Juvenil' ? 'selected' : '' }}>Juvenil Masculino (17-18 años - Nacidos 2009-2008)</option>
                                                        @endif
                                                        @if(old('gender') == 'Femenino' || !old('gender'))
                                                            <option value="Pre-Infantil" {{ old('category') == 'Pre-Infantil' ? 'selected' : '' }}>Pre-Infantil Femenino (11-12 años - Nacidos 2015-2014)</option>
                                                            <option value="Infantil" {{ old('category') == 'Infantil' ? 'selected' : '' }}>Infantil Femenino (13-14 años - Nacidos 2013-2012)</option>
                                                            <option value="Pre-Juvenil" {{ old('category') == 'Pre-Juvenil' ? 'selected' : '' }}>Pre-Juvenil Femenino (15-16 años - Nacidos 2011-2010)</option>
                                                            <option value="Juvenil" {{ old('category') == 'Juvenil' ? 'selected' : '' }}>Juvenil Femenino (17-18 años -  Nacidos 2009-2008)</option>
                                                        @endif
                                                    </optgroup>
                                                    <optgroup label="1 Día (Domingo) - Exhibición">
                                                        <option value="Iniciación A" {{ old('category') == 'Iniciación A' ? 'selected' : '' }}>Iniciación A (5-6 años - Nacidos 2021-2020)</option>
                                                        <option value="Iniciación B" {{ old('category') == 'Iniciación B' ? 'selected' : '' }}>Iniciación B (7-8 años - Nacidos 2019-2018)</option>
                                                        <option value="Iniciación C" {{ old('category') == 'Iniciación C' ? 'selected' : '' }}>Iniciación C (9-10 años - Nacidos 2017-2016)</option>
                                                        <option value="Compota Strider" {{ old('category') == 'Compota Strider' ? 'selected' : '' }}>Compota Strider (3-4 años - Nacidos 2023-2022)</option>
                                                        <option value="Compota Pedales" {{ old('category') == 'Compota Pedales' ? 'selected' : '' }}>Compota Pedales (3-4 años - Nacidos 2023-2022)</option>
                                                    </optgroup>
                                                </select>
                                                @error('category')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label required-field">Contacto de Emergencia</label>
                                                <input type="text" name="emergency_contact"
                                                       class="form-control @error('emergency_contact') is-invalid @enderror"
                                                       required placeholder="Nombre completo y teléfono de contacto"
                                                       value="{{ old('emergency_contact') }}">
                                                @error('emergency_contact')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Ej: Juan Pérez - 0414XXXXXX</small>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input @error('accept_terms') is-invalid @enderror"
                                                           id="acceptTermsIndividual" name="accept_terms" required>
                                                    <label class="form-check-label" for="acceptTermsIndividual">
                                                        Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">términos y condiciones</a> de la competencia
                                                    </label>
                                                    @error('accept_terms')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 text-center">
                                                <!-- Si no tienes reCAPTCHA, comenta esta línea -->
                                                <div class="g-recaptcha mb-3" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                                                <button type="submit" class="btn-custom">
                                                    <i class="fas fa-paper-plane me-2"></i> Enviar Inscripción
                                                </button>
                                                <button type="button" class="btn-outline-custom ms-2" onclick="hideForms()">
                                                    <i class="fas fa-times me-2"></i> Cancelar
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FORMULARIO POR EQUIPOS (oculto inicialmente) -->
                <div id="teamForm" class="form-container" style="display: none;">
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

                                        <!-- Información del Equipo -->
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

                                        <!-- Datos del Delegado -->
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

                                        <!-- Datos Migratorios (colapsable para internacionales) -->
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

                                        <!-- Vehículos de la delegación -->
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

                                        <!-- Subida de Excel -->
                                        <div class="bg-light p-3 rounded mb-4">
                                            <h5 class="mb-3" style="color: var(--primary);"><i class="fas fa-file-excel me-2"></i> Lista de Atletas</h5>

                                            <div class="alert alert-info">
                                                <i class="fas fa-download me-2"></i>
                                                <strong>Descarga la plantilla:</strong>
                                                <a href="{{ route('registration.team.download-template') }}" class="btn btn-sm btn-primary ms-2">
                                                    <i class="fas fa-download"></i> Descargar Plantilla Excel
                                                </a>
                                                <hr class="my-2">
                                                <small class="d-block">La plantilla debe contener: ID, APELLIDOS, NOMBRES, FECHA DE NACIMIENTO, TIPO DOCUMENTO, NÚMERO DOCUMENTO, UCI ID, CATEGORÍA, GÉNERO</small>
                                            </div>

                                            <div class="upload-area" id="uploadArea">
                                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                                <p>Arrastra tu archivo aquí o haz clic para seleccionar</p>
                                                <input type="file" name="excel_file" id="excelFile" accept=".xlsx,.xls,.csv" style="display: none;" required>
                                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('excelFile').click()">
                                                    Seleccionar archivo
                                                </button>
                                                <div id="fileName" class="mt-2 small text-muted"></div>
                                            </div>
                                            <small class="text-muted">Formatos aceptados: .xlsx, .xls, .csv (máx 5MB)</small>
                                        </div>

                                        <!-- reCAPTCHA y Términos -->
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
                                            <button type="submit" class="btn-custom btn-lg">
                                                <i class="fas fa-paper-plane me-2"></i> Enviar Inscripción del Equipo
                                            </button>
                                            <button type="button" class="btn-outline-custom ms-2" onclick="hideForms()">
                                                <i class="fas fa-times me-2"></i> Cancelar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-5" data-aos="fade-up">
                    <p class="text-muted">Pago a tasa oficial BCV. Para más información contactar a Jhoana Noriega: <strong>+58 424-7371101</strong></p>
                    <p class="text-muted">Forma de pago: Cuentas bancarias disponibles al solicitar planilla de preinscripción</p>
                </div>
            </div>
        </section>

        <!-- Modal de Términos y Condiciones (igual que antes) -->
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

        <script>
            // Drag and drop para archivo
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('excelFile');
            const fileName = document.getElementById('fileName');

            if (uploadArea) {
                uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    uploadArea.classList.add('dragover');
                });

                uploadArea.addEventListener('dragleave', () => {
                    uploadArea.classList.remove('dragover');
                });

                uploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                    const files = e.dataTransfer.files;
                    if (files.length) {
                        fileInput.files = files;
                        updateFileName(files[0].name);
                    }
                });

                uploadArea.addEventListener('click', () => {
                    if (window.innerWidth > 768) {
                        fileInput.click();
                    }
                });
            }

            if (fileInput) {
                fileInput.addEventListener('change', (e) => {
                    if (e.target.files.length) {
                        updateFileName(e.target.files[0].name);
                    }
                });
            }

            function updateFileName(name) {
                if (fileName) {
                    fileName.innerHTML = '<i class="fas fa-check-circle text-success"></i> Archivo seleccionado: ' + name;
                }
            }

            let vehicleIndex = 1;

            function addVehicleRow() {
                const tbody = document.getElementById('vehiclesBody');
                if (tbody) {
                    const newRow = `
                <tr>
                    <td><input type="text" name="vehicles[${vehicleIndex}][brand]" class="form-control form-control-sm" placeholder="Marca"></td>
                    <td><input type="text" name="vehicles[${vehicleIndex}][model]" class="form-control form-control-sm" placeholder="Modelo"></td>
                    <td><input type="text" name="vehicles[${vehicleIndex}][plate]" class="form-control form-control-sm" placeholder="Placa"></td>
                    <td><input type="number" name="vehicles[${vehicleIndex}][year]" class="form-control form-control-sm" placeholder="Año"></td>
                    <td><input type="text" name="vehicles[${vehicleIndex}][color]" class="form-control form-control-sm" placeholder="Color"></td>
                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeVehicleRow(this)"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;
                    tbody.insertAdjacentHTML('beforeend', newRow);
                    vehicleIndex++;
                }
            }

            function removeVehicleRow(button) {
                const row = button.closest('tr');
                if (row && document.getElementById('vehiclesBody').children.length > 1) {
                    row.remove();
                } else if (row) {
                    // Limpiar campos en lugar de eliminar la última fila
                    row.querySelectorAll('input').forEach(input => input.value = '');
                }
            }
        </script>

        <style>
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
            .nav-pills .nav-link {
                background: #eee !important;
                border-radius: 50px !important;
                padding: 10px 25px !important;
                color: #333 !important;
            }
            .nav-pills .nav-link.active {
                background: #00ecfe !important;
                color: #000 !important;
            }
            .nav-pills .nav-link i {
                margin-right: 8px;
            }
            .card {
                border-radius: 20px;
                overflow: hidden;
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
        </style>



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
                                Dirección Técnica - <a href="https://api.whatsapp.com/send/?phone=584126851119&text=Hola+Srs.+de+la+vuelta+a+la+fria+quisiera+informacion+sobre%3A+&type=phone_number&app_absent=0" target="_blank" style="color:#999 !important; text-decoration: none !important;">+58 412-6851119</a>
                            </div>
                        </div>
                        <div class="contact-info">
                            <i class="fas fa-file-alt"></i>
                            <div>
                                <strong>Gustavo Uzcátegui Rosales</strong><br>
                                Secretario General - <a href="https://api.whatsapp.com/send/?phone=584247038594&text=Hola+Srs.+de+la+vuelta+a+la+fria+quisiera+informacion+sobre%3A+&type=phone_number&app_absent=0" target="_blank" style="color:#999 !important; text-decoration: none !important;">+58 424-7038594</a>
                            </div>
                        </div>
                        <div class="contact-info">
                            <i class="fas fa-user-check"></i>
                            <div>
                                <strong>Jhoana Noriega</strong><br>
                                Coordinadora de Preinscripción - <a href="https://api.whatsapp.com/send/?phone=584247371101&text=Hola+Srs.+de+la+vuelta+a+la+fria+quisiera+informacion+sobre%3A+&type=phone_number&app_absent=0" target="_blank" style="color:#999 !important; text-decoration: none !important;">+58 424-7371101</a>
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


                function showIndividualForm() {
                    // Ocultar ambos formularios
                    document.getElementById('individualForm').style.display = 'none';
                    document.getElementById('teamForm').style.display = 'none';

                    // Remover clase active de ambos botones
                    document.getElementById('btnIndividual').classList.remove('active');
                    document.getElementById('btnTeam').classList.remove('active');

                    // Mostrar el formulario individual con animación
                    document.getElementById('individualForm').style.display = 'block';
                    document.getElementById('individualForm').classList.add('show');

                    // Agregar clase active al botón
                    document.getElementById('btnIndividual').classList.add('active');

                    // Scroll suave al formulario
                    setTimeout(() => {
                        document.getElementById('individualForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                }

                function showTeamForm() {
                    // Ocultar ambos formularios
                    document.getElementById('individualForm').style.display = 'none';
                    document.getElementById('teamForm').style.display = 'none';

                    // Remover clase active de ambos botones
                    document.getElementById('btnIndividual').classList.remove('active');
                    document.getElementById('btnTeam').classList.remove('active');

                    // Mostrar el formulario de equipo con animación
                    document.getElementById('teamForm').style.display = 'block';
                    document.getElementById('teamForm').classList.add('show');

                    // Agregar clase active al botón
                    document.getElementById('btnTeam').classList.add('active');

                    // Scroll suave al formulario
                    setTimeout(() => {
                        document.getElementById('teamForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 100);
                }

                function hideForms() {
                    // Ocultar ambos formularios
                    document.getElementById('individualForm').style.display = 'none';
                    document.getElementById('teamForm').style.display = 'none';

                    // Remover clase active de ambos botones
                    document.getElementById('btnIndividual').classList.remove('active');
                    document.getElementById('btnTeam').classList.remove('active');
                }

                // Mostrar formulario si hay errores en individual
                @if($errors->any() && !session('import_errors'))
                showIndividualForm();
                @endif

                // Mostrar formulario si hay errores en equipo
                @if(session('import_errors') || ($errors->any() && session('import_errors')))
                showTeamForm();
                @endif

                // Validación del formulario individual antes de enviar
                document.getElementById('individualFormSubmit')?.addEventListener('submit', function(e) {
                    let errors = [];
                    let firstError = null;

                    // Validar campos
                    const fields = [
                        { name: 'first_name', label: 'Nombres' },
                        { name: 'last_name', label: 'Apellidos' },
                        { name: 'email', label: 'Email' },
                        { name: 'phone', label: 'Teléfono' },
                        { name: 'gender', label: 'Género' },
                        { name: 'birth_date', label: 'Fecha de nacimiento' },
                        { name: 'category', label: 'Categoría' },
                        { name: 'emergency_contact', label: 'Contacto de emergencia' }
                    ];

                    fields.forEach(field => {
                        const input = document.querySelector(`[name="${field.name}"]`);
                        if (input && !input.value.trim()) {
                            errors.push(`❌ El campo "${field.label}" es obligatorio`);
                            input.classList.add('is-invalid');
                            if (!firstError) firstError = input;
                        } else if (input) {
                            input.classList.remove('is-invalid');
                        }
                    });

                    // Validar email
                    const email = document.querySelector('[name="email"]');
                    if (email && email.value.trim()) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(email.value)) {
                            errors.push(`❌ El email ingresado no es válido`);
                            email.classList.add('is-invalid');
                            if (!firstError) firstError = email;
                        }
                    }

                    // Validar términos
                    const terms = document.getElementById('acceptTermsIndividual');
                    if (terms && !terms.checked) {
                        errors.push(`❌ Debes aceptar los términos y condiciones`);
                        terms.classList.add('is-invalid');
                    } else if (terms) {
                        terms.classList.remove('is-invalid');
                    }

                    // Validar reCAPTCHA si está activo
                    const recaptcha = document.querySelector('.g-recaptcha');
                    if (recaptcha && typeof grecaptcha !== 'undefined') {
                        const token = document.querySelector('[name="g-recaptcha-response"]');
                        if (token && !token.value) {
                            errors.push(`❌ Debes completar la verificación humana`);
                        }
                    }

                    if (errors.length > 0) {
                        e.preventDefault();

                        // Mostrar alerta con todos los errores
                        let errorMsg = '⚠️ Por favor, corrige los siguientes errores:\n\n';
                        errors.forEach(err => {
                            errorMsg += err + '\n';
                        });
                        alert(errorMsg);

                        // Scroll al primer error
                        if (firstError) {
                            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstError.focus();
                        }

                        return false;
                    }

                    return true;
                });
            </script>
        @endsection
    @endsection
@endsection
