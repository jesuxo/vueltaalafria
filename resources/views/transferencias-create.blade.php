@extends('layouts.master')
@section('title')
    Ingresar transferencia
@endsection
@section('css')

    <!-- extra js -->
    <script>
        function  bancoSucursal(fksucursal){

            $('#bancosucursaldiv').html('');

            $.ajax({
                type: 'POST',
                url : '/sascursal/bancos',
                data:{fksucursal:fksucursal },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#bancosucursaldiv').html(response);

                }
            });

        }

    </script>

    <link rel="stylesheet" href="{{ URL::asset('build/libs/dropzone/dropzone.css') }}" type="text/css">
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
@endsection
@section('content')
    <x-breadcrumb title="Ingresar nueva" pagetitle="Transferencias" />
    <form id="createtransf-form" autocomplete="off" class="needs-validation" method="post" novalidate
          action="{{route('transferencias.store')}}">

        @method('POST')
        @csrf

    <div class="row">
        <div class="col-xl-9 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm">
                                <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-1 ">Informaci&oacute;n</h5>
                            <p class="text-muted mb-0"  >Ingrese los datos de la transferencia.</p>
                            <span class="alertatransferencia text-danger"></span>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <div>
                        <div class="d-flex align-items-start">
                            <div class="flex-grow-1">
                                <label class="form-label">Sucursal</label>
                            </div>

                        </div>
                        <div>
                            <select required class="form-control"   data-choices
                                    onchange="$('.error-msg').hide();  $('.datosocultos').fadeIn(); bancoSucursal(this.value)" name="fksucursal"  id="fksucursal">
                                <option value="">Seleccione</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{$sucursal->id}}">{{$sucursal->descrip}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="error-msg mt-1">Por favor, seleccione una sucursal de la empresa.</div>
                    </div>
                </div>
            </div>

            <div class="card datosocultos" style="display: none">
                    <div class="card-body border-bottom border-bottom-dashed p-4">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="mb-2">
                                    <label for="numero" class="form-label">Transf No</label>
                                    <input type="text" class="form-control" id="numero" name="numero" placeholder="Transferencia No"
                                           value=""  required >
                                    <div class="invalid-feedback">
                                        Nro de transferencia obligatorio
                                    </div>
                                </div>
                                <div class="mb-2" id="bancosucursaldiv" ></div>


                            </div>
                            <!--end col-->
                            <div class="col-lg-4 ms-auto">
                                <div class="mb-2">
                                    <label for="monto" class="form-label">Monto</label>
                                    <input type="text"  style="text-align: right"
                                           class="form-control" id="monto" placeholder="0.00" name="monto"
                                             required>
                                    <div class="invalid-feedback">
                                        Monto requerido
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label for="fecha" class="form-label">Fecha </label>
                                    <input type="text" class="form-control" id="fecha" data-provider="flatpickr"
                                           data-date-format="d/m/Y" name="fecha" data-time="true" required
                                           placeholder="Fecha de la transferencia" value="">
                                </div>


                            </div>
                            <div class="col-lg-4 ms-auto">
                                <div class="mb-2">
                                    <label for="titular" class="form-label">Cliente/Titular</label>
                                    <input type="text" required     class="form-control" id="titular" placeholder="Ej: Pedro Perez" name="titular">
                                    <div class="invalid-feedback">
                                        Monto requerido
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label for="observacion" class="form-label">Observaci&oacute;n</label>
                                    <textarea class="form-control"  name="observacion" id="observacion" rows="3" placeholder="Informacion adicional"  ></textarea>
                                </div>
                            </div>
                        </div>
                        <!--end row-->
                    </div>


                    <div class="card-body  ">

                        <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                            <span class="alertatransferencia text-danger"></span>
                            <button type="submit" class="btn btn-success"><i
                                    class="ri-printer-line align-bottom me-1"></i> Guardar
                            </button>

                        </div>
                    </div>


            </div>
        </div>
        <!--end col-->
        <div class="col-xl-3 col-lg-4"></div>
    </div>

    </form>
    <!--end row-->
@endsection
@section('scripts')
    <!-- dropzone min -->
    <script src="{{ URL::asset('build/libs/dropzone/dropzone-min.js') }}"></script>

    <!-- cleave.js -->
    <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>

    <!-- Sweet Alerts js -->
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script src="{{ URL::asset('build/js/backend/create-transferencia.init.js') }}?version={{rand(0,5000)}}"></script>

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
