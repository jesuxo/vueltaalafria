@extends('layouts.master')
@section('title')
    Mantenimientos - {{ $vehiculo ? $vehiculo->modelo : 'Todos los vehículos' }}
@endsection
@section('css')
    <style>
        .btn-soft-light:hover, .codclieseleted{
            background-color: #e0f2ff !important;
        }
        .nav-pills .nav-link {
            background: #eee !important;
            border-bottom-right-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
        .nav-pills .nav-link.active  {
            background: #0072c5 !important;
            border-bottom-right-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }
        .nav-pills{
            border-bottom: 1px solid #0072c5;
        }
        .tdline{
            border:1px solid #0072c5 !important;
            font-size: 12px;
        }
        .tdlineff{
            border-left:1px solid #fff !important;
            font-size: 12px;
            color: white !important;
            background-color: #0072c5 !important;
        }
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">
                            @if($vehiculo)
                                Mantenimientos de {{ (isset($vehiculo->marca))?$vehiculo->marca : 'Marca' }} {{ (isset($vehiculo->modelo))? $vehiculo->modelo  : 'Modelo' }} ({{ (iset($vehiculo->identificacion))? $vehiculo->identificacion: 'Placa' }})
                            @else
                                Historial de Mantenimientos - {{ $cliente->descrip }}
                            @endif
                        </h5>
                        @if($vehiculo)
                            <div class="flex-shrink-0">
                                <a href="{{ route('clientes.vehiculos.mantenimientos.create', [$cliente->codclie, $vehiculo->id]) }}"
                                   class="btn btn-success" style="display: none">Registrar Mantenimiento</a>
                                <a href="{{ url('/clientes/'.$cliente->codclie.'/tab3') }}"
                                   class="btn btn-secondary">Volver a Vehículos</a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                @if(!$vehiculo)
                                    <th>Vehículo</th>
                                @endif
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Kilometraje</th>
                                <th>Producto</th>
                               <!-- <th>Costo</th> -->
                                <th>Próximo</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($mantenimientos as $m)
                                <tr>
                                    @if(!$vehiculo)
                                        <td>{{ $m->vehiculo->marca }} {{ $m->vehiculo->modelo }} ({{ $m->vehiculo->identificacion }})</td>
                                    @endif
                                    <td>{{ $m->fechaformat  }}</td>
                                    <td>
                                        @switch($m->tipo_mantenimiento)
                                            @case('cambio_aceite') Cambio Aceite @break
                                            @case('cambio_filtro_aceite') Filtro Aceite @break
                                            @case('cambio_filtro_gasolina') Filtro Gasolina @break
                                            @case('cambio_filtro_aire') Filtro Aire @break
                                            @case('mantenimiento_inyectores') Inyectores @break
                                            @case('bateria') Batería @break
                                            @default Otros
                                        @endswitch
                                    </td>
                                    <td>{{ number_format($m->kilometraje, 0, ',', '.') }}</td>
                                    <td>{{ $m->producto_utilizado }}</td>
                                        <!-- <td>${{ number_format($m->costo, 2, ',', '.') }}</td>-->
                                    <td>
                                        @if($m->proximo_mantenimiento)
                                            {{ $m->proximo_mantenimiento->format('d/m/Y') }}
                                        @elseif($m->proximo_kilometraje)
                                            {{ number_format($m->proximo_kilometraje, 0, ',', '.') }} km
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('clientes.vehiculos.mantenimientos.show', [$cliente->codclie, $m->fk_vehiculo, $m->id]) }}"
                                           class="btn btn-sm btn-info">Ver</a>
                                        <a href="{{ route('clientes.vehiculos.mantenimientos.edit', [$cliente->codclie, $m->fk_vehiculo, $m->id]) }}"
                                           class="btn btn-sm btn-warning">Editar</a>
                                        <form action="{{ route('clientes.vehiculos.mantenimientos.destroy', [$cliente->codclie, $m->fk_vehiculo, $m->id]) }}"
                                              method="POST" style="display:inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('¿Eliminar este mantenimiento?')">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $vehiculo ? '6' : '7' }}" class="text-center">
                                        No hay mantenimientos registrados
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#busqueda').select();

            $('.openDocumento').unbind('click').bind('click',function () {

                var fksucu   = $(this).attr('data-fksucu');
                var numerod  = $(this).attr('data-numerod');
                var tipofac  = $(this).attr('data-tipofac');

                $('#documentView').html('<button class="btn btn-outline-primary btn-load"><span class="d-flex align-items-center"><span class="spinner-border flex-shrink-0" role="status"> <span class="visually-hidden"> Cargando...</span> </span> <span class="flex-grow-1 ms-2">Cargando... </span> </span> </button>');
                $.ajax({
                    type:'post',
                    data:{tipofac: (tipofac)? tipofac : '', numerod: (numerod)? numerod : '', fksucu: (fksucu)? fksucu : '' },
                    url:'/openDoc',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success:function(response) {
                        $('#documentView').html(response);
                    }
                });
            });

        });

        function checkSubmitVehiculos() {
            var campos = [
                '#year', '#marca', '#modelo', '#identificacion'
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
                alert('Complete los campos: ' + vacios.join(', '));
                return false;
            }

            return true;
        }


        document.querySelectorAll('input, textarea').forEach(input => {
            input.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    this.classList.add('error');
                } else {
                    this.classList.remove('error');
                }
            });
        });
    </script>
@endsection
