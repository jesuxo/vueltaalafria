{{-- resources/views/home/registration/success.blade.php --}}
@extends('home.layouts.master')

@section('title', 'Inscripción Exitosa - Vuelta a la Fría 2026')

@section('content')
    <div class="container">
        <div class="success-box" data-aos="fade-up">
            <i class="fas fa-check-circle fa-5x mb-4"></i>
            <h2 class="mb-3">¡Inscripción Exitosa!</h2>
            <p class="lead">Hemos recibido la inscripción de tu equipo</p>

            <div class="row justify-content-center mt-5">
                <div class="col-md-8">
                    <div class="card bg-white text-dark">
                        <div class="card-body">
                            <h4>Detalles de la inscripción:</h4>
                            <hr>
                            <p><strong>Equipo:</strong> {{ session('team_name') }}</p>
                            <p><strong>Atletas registrados:</strong> {{ session('athletes_count') }}</p>
                            <p><strong>Código de acceso al panel:</strong></p>
                            <div class="access-code">{{ session('access_code') }}</div>
                            <p class="mt-3 small">Guarda este código para acceder al panel de gestión de tu equipo</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <a href="{{ route('team.login') }}" class="btn-custom">
                    <i class="fas fa-sign-in-alt me-2"></i> Acceder al Panel
                </a>
                <a href="{{ route('home') }}" class="btn-outline-custom ms-3">
                    <i class="fas fa-home me-2"></i> Volver al Inicio
                </a>
            </div>
        </div>
    </div>
@endsection
