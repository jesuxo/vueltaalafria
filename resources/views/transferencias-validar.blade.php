@extends('layouts.master-auth')
@section('title')
    Transferencia nro {{$transf->numero}}
@endsection
@section('css')
    <!-- extra css -->
@endsection
@section('content')
    <div class="w-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="auth-card mx-lg-3">
                        <div class="card border-0 mb-0">
                            <div class="card-body text-center p-4">

                                <div class="text-center px-sm-5 mx-5">
                                    <img src="{{ URL::asset('build/images/transf.jpg') }}" class="img-fluid" alt="">
                                </div>
                                <div class="mt-4 text-center pt-3">
                                    <div class="position-relative">
                                        <h4 class="fs-18 error-subtitle text-uppercase mb-0">Validaci&oacute;n de Transferencia</h4>
                                        <p class="fs-15 text-muted mt-3">Por favor, valide los siguientes datos</p>
                                        <style>
                                            .datos td{
                                                padding: 5px;
                                            }
                                        </style>
                                        <table width="80%"  style="margin:auto; " class="datos">
                                            <tr> <td width="30%" align="left">Nro:    </td><td align="left" width="70%">{{$transf->numero}}</td>  </tr>
                                            <tr bgcolor="#f3f6f9"> <td width="30%" align="left">Fecha:  </td><td align="left"  >{{$transf->fechaformat}}</td>  </tr>
                                            <tr> <td width="30%" align="left">Monto:  </td><td align="left"  >{{$transf->currency.number_format($transf->monto,2,',','.')}}</td>  </tr>
                                            <tr bgcolor="#f3f6f9"> <td width="30%" align="left">Titular:</td><td align="left"  >{{$transf->titular}}</td>  </tr>
                                            <tr bgcolor="#f3f6f9"> <td colspan="2" width="30%" align="left"> {{$transf->banco->descrip}}</td>  </tr>
                                        </table>
                                        <div class="mt-4">
                                            @if(\Illuminate\Support\Facades\Auth::user() and  auth()->user()->can('menu_transferencias_aprobar') )
                                            <a data-bs-toggle="modal" href="#rechazarModal"  class="btn btn-danger"><i
                                                    class="mdi mdi-close me-1"></i>Rechazar</a>
                                            <a data-bs-toggle="modal" href="#aprobarModal" class="m-2 btn btn-success"><i
                                                    class="mdi mdi-check me-1"></i>Aprobar</a>
                                            @else
                                                <a   href="#"  class="btn btn-danger"><i
                                                        class="mdi mdi-close me-1"></i>SOLICITE PERMISO REQUERIDO</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade flip" id="aprobarModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-5 text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                   colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px">
                        </lord-icon>
                        <div class="mt-4 text-center">
                            <h4>Confirmaci&oacute;n</h4>
                            <p class="text-muted fs-15 mb-4">
                                <b>APRUEBA</b> que esta disponible esta transferencia?</p>
                            <div class="hstack gap-2 justify-content-center remove">
                                <button class="btn btn-link link-success fw-medium text-decoration-none"
                                        id="deleteRecord-close" data-bs-dismiss="modal"><i
                                        class="ri-close-line me-1 align-middle"></i> Atras</button>
                                <button class="btn btn-success botonstatus"  data-va="1">Si, Apruebo</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade flip" id="rechazarModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body p-5 text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                   colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px">
                        </lord-icon>
                        <div class="mt-4 text-center">
                            <h4>Confirmaci&oacute;n</h4>
                            <p class="text-muted fs-15 mb-4">
                                Esta seguro que desea <b>RECHAZAR</b> esta transferencia? </p>
                            <div class="hstack gap-2 justify-content-center remove">
                                <button class="btn btn-link link-danger fw-medium text-decoration-none"
                                        id="deleteRecord-close" data-bs-dismiss="modal"><i
                                        class="ri-close-line me-1 align-middle"></i> Atras</button>
                                <button class="btn btn-danger botonstatus"  data-va="2">Si, Seguro</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.footer')
    </div>
@endsection

@section('scripts')
    <script>
        $('.botonstatus').unbind('click').bind('click',function () {
            var va = $(this).data('va');
            var id = '{{$transf->hashid}}';

            $.ajax({
                type: 'GET',
                url: '/transferencias/cambiarstatus/'+id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{id: id, va: va},
                success: function (data) {
                    var cambiado = data.cambiado;

                    if(cambiado)
                        window.location.href='/transferencias/validar/'+id;

                }
            });
        });
    </script>

@endsection
