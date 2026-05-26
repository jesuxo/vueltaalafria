{{-- resources/views/cliente/confirmar-mantenimiento.blade.php --}}
    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Mantenimiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root {
            --pastel-blue: #e6f3ff;
            --pastel-green: #e1f7e6;
            --pastel-yellow: #fff9e6;
            --pastel-pink: #ffe6f0;
            --pastel-purple: #f0e6ff;
            --pastel-peach: #ffe6d9;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .confirm-card {
            background: white;
            border-radius: 30px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-icon {
            width: 100px;
            height: 100px;
            background: var(--pastel-purple);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 3rem;
            color: #0072c5;
            border: 5px solid white;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .vehiculo-info {
            background: var(--pastel-yellow);
            border-radius: 20px;
            padding: 20px;
            margin: 30px 0;
            border-left: 5px solid #f1be46;
        }

        .btn-confirmar {
            background: #06d6a0;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 1.2rem;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(6, 214, 160, 0.3);
        }

        .btn-confirmar:hover {
            background: #05ab80;
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(6, 214, 160, 0.4);
            color: white;
        }

        .btn-rechazar {
            background: #ef476f;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 1.2rem;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(239, 71, 111, 0.3);
        }

        .btn-rechazar:hover {
            background: #bf3959;
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(239, 71, 111, 0.4);
            color: white;
        }

        .mensaje-exito {
            background: #d4edda;
            color: #155724;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            border-left: 5px solid #06d6a0;
        }

        .mensaje-error {
            background: #f8d7da;
            color: #721c24;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            border-left: 5px solid #ef476f;
        }

        .countdown {
            background: var(--pastel-blue);
            border-radius: 15px;
            padding: 15px;
            margin-top: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<div class="confirm-card" id="confirmCard">
    <div class="header-icon">
        <i class="ri-calendar-check-line"></i>
    </div>

    <h2 class="text-center mb-3">Confirmar Asistencia</h2>
    <p class="text-center text-muted mb-4">
        Por favor, confirmános si podrás asistir al mantenimiento de tu vehículo
    </p>

    <div class="vehiculo-info">
        <div class="d-flex align-items-center mb-2">
            <i class="ri-user-line fs-4 me-2" style="color: #0072c5;"></i>
            <h5 class="mb-0">{{ $mantenimiento->vehiculo->cliente->descrip }}</h5>
        </div>
        <div class="d-flex align-items-center mb-2">
            <i class="ri-car-line fs-4 me-2" style="color: #0072c5;"></i>
            <h5 class="mb-0">{{ $mantenimiento->vehiculo->marca }} {{ $mantenimiento->vehiculo->modelo }}</h5>
        </div>
        <div class="d-flex align-items-center mb-2">
            <i class="ri-road-map-line fs-4 me-2" style="color: #0072c5;"></i>
            <span>Placa: <strong>{{ $mantenimiento->vehiculo->identificacion }}</strong></span>
        </div>
        <div class="d-flex align-items-center">
            <i class="ri-calendar-line fs-4 me-2" style="color: #0072c5;"></i>
            <span>Próximo mantenimiento:
                    <strong>
                        @if($mantenimiento->proximo_mantenimiento)
                            {{ $mantenimiento->proximo_mantenimiento->format('d/m/Y') }}
                        @elseif($mantenimiento->proximo_kilometraje)
                            {{ number_format($mantenimiento->proximo_kilometraje, 0, ',', '.') }} km
                        @endif
                    </strong>
                </span>
        </div>
    </div>

    @if($yaRespondio)
        <div class="mensaje-exito">
            <div class="d-flex align-items-center">
                <i class="ri-checkbox-circle-line fs-1 me-3"></i>
                <div>
                    <h5 class="mb-1">¡Gracias por responder!</h5>
                    <p class="mb-0">
                        @if($respuestaUsuario == 'confirmado')
                            Hemos registrado tu confirmación. Te esperamos.
                        @elseif($respuestaUsuario == 'rechazado')
                            Hemos registrado que no podrás asistir. Te contactaremos para reagendar.
                        @endif
                    </p>

                </div>
            </div>
        </div>
    @elseif(session('success'))
        <div class="mensaje-exito">
            <div class="d-flex align-items-center">
                <i class="ri-checkbox-circle-line fs-1 me-3"></i>
                <div>
                    <h5 class="mb-1">¡Gracias por responder!</h5>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="row g-3">
            <div class="col-md-6">
                <button class="btn btn-confirmar w-100" onclick="confirmar(true)">
                    <i class="ri-check-line"></i> Sí, asistiré
                </button>
            </div>
            <div class="col-md-6">
                <button class="btn btn-rechazar w-100" onclick="confirmar(false)">
                    <i class="ri-close-line"></i> No podré asistir
                </button>
            </div>
        </div>

        <div class="countdown text-center" id="countdownMessage" style="display: none;">
            <i class="ri-time-line"></i>
            <span id="countdownText"></span>
        </div>
    @endif

    <div class="text-center mt-4">
        <a href="{{ route('cliente.mantenimiento.ver', $mantenimiento->token_cliente) }}" class="text-decoration-none">
            <i class="ri-arrow-left-line"></i> Ver detalles del mantenimiento
        </a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let recordatorioId = {{ $recordatorioId ?? 0 }};

    function confirmar(asistire) {
        // Deshabilitar botones
        $('.btn-confirmar, .btn-rechazar').prop('disabled', true);

        // Mostrar mensaje de carga
        $('#confirmCard').append(`
        <div class="text-center mt-3" id="loadingMsg">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Procesando...</span>
            </div>
            <p class="mt-2">Procesando tu confirmación...</p>
        </div>
    `);

        // Enviar como 1/0 en lugar de true/false
        let valorConfirmar = asistire ? 1 : 0;

        $.ajax({
            url: '{{ route("cliente.mantenimiento.procesar-confirmacion") }}',
            method: 'POST',
            data: {
                token: '{{ $mantenimiento->token_cliente }}',
                recordatorio_id: recordatorioId,
                confirmar: valorConfirmar, // Enviamos 1 o 0
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#loadingMsg').remove();

                if (response.success) {
                    // Mostrar mensaje de éxito
                    let mensaje = asistire
                        ? '✅ ¡Gracias por confirmar! Te esperamos.'
                        : '❌ Hemos registrado tu respuesta. Te contactaremos para reagendar.';

                    $('#confirmCard').find('.row.g-3').hide();

                    $('#confirmCard').append(`
                    <div class="mensaje-exito mt-3">
                        <div class="d-flex align-items-center">
                            <i class="ri-checkbox-circle-line fs-1 me-3"></i>
                            <div>
                                <h5 class="mb-1">¡Gracias por responder!</h5>
                                <p class="mb-0">${mensaje}</p>
                            </div>
                        </div>
                    </div>
                `);

                    // Iniciar countdown para redirección
                    iniciarCountdown();
                }
            },
            error: function(xhr) {
                $('#loadingMsg').remove();

                let errorMsg = 'Error al procesar tu confirmación. Por favor intenta de nuevo.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                $('#confirmCard').append(`
                <div class="mensaje-error mt-3">
                    <i class="ri-error-warning-line"></i> ${errorMsg}
                </div>
            `);

                // Rehabilitar botones
                $('.btn-confirmar, .btn-rechazar').prop('disabled', false);
            }
        });
    }

    function iniciarCountdown() {
        let segundos = 5;
        $('#countdownMessage').show();

        let interval = setInterval(function() {
            $('#countdownText').text(`Serás redirigido en ${segundos} segundos...`);
            segundos--;

            if (segundos < 0) {
                clearInterval(interval);
                window.location.href = '{{ route("cliente.mantenimiento.ver", $mantenimiento->token_cliente) }}';
            }
        }, 1000);
    }
</script>
</body>
</html>
