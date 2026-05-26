@extends('layouts.master-auth')
@section('title')
    Bienvenid@
@endsection
@section('css')
    <style>
        .error {
            border: 2px solid red !important;
            background-color: #ffe6e6;
        }

        .error:focus {
            outline: none;
            border-color: #ff0000;
            box-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
        }
    </style>
@endsection
@section('content')

    <div class="w-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="auth-card mx-lg-3">
                        <div class="card border-0 mb-0">

                            <div class="card-body">
                                <div class="p-2">
                                    <form class="needs-validation" novalidate method="POST"
                                          action="{{ route('register') }}"  id="form11" name="form11"
                                          onsubmit="return checkSubmitDatos()"
                                          autocomplete="off" class="needs-validation registro-form"    >

                                        @method('post')
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="first_name" class="form-label">C&eacute;dula / RIF
                                                        <span    class="text-danger">*</span>
                                                    </label>
                                                    <input id="cedula" type="text"
                                                        class="form-control @error('cedula') is-invalid @enderror"
                                                        name="cedula" value="{{ old('cedula') }}" required
                                                        autocomplete="cedula" autofocus
                                                        placeholder="Ingresa cedula/rif">
                                                    @error('cedula')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Nombre / Raz&oacute;n social
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input id="name" type="text"
                                                        class="form-control @error('name') is-invalid @enderror"
                                                        name="name" value="{{ old('name') }}" required
                                                        autocomplete="name" autofocus
                                                        placeholder="Ingresa tu apellido">
                                                    @error('name')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                        </div>


                                        <div class="mt-4 col-md-12"  >
                                            <button class="btn btn-success  w-100" type="submit"  >Enviar Datos</button>
                                        </div>


                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>


        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">©
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> Hecho con <i class="mdi mdi-heart text-danger"></i>  por <a href="https://CelisWeb.com.ve" target="_blank"> CelisWeb </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
@endsection

@section('scripts')
    <script>
        function checkSubmitDatos() {
            var campos = [
                '#cedula', '#name'
            ];

            var vacios = [];

            $(campos.join(',')).each(function() {
                if (!$(this).val().trim()) {
                    vacios.push($(this).attr('name'));
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });

            if (vacios.length > 0) {
                return false;
            }

            return true;
        }
        //$('#form11').attr('action','{{ route('register') }}');$('#form11').submit()
    </script>
@endsection
