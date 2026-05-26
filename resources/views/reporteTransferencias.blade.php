@extends('layouts.master')
@section('title')
   REPORTE DE TRANSFERENCIAS
@endsection
@section('css')
    <style>
        .table-nowrap th, .table-nowrap td {
            white-space: unset !important;
        }
    .botoncal{
        background: transparent;
        border: none;
        color: white;
    }
    .botoncal:hover{
         font-size: 13px;
    }

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
    </style>
@endsection
@section('content')
    <style>
        .tdline{
            border:1px solid #0072c5 !important;

        }
        .tdlineff{
            border-left:1px solid #fff !important;

            color: white !important;
            background-color: #0072c5 !important;
        }
    </style>
    <div class="row">


        <div class="row ">
            <div class="col-lg-8 ">
                <div class="d-flex align-items-center flex-wrap gap-3 mb-4">
                    <ul class="nav nav-pills flex-grow-1 mb-0" role="tablist">

                        <li class="nav-item ">
                            <a class="nav-link active" onclick="tabchangeto('1')"  href="javascript:;" role="tab" id="tab1">
                                PENDIENTES ({{count($pendientes)}})
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" onclick="tabchangeto('2')"  href="javascript:;" role="tab" id="tab2">
                                APROBADAS ({{count($aprobadas)}})
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" onclick="tabchangeto('3')"  href="javascript:;" role="tab" id="tab3">
                                RECHAZADAS ({{count($rechazadas)}})
                            </a>
                        </li>
                    </ul>

                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="tabcontent1" role="tabpanel">
                        <div class="card"  >
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">TRANSFERENCIAS PENDIENTES</h4>
                                <small> {{ str_replace('to','al',$fechasreport) }} </small>
                            </div>
                            <div class="card-body" >
                                <div class="table-responsive table-card">
                                    <table width="100%" class="table table-nowrap align-middle">
                                    <tr>
                                        <td width="10%" class="p-2 text-center tdlineff">Fecha</td>
                                        <td width="10%" class="p-2 text-center tdlineff">Nro.Transf</td>
                                        <td width="30%" class="p-2 text-start  tdlineff">Titular</td>
                                        <td width="15%" class="p-2 text-center tdlineff">Banco</td>
                                        <td width="5%" class="p-2 text-center tdlineff">Sucursal</td>
                                        <td width="2%"  class="p-2 text-center tdlineff">Moneda</td>
                                        <td width="5%" class="p-2 text-center tdlineff">Monto</td>
                                        <td width="1%" class="p-2 text-center tdlineff"> </td>
                                    </tr>

                                    @if(isset($pendientes))
                                        @foreach($pendientes as $index => $transf)
                                            @php
$texto      = "Por favor revisar transferencia
Nro:       ".strtoupper($transf->numero)."
Fecha:   $transf->fechaformat
Monto:   $transf->currency ".number_format($transf->monto,2,',','.')."
Titular:   ".strtoupper($transf->titular)."
".$transf->banco->descrip."
".$transf->observacion."

