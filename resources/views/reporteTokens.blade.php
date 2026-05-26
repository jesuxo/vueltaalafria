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
            <div class="col-lg-2">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <form  method="post" name="form1" id="form1" action="{{route('reportetokens')}}">

                            <input placeholder="Buscar: TOKEN ..." class="form-control"
                                   type="text" style="margin-bottom: 20px; height: 40px; width: 99%; "
                                   value="{{(isset($busquedatoken) and $busquedatoken !='')? $busquedatoken : ''}}"
                                   onchange="$('#form1').submit()"
                                   name="busquedatoken" id="busquedatoken">

                            <div class="input-group mb-4" >
                                <div class="input-group-text bg-primary border-primary text-white" style="width: 100%">
                                    <button type="submit" class="botoncal" style="margin: auto" >Consultar</button>
                                </div>
                            </div>
                            @csrf
                            @method('POST')
                        </form>

                    </div>


                </div>
            </div>
            <div class="col-lg-10 ">
                <div class="d-flex align-items-center flex-wrap gap-3 mb-4">
                    <ul class="nav nav-pills flex-grow-1 mb-0" role="tablist">

                        <li class="nav-item ">
                            <a class="nav-link active" onclick="tabchangeto('1')"  href="javascript:;" role="tab" id="tab1">
                                PENDIENTES ({{count($pendientes)}})
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" onclick="tabchangeto('2')"  href="javascript:;" role="tab" id="tab2">
                                USADOS ({{count($usados)}})
                            </a>
                        </li>

                    </ul>

                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="tabcontent1" role="tabpanel">
                        <div class="card"  >
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">TOKENS PENDIENTES</h4>
                            </div>
                            <div class="card-body" >
                                <div class="table-responsive table-card">
                                    <table width="100%" class="table table-nowrap align-middle">
                                        <tr>
                                            <td width="10%" class="p-2 text-center tdlineff">Fecha</td>
                                            <td width="10%" class="p-2 text-center tdlineff">Token</td>
                                            <td width="50%" class="p-2 text-center tdlineff">Observaci&oacute;n/Comentario</td>
                                            <td width="50%" class="p-2 text-center tdlineff">Sucursal</td>
                                            <td width="1%" class="p-2 text-center tdlineff"> </td>
                                        </tr>

                                        @if(isset($pendientes))
                                            @foreach($pendientes as $index => $token)

                                                <tr>
                                                    <td class="p-2 text-start tdline" style="font-size: 10px; !important;"valign="top">{{$token->fechaformat}}</td>
                                                    <td class="p-2 text-start tdline" style="font-size: 10px; !important;" valign="top">
                                                        @if($token->token !='')
                                                            {{$token->token}}
                                                        @else
                                                            <a onclick="$('#tokenid').val({{$token->id}});"
                                                               data-bs-toggle="modal" href="#updatetoken">  Editar</a>
                                                        @endif
                                                    </td>
                                                    <td class="p-2 text-start tdline" style="font-size: 10px; !important;" valign="top">{{$token->obs}}</td>
                                                    <td class="p-2 text-start tdline" style="font-size: 10px; !important;" valign="top">{{$token->sucursal->descrip}}</td>

                                                    <td class="p-2 text-end   tdline" valign="top" >
                                                        <div class="dropdown">
                                                            <button class="btn btn-soft-primary btn-sm dropdown btn-icon" type="button"
                                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-more-fill align-middle"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end" >
                                                                <li>
                                                                    <a  onclick="$('#deleterecord').data('id',{{$token->id}})" class="dropdown-item remove-item-btn"
                                                                        data-bs-toggle="modal" href="#deleteOrder">
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
                                <h4 class="card-title mb-0 flex-grow-1">TOKENS USADOS</h4>

                            </div>
                            <div class="card-body" >
                                <div class="table-responsive table-card">
                                    <table width="100%" class="table table-nowrap align-middle">

                                        <tr>
                                            <td width="10%" class="p-2 text-center tdlineff">Fecha</td>
                                            <td width="5%" class="p-2 text-center tdlineff">Token</td>
                                            <td width="30%" class="p-2 text-start  tdlineff">Usuario</td>
                                            <td width="30%" class="p-2 text-start  tdlineff">Observaci&oacute;n/Comentario</td>
                                        </tr>

                                        @if(isset($usados))
                                            @foreach($usados as $index => $token)
                                                <tr>
                                                    <td class="p-2 text-start tdline" style="font-size: 10px; !important;"valign="top">{{$token->fechaformat}}</td>
                                                    <td class="p-2 text-start tdline" valign="top" style="font-size: 10px; !important;">{{$token->token}}</td>
                                                    <td class="p-2 text-start tdline" valign="top" style="font-size: 10px; !important;">{{$token->codusua}}  </td>
                                                    <td class="p-2 text-start tdline" valign="top" style="font-size: 10px; !important;">{{$token->obs}}  </td>
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
                        <h4>Desea eliminar este token?</h4>
                        <p class="text-muted fs-15 mb-4">
                            Borrando este registro ud eliminar&aacute; la informaci&oacute;n de la base de datos </p>
                        <div class="hstack gap-2 justify-content-center remove">
                            <button class="btn btn-link link-success fw-medium text-decoration-none"
                                    id="deleteRecord-close" data-bs-dismiss="modal"><i
                                    class="ri-close-line me-1 align-middle"></i> Cancelar</button>
                            @if( auth()->user()->can('menu_token_eliminar') )
                                <button class="btn btn-danger" id="deleterecord" data-id="">Si, Eliminar</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade flip" id="updatetoken" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-5 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                               colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px">
                    </lord-icon>
                    <div class="mt-4 text-center">
                        <form name="form22" id="form22" method="POST" action="/token/update">
                            @csrf @method('POST')
                            <input type="hidden" name="tokenid" value="" id="tokenid"/>
                            <h4>Editar Token</h4>
                            <p class="text-muted fs-15 mb-4">
                                Puede escribir cualquier cadena de texto incluyendo numeros, no debe contener espacios ni simbolos especiales
                            </p>
                            <div class="input-group">
                                <input type="text" class="form-control" onclick="$('#btncopy').html('Copiar Token');" name="token" id="tokenval"   value=""   >
                                <div class="input-group-text bg-primary border-primary text-white">
                                    <button type="submit" class="botoncal" >Guardar</button>
                                </div>
                            </div>
                            <button onclick="copyText('tokenval')" type="button"
                                    class="btn btn-primary mt-3 text-white"  style="width: 100%" id="btncopy">Copiar Token</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('scripts')

    <script src="{{ URL::asset('build/js/app.js?'.rand(0,5555555)) }}"></script>

    <script>



        function copyText(copyInput) {
            const input = document.getElementById(copyInput);

            input.select();
            input.setSelectionRange(0, 99999);

            try {
                const copied = document.execCommand('copy');
                if (copied) {
                    $("#btncopy").html('Token Copiado <i class="bi bi-check-circle"></i>');
                } else {
                    // If execCommand fails, try to help user copy manually
                    input.focus();
                    alert('Please press Ctrl+C to copy the selected text');
                }
            } catch (err) {
                console.error('Copy error:', err);
                // Show text in alert as last resort
                alert('Text to copy: ' + input.value);
            }

            // Remove selection
            window.getSelection().removeAllRanges();
        }



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
                url: '/tokens/'+id,
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
