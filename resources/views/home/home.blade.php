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
    </style>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero" id="inicio" style="text-align: left !important;">
        <div class="container text-center text-white">
            <div data-aos="fade-up" style="text-align: left !important;">
                <p class="fecha" style="font-size: 30px; padding: 30px 0px">12 - 14 JUNIO 2026</p>
                <h1>VUELTA A LA <span>FRIA</span></h1>
                <p class="subtitulo" style="font-size: 30px; padding: 30px 0px">LA VUELTA MENOR MAS IMPORTANTE DE VENEZUELA</p>
                <div>
                    <a href="#inscripcion" class="btn-custom">Inscribirme Ahora</a>
                    <a href="#acerca" class="btn-custom btn-outline-custom">Más Información</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Divisor de Montañas -->
    <div class="mountain-divider-modern" style="position: relative; height: 150px; overflow: hidden; margin-top: -84px;">
        <div style="position: absolute; bottom: 50px; left: 0; width: 100%; height: 100px; background: url('/images/mountains_pattern_01.png') repeat-x bottom; background-size: auto 100%;"></div>
    </div>

    <!-- About Section -->
    <section id="informacion" class="section" style="text-align: left !important;">
        <div class="container" style="text-align: left !important;">
            <div class="section-title" data-aos="fade-up">
                <h2>INFORMACIÓN</h2>
                <p>Carrera de montaña legendaria desde 1997</p>
            </div>
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <img src="https://images.unsplash.com/photo-1541625602330-2277a4c46182?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Mountain Bike" class="about-img">
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <p class="lead">Esta es la carrera épica de MTB en Utah. El circuito está diseñado por la leyenda del trail building Peter Laing, y ha sido votado regularmente como el mejor del país.</p>
                    <p>Ven a probarte en una mezcla fantástica de sendero natural, caminos forestales, antiguos caminos de herradura, sendero artesanal y centro de trail. Visitarás los valles de los ríos Tweed, Yarrow y Ettrick, y por supuesto, los puntos altos intermedios.</p>
                    <p class="mt-3">La carrera tiene lugar en las estepas y montañas cercanas al embalse Bartogay. El variado terreno y las condiciones climáticas hacen de esta carrera un verdadero desafío incluso para los ciclistas profesionales.</p>
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
                                <p class="fw-bold mt-2">500+ Ciclistas</p>
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
                        <div class="stat-number">80</div>
                        <div class="stat-label">KM DISTANCIA</div>
                    </div>
                </div>
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-card">
                        <div class="stat-number">2000</div>
                        <div class="stat-label">M ASCENSO</div>
                    </div>
                </div>
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-card">
                        <div class="stat-number">27</div>
                        <div class="stat-label">AÑOS</div>
                    </div>
                </div>
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-card">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">CICLISTAS</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Section -->
    <section class="section video-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2 style="color: white;">VIDEO</h2>
                <p style="color: #aaa;">RedRock Epic 2017 - Mira la acción</p>
            </div>
            <div class="row justify-content-center" data-aos="zoom-in">
                <div class="col-lg-8">
                    <div class="video-wrapper">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/placeholder" title="RedRock Epic Highlights" allowfullscreen></iframe>
                        </div>
                    </div>
                    <p class="text-center text-white-50 mt-3">Mira el video de eventos anteriores</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Schedule Section -->
    <section id="programa" class="section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>SOBRE LA CARRERA</h2>
                <p>Programa del evento</p>
            </div>
            <div class="schedule-timeline">
                <div class="schedule-item" data-aos="fade-up">
                    <div class="schedule-time">6:30 - 8:45 am</div>
                    <div class="schedule-event">Recogida de paquetes en el lugar</div>
                </div>
                <div class="schedule-item" data-aos="fade-up">
                    <div class="schedule-time">6:40 - 6:50</div>
                    <div class="schedule-event">Fila y reunión de corredores - 100 Millas</div>
                </div>
                <div class="schedule-item" data-aos="fade-up">
                    <div class="schedule-time">6:50 - 6:58</div>
                    <div class="schedule-event">Ceremonia de apertura</div>
                </div>
                <div class="schedule-item" data-aos="fade-up">
                    <div class="schedule-time">7:00 AM</div>
                    <div class="schedule-event">Inicio - 100 Millas</div>
                </div>
                <div class="schedule-item" data-aos="fade-up">
                    <div class="schedule-time">8:15</div>
                    <div class="schedule-event">Fila y reunión de corredores - 50 Millas</div>
                </div>
                <div class="schedule-item" data-aos="fade-up">
                    <div class="schedule-time">8:30 AM</div>
                    <div class="schedule-event">Inicio - 50 Millas</div>
                </div>
                <div class="schedule-item" data-aos="fade-up">
                    <div class="schedule-time">12:30 pm</div>
                    <div class="schedule-event">Comida post carrera</div>
                </div>
                <div class="schedule-item" data-aos="fade-up">
                    <div class="schedule-time">1:30 - 5:00 pm</div>
                    <div class="schedule-event">Concierto en vivo en la meta</div>
                </div>
                <div class="schedule-item" data-aos="fade-up">
                    <div class="schedule-time">2:00 - 6:00 pm</div>
                    <div class="schedule-event">Ceremonia de premiación</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rules Section -->
    <section class="section section-dark">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>SOBRE LA CARRERA</h2>
                <p>Reglas del evento</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="p-4 bg-white rounded-4 h-100">
                        <h3 class="text-danger mb-4"><i class="fas fa-child me-2"></i> Límites de edad</h3>
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="fas fa-check-circle text-danger me-2"></i> Nadie menor de 14 años puede competir</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-danger me-2"></i> Corredores entre 14-16 deben enviar un currículum</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-danger me-2"></i> Corredores jóvenes deben enviar autorización firmada</li>
                            <li><i class="fas fa-check-circle text-danger me-2"></i> Todo corredor menor de 18 debe tener un acompañante</li>
                        </ul>
                        <h3 class="text-danger mt-4 mb-4"><i class="fas fa-flag-checkered me-2"></i> Inicio</h3>
                        <p>Los horarios de salida son escalonados. Usamos salida con pistola. Saltarse la salida es trampa y resultará en penalización.</p>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="p-4 bg-white rounded-4 h-100">
                        <h3 class="text-danger mb-4"><i class="fas fa-gavel me-2"></i> Reglas generales</h3>
                        <ul class="list-unstyled">
                            <li class="mb-3"><i class="fas fa-check-circle text-danger me-2"></i> Sé responsable con tus pertenencias</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-danger me-2"></i> Sé amable con tus compañeros corredores</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-danger me-2"></i> No hagas atajos en el circuito</li>
                            <li class="mb-3"><i class="fas fa-check-circle text-danger me-2"></i> Respeta el entorno del desierto</li>
                            <li><i class="fas fa-check-circle text-danger me-2"></i> No tires basura en el sendero</li>
                        </ul>
                        <div class="text-center mt-4">
                            <a href="#" class="btn-custom">Ver términos completos</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Route Section -->
    <section id="etapas" class="section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>ETAPAS</h2>
                <p>El Circuito Épico</p>
            </div>
            <div class="row mb-5" data-aos="fade-up">
                <div class="col-lg-10 mx-auto">
                    <img src="https://images.unsplash.com/photo-1571008887538-b36bb32f4571?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80" alt="Mapa de Ruta" class="route-map-img">
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3" data-aos="fade-up">
                    <div class="route-card">
                        <i class="fas fa-flag-checkered"></i>
                        <h4>Inicio</h4>
                        <p>Bucle de 15km a través de cañones de roca roja con vistas impresionantes.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="route-card">
                        <i class="fas fa-arrow-up"></i>
                        <h4>La Subida Más Larga</h4>
                        <p>8km de ascenso con 600m de desnivel - la prueba definitiva.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="route-card">
                        <i class="fas fa-bicycle"></i>
                        <h4>Terreno Técnico</h4>
                        <p>Jardines de roca y descensos técnicos que desafían a los mejores.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="route-card">
                        <i class="fas fa-tachometer-alt"></i>
                        <h4>Tramo Rápido</h4>
                        <p>Secciones de alta velocidad a través de caminos forestales.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="route-card">
                        <i class="fas fa-music"></i>
                        <h4>Rock'n Roll</h4>
                        <p>Secciones con ritmo que fluyen como la música.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="500">
                    <div class="route-card">
                        <i class="fas fa-arrow-down"></i>
                        <h4>Descenso Técnico</h4>
                        <p>Descenso rápido y técnico hasta la línea de meta.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="600">
                    <div class="route-card">
                        <i class="fas fa-star"></i>
                        <h4>Subida de Luca</h4>
                        <p>Nombrada en honor a una leyenda local - empinada y gratificante.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="700">
                    <div class="route-card">
                        <i class="fas fa-trophy"></i>
                        <h4>Línea de Meta</h4>
                        <p>Final épico con multitudes animándote.</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5" data-aos="fade-up">
                <a href="#" class="btn-custom"><i class="fas fa-download me-2"></i> Descargar Archivo GPX</a>
            </div>
        </div>
    </section>

    <!-- Key Info Section -->
    <section class="section section-dark">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>IMPORTANTE</h2>
                <p>Información clave</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up">
                    <div class="text-center p-4">
                        <i class="fas fa-child fa-3x text-danger mb-3"></i>
                        <h4>¡Niños gratis!</h4>
                        <p>¡Todos los ciclistas menores de 16 años corren gratis cuando van acompañados por un adulto que paga!</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="text-center p-4">
                        <i class="fas fa-laptop fa-3x text-danger mb-3"></i>
                        <h4>Pre-inscripción online</h4>
                        <p>Ahorra $3.00 por persona vs. inscribirte el día del evento. ¡Evita la fila!</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-center p-4">
                        <i class="fas fa-calendar-day fa-3x text-danger mb-3"></i>
                        <h4>Inscripción el día del evento</h4>
                        <p>Disponible si no está agotado. Sujeto a recargo de $3.00.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>AYUDA</h2>
                <p>Preguntas frecuentes</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item" data-aos="fade-up">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    ¿Necesito una licencia de ciclismo?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">Sí, todos los participantes deben tener una licencia UCI válida o comprar una licencia de un día en el registro.</div>
                            </div>
                        </div>
                        <div class="accordion-item" data-aos="fade-up">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    ¿Cuántas vueltas haré?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">Carrera de 100 Millas: 2 vueltas (50 millas cada una). Carrera de 50 Millas: 1 vuelta.</div>
                            </div>
                        </div>
                        <div class="accordion-item" data-aos="fade-up">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    ¿Qué pasa si me doblan?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">Los corredores que sean doblados serán redirigidos al área de meta por razones de seguridad.</div>
                            </div>
                        </div>
                        <div class="accordion-item" data-aos="fade-up">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    ¿Dónde puedo encontrar los resultados?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">Los resultados en vivo estarán disponibles en nuestro sitio web y aplicación móvil durante la carrera.</div>
                            </div>
                        </div>
                        <div class="accordion-item" data-aos="fade-up">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    ¿Hay servicio de catering disponible?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">Sí, habrá comida y bebidas disponibles en el área de salida/meta y en los puntos de avituallamiento.</div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4" data-aos="fade-up">
                        <p>¿Aún tienes preguntas? <a href="#contacto" class="text-danger fw-bold">Contáctanos</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="section section-dark">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>#redrockepic2017</h2>
                <p>Lo que la gente dice sobre eventos anteriores</p>
            </div>
            <div class="testimonials-wrapper" style="position: relative; padding-bottom: 60px;">
                <div class="swiper testimonial-swiper" data-aos="fade-up">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <i class="fas fa-quote-left"></i>
                                <p class="quote">"¡La carrera más desafiante y gratificante que he hecho! Las vistas son absolutamente increíbles."</p>
                                <p class="author">Alexander P. / Team Scott</p>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <i class="fas fa-quote-left"></i>
                                <p class="quote">"Organización increíble, circuito desafiante y un ambiente increíble. ¡Volveré todos los años!"</p>
                                <p class="author">Shean B. / Canyon Team</p>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <i class="fas fa-quote-left"></i>
                                <p class="quote">"¡Las secciones técnicas no son broma! Esta carrera te lleva al límite. ¡Absolutamente épica!"</p>
                                <p class="author">Samuel G. / Specialized Team</p>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <i class="fas fa-quote-left"></i>
                                <p class="quote">"¡El mejor evento de MTB en Utah! La comunidad, los senderos, todo es perfecto."</p>
                                <p class="author">Maria R. / Trek Team</p>
                            </div>
                        </div>
                    </div>
                    <!-- Paginación manual -->
                    <div class="custom-pagination" style="text-align: center; margin-top: 30px;">
                        <span class="dot active"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Media Gallery Section -->
    <section id="fotos" class="section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>MEDIA</h2>
                <p>Fotos de eventos anteriores</p>
            </div>
            <div class="row g-4">
                <div class="col-md-3 col-6" data-aos="fade-up">
                    <img src="https://images.unsplash.com/photo-1571008887538-b36bb32f4571?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Foto de la carrera" class="gallery-img">
                </div>
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                    <img src="https://images.unsplash.com/photo-1541625602330-2277a4c46182?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Foto de la carrera" class="gallery-img">
                </div>
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                    <img src="https://images.unsplash.com/photo-1566104808507-6c1db372b0e6?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Foto de la carrera" class="gallery-img">
                </div>
                <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                    <img src="https://images.unsplash.com/photo-1535463731090-e34f4b5098c5?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Foto de la carrera" class="gallery-img">
                </div>
            </div>
            <div class="text-center mt-5" data-aos="fade-up">
                <p>Más fotos las puedes encontrar en nuestro <a href="#" class="text-danger fw-bold">Instagram</a> y <a href="#" class="text-danger fw-bold">Facebook</a></p>
            </div>
        </div>
    </section>

    <!-- Registration Section -->
    <section id="preinscripcion" class="section section-dark">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>PARTICIPA</h2>
                <p>Tarifas de inscripción</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6" data-aos="fade-right">
                    <div class="price-card">
                        <div class="price-header">
                            <h3>Épico</h3>
                        </div>
                        <div class="price-body">
                            <div class="price">$59</div>
                            <hr>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Distancia: 80 km</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Dificultad: 4/5</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Paquete extendido</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Medalla de finalización</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Comida post carrera</p>
                            <a href="#" class="btn-custom mt-3 d-inline-block">Inscribirme</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-left">
                    <div class="price-card featured">
                        <div class="price-header">
                            <h3>Héroe</h3>
                        </div>
                        <div class="price-body">
                            <div class="price">$39</div>
                            <hr>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Distancia: 40 km</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Dificultad: 3/5</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Paquete estándar</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Medalla de finalización</p>
                            <p><i class="fas fa-check-circle text-success me-2"></i> Comida post carrera</p>
                            <a href="#" class="btn-custom mt-3 d-inline-block">Inscribirme</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5" data-aos="fade-up">
                <p class="text-muted">Los niños menores de 16 años deben ir acompañados de un adulto que pague. Se requiere identificación válida.</p>
            </div>
        </div>
    </section>

    <!-- Sponsors Section -->
    <section class="section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>APOYO</h2>
                <p>Patrocinadores y socios</p>
            </div>
            <div class="row g-4 align-items-center justify-content-center">
                <div class="col-lg-2 col-md-3 col-4" data-aos="fade-up">
                    <div class="sponsor-item">
                        <img src="https://via.placeholder.com/150x80?text=Trek" alt="Trek">
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="sponsor-item">
                        <img src="https://via.placeholder.com/150x80?text=Specialized" alt="Specialized">
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="sponsor-item">
                        <img src="https://via.placeholder.com/150x80?text=Canyon" alt="Canyon">
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="sponsor-item">
                        <img src="https://via.placeholder.com/150x80?text=Scott" alt="Scott">
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="sponsor-item">
                        <img src="https://via.placeholder.com/150x80?text=Giant" alt="Giant">
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="sponsor-item">
                        <img src="https://via.placeholder.com/150x80?text=RedBull" alt="RedBull">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contacto" class="section section-dark">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>CONTACTO</h2>
                <p>Ponte en contacto con nosotros</p>
            </div>
            <div class="row g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="contact-info">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Race Event BeFaster</strong><br>
                            2292 Peachtree Rd NW, Virgin, Utah
                        </div>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email</strong><br>
                            hola@redrockepic.com
                        </div>
                    </div>
                    <div class="contact-info">
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Teléfono</strong><br>
                            +1 800 787 7012
                        </div>
                    </div>
                    <div class="contact-info">
                        <i class="fab fa-facebook-f"></i>
                        <div>
                            <strong>Facebook</strong><br>
                            facebook.com/redrockxcm
                        </div>
                    </div>
                    <div class="contact-info">
                        <i class="fab fa-twitter"></i>
                        <div>
                            <strong>Twitter</strong><br>
                            twitter.com/redrockepic
                        </div>
                    </div>
                    <div class="contact-info">
                        <i class="fab fa-instagram"></i>
                        <div>
                            <strong>Instagram</strong><br>
                            instagram.com/redrockxcm
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d319280.80863974244!2d-113.5!3d37.1!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80ca3b6e0ef6d3b5%3A0x5c2e5a9f8e1c6!2sVirgin%2C%20UT!5e0!3m2!1sen!2sus!4v1700000000000!5m2!1sen!2sus" width="100%" height="350" style="border:0; border-radius:15px;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
            <div class="row mt-5" data-aos="fade-up">
                <div class="col-lg-8 mx-auto">
                    <div class="contact-form">
                        <h3 class="text-center mb-4">Envíanos un mensaje</h3>
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
