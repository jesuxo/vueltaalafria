@extends('layouts.master')
@section('title')
   Clientes
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
        @if(Auth::user() and auth()->user()->type == 'admin')
            <div class="col-xxl-3">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0" id="addCategoryLabel">Buscar Clientes </h6>
                    </div>
                    <div class="card-body">
                        <form action="/clientes" method="post"
                              autocomplete="off" class="needs-validation createCategory-form" id="clientForm" novalidate>
                            @method('post')
                            @csrf
                            <input type="hidden" id="codclie" name="codclie" class="form-control" value="">
                            <div class="row">
                                <div class="col-xxl-12  ">
                                    <div class="search-box mb-3">
                                        <input type="text" class="form-control search" id="busqueda" name="busqueda"value="{{(isset($busqueda))? $busqueda : ''}}"  required placeholder="Puede buscar por nombre, cedula ...">
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                    <div class="invalid-feedback">Ingrese la descripci&oacute;n de la instancia</div>
                                </div>

                                <div class="col-xxl-12 col-lg-6">
                                    @if($busqueda !='')
                                        <div class="accordion accordion-flush filter-accordion">
                                        <div class="card-body border-bottom p-0">
                                            <div>
                                                <p class="text-muted fs-13 mb-3">Resultados para: {{$busqueda}}</p>
                                                @foreach($clientes as $cli)
                                                    <a href="javascript:;" onclick="$('#codclie').val('{{$cli->codclie}}'); $('#clientForm').submit()"
                                                       class="card btn btn-soft-light  card-animate d-flex p-2 {{(isset($codclie) and $codclie !='' and $codclie == $cli->codclie)? 'codclieseleted' : ''}}
                                                       border-bottom border-bottom-dashed  cursor-pointer"
                                                       style="text-align: left" >
                                                        <div class="flex-grow-1">
                                                            <h5>{{$cli->descrip}}</h5>
                                                            <p class="text-muted mb-0">{{$cli->codclie}}</p>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-xxl-{{  (Auth::user() and auth()->user()->type == 'admin')? '9': '12'}}">

            @if(isset($cliente) and isset($cliente->descrip))
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">{{$cliente->descrip}}</h5>
                                <div class="flex-shrink-0">
                                <p class="mb-0">Cedula: <b>{{$cliente->id3}}</b></p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($tab == 'tab1')
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-sm mb-0">
                                            <tbody>
                                            @if(isset($cliente->direc1) and strlen($cliente->direc1) > 2)
                                            <tr bgcolor="#eee">
                                                <td width="25%">
                                                    Direccion1
                                                </td>
                                                <td width="75%" class="fw-medium">
                                                    {{$cliente->direc1}}
                                                </td>
                                            </tr>
                                            @endif
                                            @if(isset($cliente->direc2) and strlen($cliente->direc2) > 2)
                                            <tr>
                                                <td>
                                                    Direccion2
                                                </td>
                                                <td class="fw-medium">
                                                    {{$cliente->direc2}}
                                                </td>
                                            </tr>
                                            @endif
                                            @if(isset($cliente->email) and strlen($cliente->email) > 2)
                                            <tr bgcolor="#eee">
                                                <td>
                                                    Email
                                                </td>
                                                <td class="fw-medium">
                                                    {{$cliente->email}}
                                                </td>
                                            </tr>
                                            @endif
                                            @if(isset($cliente->telef) and strlen($cliente->telef) > 2)
                                            <tr>
                                                <td>
                                                    Telfono
                                                </td>
                                                <td class="fw-medium">
                                                    {{$cliente->telef}}
                                                </td>
                                            </tr>
                                            @endif
                                            @if(isset($cliente->movil) and strlen($cliente->movil) > 2)
                                            <tr bgcolor="#eee">
                                                <td>
                                                    Celular
                                                </td>
                                                <td class="fw-medium">
                                                    {{$cliente->movil}}
                                                </td>
                                            </tr>
                                            @endif
                                            @if(isset($cliente->codzona) and strlen($cliente->codzona) > 1)
                                            <tr>
                                                <td>
                                                    Zona
                                                </td>
                                                <td class="fw-medium">
                                                    {{$cliente->codzona}}
                                                </td>
                                            </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <div class="table-responsive">
                                        <table class="table table-borderless table-sm mb-0">
                                            <tbody>
                                            @if(isset($cliente->tipopvp) and $cliente->tipopvp > 0)
                                            <tr bgcolor="#eee">
                                                <td  width="25%" >
                                                    Tipo Precio
                                                </td>
                                                <td  width="75%" class="fw-medium">
                                                    PRECIO{{$cliente->tipopvp}}
                                                </td>
                                            </tr>
                                            @endif
                                            @if(isset($cliente->codvend)  and strlen($cliente->codvend) > 2)
                                            <tr>
                                                <td>
                                                    Vendedor Asig:
                                                </td>
                                                <td class="fw-medium">
                                                    {{$cliente->codvend}}
                                                </td>
                                            </tr>
                                            @endif
                                            @if(isset($cliente->clase)  and strlen($cliente->clase) > 2)
                                            <tr bgcolor="#eee">
                                                <td>
                                                    Clase
                                                </td>
                                                <td class="fw-medium">
                                                    {{$cliente->clase}}
                                                </td>
                                            </tr>
                                            @endif
                                            @if(isset($cliente->represent)  and strlen($cliente->represent) > 2)
                                            <tr>
                                                <td>
                                                    Representante
                                                </td>
                                                <td class="fw-medium">
                                                    {{$cliente->represent}}
                                                </td>
                                            </tr>
                                            @endif
                                            @if(isset($cliente->fax)  and strlen($cliente->fax) > 2)
                                            <tr  bgcolor="#eee">
                                                <td>
                                                    Fax
                                                </td>
                                                <td class="fw-medium">
                                                    {{$cliente->fax}}
                                                </td>
                                            </tr>
                                            @endif
                                            @if(isset($cliente->Estado)  and $cliente->Estado >0)
                                            <tr>
                                                <td>
                                                    Estado
                                                </td>
                                                <td class="fw-medium">
                                                    {{$cliente->Estado}}
                                                </td>
                                            </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex align-items-center flex-wrap gap-3 mb-4">
                            <ul class="nav nav-pills flex-grow-1 mb-0" role="tablist">
                                @if(Auth::user() and auth()->user()->type == 'admin')
                                    <li class="nav-item ">
                                        <a class="nav-link {{ ($tab == 'tab1')? 'active': '' }}"   href="/clientes/{{$cliente->codclie}}/tab1" role="tab">
                                            Ultimas Facturas
                                        </a>
                                    </li>
                                    <li class="nav-item ">
                                        <a class="nav-link {{ ($tab == 'tab2')? 'active': '' }}"   href="/clientes/{{$cliente->codclie}}/tab2" role="tab">
                                            Estado de cuenta
                                        </a>
                                    </li>
                                @endif
                                <li class="nav-item ">
                                    <a class="nav-link {{ ($tab == 'tab3')? 'active': '' }}"   href="/clientes/{{$cliente->codclie}}/tab3" role="tab">
                                        Vehiculos
                                    </a>
                                </li>
                            </ul>
                            <div class="flex-shrink-0" style="{{ ($tab == 'tab3')? '' : 'display:none'}}">
                                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#vehiculoModal"  class="btn btn-success">Agregar Veh&iacute;culo</a>
                            </div>
                        </div>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            @if(Auth::user() and auth()->user()->type == 'admin')
                                @if($tab == 'tab1')
                                <div class="tab-pane active" id="facturas" role="tabpanel">
                                    <div class="card">

                                        <div class="card-body">
                                            <div class="table-responsive table-card mb-1">
                                                <table width="100%" border="0" class=" table align-middle table-nowrap ">
                                                    <tr bgcolor="#fff">
                                                        <td width="14%" height="30"align="center" class="tdline" >VENTAS  </td>
                                                        <td width="3%" align="center" class="tdlineff" > FECHA</td>
                                                        <td width="3%" align="center" class="tdlineff" > DOC</td>
                                                        <td width="6%" align="center" class="tdlineff" > BS</td>
                                                        <td width="5%" align="center" class="tdlineff" > BS.T</td>
                                                        <td width="6%" align="center" class="tdlineff" > USD</td>
                                                        <td width="5%" align="center" class="tdlineff" > USD.T</td>
                                                        <td width="6%" align="center" class="tdlineff ocultarcop" > COP</td>
                                                        <td width="6%" align="center" class="tdlineff ocultarcopt" > COP.T</td>
                                                        <td width="5%" align="center" class="tdlineff ocultareur" > EUR</td>
                                                        <td width="5%" align="center" class="tdlineff" > ANTICIPOS </td>
                                                        <td width="5%" align="center" class="tdlineff" > CREDITO </td>
                                                        <td width="6%" align="center" class="tdlineff" > TOTAL USD</td>
                                                    </tr>

                                                    @php $n=0;
                                            $tcancele    =0;
                                            $tcancelt    =0;
                                            $tdolares    =0;
                                            $ttransf     =0;
                                            $tpesos      =0;
                                            $tpeso_tranf =0;
                                            $teuros      =0;
                                            $tcredito    =0;
                                            $tcancelaUSD =0;
                                            $ttotalventa =0;
                                                    @endphp
                                                    @foreach($listado as $nrounico => $sucursal)
                                                        @php
                                                            $tcancele    += $listado[$nrounico]['cancele'];
                                                            $tcancelt    += $listado[$nrounico]['cancelt'];
                                                            $tdolares    += $listado[$nrounico]['dolares'];
                                                            $ttransf     += $listado[$nrounico]['transf'];
                                                            $tpesos      += $listado[$nrounico]['pesos'];
                                                            $tpeso_tranf += $listado[$nrounico]['peso_tranf'];
                                                            $teuros      += $listado[$nrounico]['euros'];
                                                            $tcredito    += $listado[$nrounico]['credito'];
                                                            $tcancelaUSD += $listado[$nrounico]['cancelaUSD'];
                                                            $ttotalventa += $listado[$nrounico]['totalventa'];
                                                        @endphp
                                                        <tr  >
                                                            <td height="30"align="left" >
                                                                <div style=" max-width: 99%;  width: 100%; height: 20px; overflow: hidden; font-size: 12px">   {{$listado[$nrounico]['cliente']}}</div>
                                                            </td>
                                                            <td align="right" ><a href="javascript:;"  data-bs-toggle="modal" data-bs-target="#documentModal" class="openDocumento" data-fksucu="{{ $listado[$nrounico]['fk_sucu'] }}" data-numerod="{{ $listado[$nrounico]['numerod'] }}" data-tipofac="{{ $listado[$nrounico]['tipofac'] }}"  > {{($listado[$nrounico]['fecha'] !='')? $listado[$nrounico]['fecha']    : ''}} </a> </td>
                                                            <td align="right" ><a href="javascript:;"  data-bs-toggle="modal" data-bs-target="#documentModal" class="openDocumento" data-fksucu="{{ $listado[$nrounico]['fk_sucu'] }}" data-numerod="{{ $listado[$nrounico]['numerod'] }}" data-tipofac="{{ $listado[$nrounico]['tipofac'] }}"   > {{($listado[$nrounico]['numerod'] !='')? $listado[$nrounico]['numerod']        : ''}} </a> </td>
                                                            <td align="right" >  {{($listado[$nrounico]['cancele']!=0)?number_format($listado[$nrounico]['cancele'],2,',','.') : ''}}</td>
                                                            <td align="right" >  {{($listado[$nrounico]['cancelt']!=0)?number_format($listado[$nrounico]['cancelt'],2,',','.') : ''}}</td>
                                                            <td align="right" >  {{($listado[$nrounico]['dolares']!=0)?number_format($listado[$nrounico]['dolares'],2,',','.') : ''}}</td>
                                                            <td align="right" >  {{($listado[$nrounico]['transf'] !=0)?number_format($listado[$nrounico]['transf'] ,2,',','.') : ''}}</td>
                                                            <td align="right" class=" ocultarcop" >  {{($listado[$nrounico]['pesos']  !=0)?number_format($listado[$nrounico]['pesos']  ,2,',','.') : ''}}</td>
                                                            <td align="right" class=" ocultarcopt" >  {{($listado[$nrounico]['peso_tranf']!=0)?number_format($listado[$nrounico]['peso_tranf'],2,',','.') : ''}}</td>
                                                            <td align="right" class=" ocultareur" >  {{($listado[$nrounico]['euros']!=0)?number_format($listado[$nrounico]['euros'],2,',','.') : ''}}</td>
                                                            <td align="right" class=" " >  {{($listado[$nrounico]['cancelaUSD']!=0)?number_format($listado[$nrounico]['cancelaUSD'],2,',','.') : ''}}</td>
                                                            <td align="right" >  {{($listado[$nrounico]['credito']!=0)?number_format($listado[$nrounico]['credito'],2,',','.') : ''}}</td>
                                                            <td align="right" >  {{($listado[$nrounico]['totalventa']!=0)?number_format($listado[$nrounico]['totalventa'],2,',','.') : ''}}</td>
                                                        </tr>
                                                        @php $n++; @endphp
                                                    @endforeach

                                                    <tr bgcolor="#eee" style=" display: none">
                                                        <td height="30"align="left" > TOTALES</td>
                                                        <td align="left" ></td>
                                                        <td align="left" ></td>
                                                        <td align="right" >{{ ($tcancele!=0)? number_format($tcancele,2,',','.') : ''}}     </td>
                                                        <td align="right" >{{ ($tcancelt!=0)?number_format($tcancelt,2,',','.'): ''}}        </td>
                                                        <td align="right" >{{ ($tdolares!=0)?number_format($tdolares,2,',','.'): ''}}       </td>
                                                        <td align="right" >{{ ($ttransf!=0)?number_format($ttransf,2,',','.'): ''}}         </td>
                                                        <td align="right" class=" ocultarcop" >{{ ($tpesos!=0)?number_format($tpesos,2,',','.'): ''}}           </td>
                                                        <td align="right" class=" ocultarcopt" >{{ ($tpeso_tranf!=0)?number_format($tpeso_tranf,2,',','.'): ''}} </td>
                                                        <td align="right" class=" ocultareur" >{{ ($teuros!=0)?number_format($teuros,2,',','.'): ''}}           </td>
                                                        <td align="right" >{{ ($tcancelaUSD!=0)?number_format($tcancelaUSD,2,',','.'): ''}}       </td>
                                                        <td align="right" >{{ ($tcredito!=0)?number_format($tcredito,2,',','.'): ''}}       </td>
                                                        <td align="right" >{{ ($ttotalventa!=0)?number_format($ttotalventa,2,',','.'): ''}} </td>
                                                    </tr>

                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                    <script>
                                        @php if($tpesos == 0){ @endphp
                                        $('.ocultarcop').hide();
                                        @php }
                                     if($tpeso_tranf == 0){ @endphp
                                        $('.ocultarcopt').hide();
                                        @php }
                                    if($teuros == 0){ @endphp
                                        $('.ocultareur').hide();
                                        @php }  @endphp
                                    </script>
                                @endif
                                @if($tab == 'tab2')
                                    <div class="tab-pane active" id="edocuenta" role="tabpanel">
                                        <div class="card">

                                            <div class="card-body"   style="height: 400px; overflow: auto" >
                                                <div class="table-responsive table-card mb-1" >
                                                    <table class="table align-middle table-nowrap" id="">
                                                        <tr style="position: sticky; top: 0; ">
                                                            <td class="tdlineff" width="5%"  align="center">Fecha</td>
                                                            <td class="tdlineff" width="10%" align="center">Doc</td>
                                                            <td class="tdline"   width="45%" align="center">Observacion</td>
                                                            <td class="tdlineff" width="10%" align="center">Debitos</td>
                                                            <td class="tdlineff" width="10%" align="center">Creditos</td>
                                                            <td class="tdlineff" width="10%" align="center">Anticipos</td>
                                                            <td class="tdlineff" width="10%" align="center">Saldo</td>
                                                        </tr>
                                                        @php  $saldo = 0; @endphp
                                                        @if(isset($listadoc))
                                                            @foreach($listadoc as $doc)
                                                                <tr style="font-size: 11px">
                                                                    <td align="center"> {{$doc['fecha']}}</td>
                                                                    <td align="center"> {{$doc['numerod']}}</td>
                                                                    <td align="left">
                                                                        @if($doc['tipocxc'] == '20')
                                                                            {{$doc['document']}}
                                                                        @else
                                                                            @if($doc['tipocxc'] == '10')
                                                                                 FACTURA [Nro {{$doc['document']}}]
                                                                            @else
                                                                                {{$doc['document']}}
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                    <td align="right">
                                                                        @php
                                                                            if ($doc['tipocxc'] == '10' or $doc['tipocxc'] == '25'  or $doc['tipocxc'] == '20') {
                                                                                if($doc['montodolares']>0){
                                                                                        echo number_format($doc['montodolares'],2,',','.');
                                                                                        if($doc['document'] != 'DIF.CAMBIARIO')
                                                                                            $saldo += $doc['montodolares'];
                                                                                }else{
                                                                                    if($doc['tasadolar']){
                                                                                        echo number_format($doc['monto']/$doc['tasadolar'], 2, ',', '.');
                                                                                        if($doc['document'] != 'DIF.CAMBIARIO')
                                                                                            $saldo += $doc['monto']/$doc['tasadolar'];
                                                                                    }

                                                                                }
                                                                           }
                                                                        @endphp
                                                                    </td>
                                                                    <td align="right">
                                                                        @php
                                                                            if(($doc['tipocxc']!='10' and $doc['tipocxc']!='50')  and   $doc['tipocxc']!='25' and   $doc['tipocxc']!='20'){

                                                                                if($doc['montodolares']>0){
                                                                                    echo number_format($doc['montodolares'],2,',','.');
                                                                                    $saldo-=$doc['montodolares'];
                                                                                }else{
                                                                                    if($doc['tasadolar']){
                                                                                        echo number_format($doc['monto']/$doc['tasadolar'], 2, ',', '.');
                                                                                        $saldo -= $doc['monto']/$doc['tasadolar'];
                                                                                    }
                                                                                }
                                                                            }

                                                                        @endphp
                                                                    </td>
                                                                    <td align="right"></td>
                                                                    <td align="right">
                                                                        {{number_format($saldo,2,',','')}}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </table>
                                                    <input type="text" name="textfield" id="dfocus" style="width:1px; height:1px; opacity:0"  />
                                                    <script> $("#dfocus").focus(); </script>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif

                                @if($tab == 'tab3')
                                    <div class="tab-pane active" id="edocuenta" role="tabpanel">
                                        <div class="card">

                                            <div class="card-body"   style="height: 400px; overflow: auto" >
                                                <div class="table-responsive table-card mb-1" >
                                                    <table class="table align-middle table-nowrap" id="">
                                                        <tr style="position: sticky; top: 0; ">
                                                            <td class="tdlineff" width="5%"  align="center">Ingresado</td>
                                                            <td class="tdlineff" width="10%" align="center">Tipo</td>
                                                            <td class="tdlineff" width="10%" align="left">Modelo</td>
                                                            <td class="tdlineff" width="10%" align="left">Marca</td>
                                                            <td class="tdlineff" width="10%" align="left">Identificacion</td>
                                                            <td class="tdlineff" width="5%" align="center">A&ntilde;o</td>
                                                            <td class="tdlineff" width="5%" align="center">Acciones</td>
                                                        </tr>
                                                        @php  $saldo = 0; @endphp
                                                        @if(isset($vehiculos))
                                                            @foreach($vehiculos as $vehiculo)
                                                                <tr style="font-size: 11px">
                                                                    <td align="center"> {{$vehiculo['FormattedDate']}}</td>
                                                                    <td align="center"> {{$vehiculo['tipo']->tipo}}</td>
                                                                    <td align="left"> {{$vehiculo['modelo']}}</td>
                                                                    <td align="left" > {{$vehiculo['marca']}}</td>
                                                                    <td align="left" > {{$vehiculo['identificacion']}}</td>
                                                                    <td align="center" > {{$vehiculo['year']}}</td>
                                                                    <td align="right" >
                                                                        <a href="{{ route('clientes.vehiculos.mantenimientos', [$codclie, $vehiculo['id']]) }}"
                                                                                              class="btn btn-sm btn-primary" title="Ver mantenimientos">
                                                                            <i class="ri-history-line"></i>
                                                                        </a>
                                                                        <a href="#" class="btn btn-sm btn-success" title="Agregar mantenimiento"
                                                                           onclick="abrirModalMantenimiento({{ $vehiculo['id'] }})">
                                                                            <i class="ri-add-line"></i>
                                                                        </a> </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </table>
                                                    <input type="text" name="textfield" id="dfocus" style="width:1px; height:1px; opacity:0"  />
                                                    <script> $("#dfocus").focus(); </script>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endif
                        </div>

                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="documentModal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" > </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body" id="documentView">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">  CERRAR</button>
                </div>
            </div>
        </div>
    </div>

    @if($tab == 'tab3' and isset($tipos))
        <div class="modal fade" id="vehiculoModal" aria-hidden="true" aria-labelledby="..." tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable" >
                <form action="{{route('vehiculos.store')}}"  name="form2" id="form2"
                      onsubmit="return checkSubmitVehiculos()" method="POST"
                      autocomplete="off" class="needs-validation vehiculos-form"   novalidate>
                    @method('post')
                    @csrf
                    <input type="hidden" name="codclie" value="{{ $codclie }}" />
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-sm">
                                                <div class="avatar-title rounded-circle bg-light text-primary fs-20">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="card-title mb-1 ">Informaci&oacute;n</h5>
                                            <p class="text-muted mb-0">Ingrese los datos del vehiculo.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div>
                                        <div>
                                            <select onchange="$('.error-msg').hide(); $('.datosvehiculo').fadeIn(); " class="form-select" data-choices  required
                                                    id="choices-tipovehiculo" name="fk_tipo">
                                                <option value=""> Seleccionar tipo de vehiculo </option>
                                                @foreach($tipos as $tipo)
                                                    <option value="{{$tipo->id}}">{{ $tipo->tipo }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="error-msg mt-1">Por favor, seleccione el tipo de vehiculo</div>
                                    </div>
                                </div>
                            </div>

                            <div class="card datosvehiculo" style="display: none">

                                <div class="card-body" style=" background-color: #f3f4f4">
                                    <div class="row ">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label"  for="modelo">Modelo</label>
                                                <input type="text" class="form-control" id="modelo" maxlength="50" name="modelo"
                                                       placeholder="Ej: Aveo" required value="">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="marca">Marca</label>
                                                <input type="text" class="form-control" id="marca" name="marca"
                                                       placeholder="Ej: Chevrolet">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label"  for="identificacion">Identificaci&oacute;n/Placa</label>
                                                <input type="text" class="form-control" id="identificacion" maxlength="50" name="identificacion"
                                                       placeholder="Ej: AXXXX124" required value="">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="year">A&ntilde;o</label>
                                                <input type="number" step="1" min="1950" max="2200" class="form-control"
                                                       id="year" name="year" required  placeholder="Ej: 2008">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row ">
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label"  for="serialmotor">Serial Motor <small>Opcional</small></label>
                                                <input type="text" class="form-control"  name="serialmotor" id="serialmotor"  value="">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="serialchasis">Serial Chasis <small>Opcional</small></label>
                                                <input type="text" class="form-control" id="serialchasis" name="serialchasis" >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row ">
                                        <div class="col-lg-12">
                                            <div class="mb-3">
                                                <label class="form-label"  for="observaciones">Observaciones/Anotaciones/Recordatorios</label>
                                                <input type="text" class="form-control"  name="observaciones" id="observaciones"  value="">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="text-end mb-3">
                                <button type="submit" class="btn btn-success w-sm">Enviar</button>
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">  CERRAR</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endif

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