Enlace: https://osoriogroup.com.ve/transferencias/validar/".$transf->hashid."
";
                                                $texto    = urlencode($texto);
                                                $telefono = "https://api.whatsapp.com/send?phone=".$transf->banco->telefono."&text=$texto";
                                                $enlace   = "https://osoriogroup.com.ve/transferencias/validar/".$transf->hashid;
                                            @endphp
                                            <tr>
                                                <td class="p-2 text-start tdline" style="font-size: 10px; !important;"valign="top">{{$transf->fechaformat}}</td>
                                                <td class="p-2 text-start tdline" style="font-size: 10px; !important;" valign="top">{{$transf->numero}}</td>
                                                <td class="p-2 text-start tdline" valign="top">{{$transf->titular}}
                                                    <br>
                                                    <span style="font-size: 10px; !important; color: #0072c5 !important; ">{{$transf->observacion}}</span>
                                                </td>
                                                <td class="p-2 text-start tdline" style="font-size: 10px; !important;"valign="top">{{$transf->banco->descrip}}
                                                </td>
                                                <td class="p-2 text-start tdline" style="font-size: 10px; !important;"valign="top">{{$transf->sucursal->descrip}}</td>
                                                <td class="p-2 text-center tdline" style="font-size: 10px; !important;"valign="top">{{$transf->currency}}</td>
                                                <td class="p-2 text-end   tdline" style="font-size: 10px; !important;"valign="top">{{number_format($transf->monto,2,',','.')}}</td>
                                                <td class="p-2 text-end   tdline" valign="top" >
                                                    <div class="dropdown">
                                                        <button class="btn btn-soft-primary btn-sm dropdown btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="ri-more-fill align-middle"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end" >
                                                            `  <li>
                                                                <a class="dropdown-item" target="_blank" href="{{$telefono}}">
                                                                    <i class="ri-download-2-line align-bottom me-2 text-muted"></i>
                                                                    Validaci&oacute;n
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" target="_blank" href="{{$enlace}}">
                                                                    Aprobaci&oacute;n
                                                                </a>
                                                            </li>
                                                            <li class="dropdown-divider"></li>
                                                            <li>
                                                                <a  onclick="$('#deleterecord').data('id',{{$transf->id}})" class="dropdown-item remove-item-btn" data-bs-toggle="modal" href="#deleteOrder">
                                                                    <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                                    Eliminar
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane " id="tabcontent2" role="tabpanel">
                        <div class="card"  >
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">TRANSFERENCIAS APROBADAS</h4>
                                <small> {{ str_replace('to','al',$fechasreport) }} </small>
                            </div>
                            <div class="card-body" >
                                <div class="table-responsive table-card">
                                    <table width="100%" class="table table-nowrap align-middle">

                                    <tr>
                                        <td width="10%" class="p-2 text-center tdlineff">Fecha</td>
                                        <td width="5%" class="p-2 text-center tdlineff">Nro.Transf</td>
                                        <td width="30%" class="p-2 text-start  tdlineff">Titular</td>
                                        <td width="15%" class="p-2 text-center tdlineff">Banco</td>
                                        <td width="15%" class="p-2 text-center tdlineff">Sucursal</td>
                                        <td width="2%"  class="p-2 text-center tdlineff">Moneda</td>
                                        <td width="10%" class="p-2 text-center tdlineff">Monto</td>
                                    </tr>

                                    @if(isset($aprobadas))
                                        @foreach($aprobadas as $index => $transf)
                                            <tr>
                                                <td class="p-2 text-start tdline" style="font-size: 10px; !important;"valign="top">{{$transf->fechaformat}}</td>
                                                <td class="p-2 text-start tdline" valign="top" style="font-size: 10px; !important;">{{$transf->numero}}</td>
                                                <td class="p-2 text-start tdline" valign="top">{{$transf->titular}}<br>
                                                <span style="font-size: 10px; !important; color: #0072c5 !important; ">{{$transf->observacion}}</span>
                                                </td>
                                                <td class="p-2 text-start tdline" style="font-size: 10px; !important;"valign="top">{{$transf->banco->descrip}}</td>
                                                <td class="p-2 text-start tdline" style="font-size: 10px; !important;"valign="top">{{$transf->sucursal->descrip}}</td>
                                                <td class="p-2 text-center tdline" style="font-size: 10px; !important;"valign="top">{{$transf->currency}}</td>
                                                <td class="p-2 text-end   tdline"  style="font-size: 10px; !important;"valign="top">{{number_format($transf->monto,2,',','.')}}</td>
                                            </tr>
                                        @endforeach
                                    @endif

                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane " id="tabcontent3" role="tabpanel">
                        <div class="card"  >
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">TRANSFERENCIAS RECHAZADAS</h4>
                                <small> {{ str_replace('to','al',$fechasreport) }} </small>
                            </div>
                            <div class="card-body" >
                                <div class="table-responsive table-card">
                                    <table width="100%" class="table table-nowrap align-middle">
                                    <tr>
                                        <td width="10%" class="p-2 text-center tdlineff">Fecha</td>
                                        <td width="5%" class="p-2 text-center tdlineff">Nro.Transf</td>
                                        <td width="30%" class="p-2 text-start  tdlineff">Titular</td>
                                        <td width="15%" class="p-2 text-center tdlineff">Banco</td>
                                        <td width="15%" class="p-2 text-center tdlineff">Sucursal</td>
                                        <td width="2%"  class="p-2 text-center tdlineff">Moneda</td>
                                        <td width="10%" class="p-2 text-center tdlineff">Monto</td>
                                    </tr>

                                    @if(isset($rechazadas))
                                        @foreach($rechazadas as $index => $transf)
                                            <tr>
                                                <td class="p-2 text-start tdline" style="font-size: 10px; !important;"valign="top">{{$transf->fechaformat}}</td>
                                                <td class="p-2 text-start tdline" style="font-size: 10px; !important;"valign="top" valign="top" style="font-size: 10px; !important;">{{$transf->numero}}</td>
                                                <td class="p-2 text-start tdline" style="font-size: 10px; !important;"valign="top">{{$transf->titular}}
                                                    <br>
                                                    <span style="font-size: 10px; !important;color: red !important;">{{$transf->observacion}}</span>
                                                </td>
                                                <td class="p-2 text-start tdline"style="font-size: 10px; !important;" valign="top">{{$transf->banco->descrip}}</td>
                                                <td class="p-2 text-start tdline" style="font-size: 10px; !important;"valign="top">{{$transf->sucursal->descrip}}</td>
                                                <td class="p-2 text-center tdline"style="font-size: 10px; !important;" valign="top">{{$transf->currency}}</td>
                                                <td class="p-2 text-end   tdline" style="font-size: 10px; !important;" valign="top">{{number_format($transf->monto,2,',','.')}}</td>
                                            </tr>
                                        @endforeach
                                    @endif

                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-md-12 mb-4">
                    <form  method="post" name="form1" id="form1" action="{{route('reportetransferencias')}}">
                        <select class="form-select" data-choices  onchange="$('#form1').submit()"
                                id="status" name="status">
                            <option  {{( isset($status) and  $status == '0' )? 'selected':''}} value="0">Pendientes</option>
                            <option  {{( isset($status) and  $status == '1' )? 'selected':''}} value="1">Aprobadas</option>
                            <option  {{( isset($status) and  $status == '2' )? 'selected':''}} value="2">Rechazadas</option>
                            <option  {{( isset($status) and  $status == '' )? 'selected':''}} value="">Todas</option>

                        </select>

                        <input placeholder="Buscar: Referencia, Banco, Titular..." class="form-control"
                               type="text" style="margin-bottom: 20px; height: 40px; width: 99%; "
                               value="{{(isset($busquedatransf) and $busquedatransf !='')? $busquedatransf : ''}}"
                               onchange="$('#form1').submit()"
                               name="busquedatransf" id="busquedatransf">

                        <input type="hidden" value="{{$selectbanco}}" name="selectbanco" id="selectbanco">

                        <div class="input-group mb-4">
                            <input type="text" class="form-control" data-provider="flatpickr"
                                   data-range-date="true" data-date-format="d/m/Y"
                                   placeholder="Puede tambien seleccionar rango de fecha"
                                   data-deafult-date="" name="fechasreport" readonly="readonly" value="{{$fechasreport}}"
                            >
                            <div class="input-group-text bg-primary border-primary text-white">
                                <button type="submit" class="botoncal" >Consultar</button>
                            </div>
                        </div>

                        @csrf
                        @method('POST')
                    </form>
                    @foreach($sucursales as $indexsucu => $sucursal)
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">{{$sucursal}}</h4>
                                    <span class="badge badge-soft-dark float-end">{{ $arraysucu[$indexsucu]['cant'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>

                    <hr>
                    <div class="row">
                        @foreach($bancos as $indexbanco => $banco)
                            <div class="col-sm-12 col-lg-6 ">
                                <div class="card">
                                    <a href="javascript:;"
                                       onclick="
                                               @if(isset($selectbanco) and $selectbanco > 0  and $indexbanco == $selectbanco)
                                                  $('#selectbanco').val(0);
                                               @else
                                                  $('#selectbanco').val({{$indexbanco}});
                                               @endif
                                        $('#form1').submit()"

                                       class="card-header align-items-center d-flex {{(isset($selectbanco) and $selectbanco > 0 and $indexbanco == $selectbanco)? ' tdlineff ': '  '}} ">
                                        <h4 class="card-title mb-0 flex-grow-1">{{$banco['descrip']}}</h4>
                                        <span class="badge {{(isset($selectbanco) and $selectbanco > 0  and $indexbanco == $selectbanco)? ' badge-soft-light ': ' badge-soft-dark '}} float-end">{{ $banco['cant']}}</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>

    </div>

    <div class="modal fade flip" id="deleteOrder" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-5 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                               colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px">
                    </lord-icon>
                    <div class="mt-4 text-center">
                        <h4>Desea eliminar esta transferencia?</h4>
                        <p class="text-muted fs-15 mb-4">
                            Borrando este registro ud eliminar&aacute; la informaci&oacute;n de la base de datos </p>
                        <div class="hstack gap-2 justify-content-center remove">
                            <button class="btn btn-link link-success fw-medium text-decoration-none"
                                    id="deleteRecord-close" data-bs-dismiss="modal"><i
                                    class="ri-close-line me-1 align-middle"></i> Cancelar</button>
                            @if( auth()->user()->can('menu_transferencias_eliminar') )
                                <button class="btn btn-danger" id="deleterecord" data-id="">Si, Eliminar</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js?'.rand(0,5555555)) }}"></script>

    <script>
        function tabchangeto(number){
            $('.nav-link').removeClass('active');
            $('#tab'+number).addClass('active');
            $('.tab-pane').removeClass('active');
            $('#tabcontent'+number).addClass('active');
        }

        $('#deleterecord').unbind('click').bind('click',function () {
            var id = $(this).data('id');

            $.ajax({
                type: 'DELETE',
                url: '/transferencias/'+id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    var deleted = data.deleted;

                    if(deleted == 1) {
                        $('#tr'+id).hide();
                        $("#deleteRecord-close").click();
                        $('#form1').submit()
                    }else{
                        console.log('no se pudo eliminar');
                    }
                }
            });
        });
    </script>

@endsection
