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
                                        <h4 class="fs-18 error-subtitle {{($transf->status==2)?' text-danger':' text-success'}} text-uppercase mb-0" >TRANSFERENCIA
                                            @if($transf->status == 2)
                                                RECHAZADA
                                            @endif
                                            @if($transf->status == 1)
                                                APROBADA
                                            @endif
                                        </h4>
                                        <p class="fs-15 text-muted mt-3">Datos de la transferencia</p>
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

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.footer')
    </div>
@endsection
