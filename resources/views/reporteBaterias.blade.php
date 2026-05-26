@extends('layouts.master')
@section('title')
    Venta de productos por sucursal
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css">

    <!--Swiper slider css-->
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css">
    <style>
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
                                Baterias
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a class="nav-link" onclick="tabchangeto('2')"  href="javascript:;" role="tab" id="tab2">
                                Areas de Baterias
                            </a>
                        </li>
                    </ul>

                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="tabcontent1" role="tabpanel">
                        <div class="card"  >
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Ventas de Baterias  </h4>
                                <small> {{ str_replace('to','al',$fechasreport) }} </small>
                            </div>

                            <div class="card-body" >
                                <table width="100%">
                                    <tr>
                                        <td  width="20%" class="p-2 text-start tdlineff">Lubricante</td>
                                        @foreach($sucursales as $indexsucu => $sucursal)
                                            <td class="text-center tdlineff" width="17%">{{$sucursal}}</td>
                                        @endforeach
                                        <td width="17%" class="tdlineff text-center">Total</td>
                                    </tr>
                                    @php
                                        $porc      = 0;
                                        $tantosprd = 0;
                                        $tantassucuprod = [];
                                        if(isset($ventaProductos)){
                                            foreach ($ventaProductos as $cant){
                                                $tantosprd += $cant;
                                            }
                                        }
                                    @endphp
                                    @if(isset($productos))
                                        @foreach($productos as $indexprod => $producto)

                                            <tr>
                                                <td class="text-start tdline p-1">{{(isset($producto))? $producto : ''}}</td>
                                                @foreach($sucursales as $indexsucu => $sucursal)
                                                    @php
                                                        if(!isset($tantassucuprod[$indexsucu])) $tantassucuprod[$indexsucu] = 0;
                                                        $tantassucuprod[$indexsucu] += (isset($ventaSucuProd[$indexprod][$indexsucu]))? $ventaSucuProd[$indexprod][$indexsucu]:0;
                                                    @endphp
                                                    <td class="text-end tdline">{{(isset($ventaSucuProd[$indexprod][$indexsucu]) and $ventaSucuProd[$indexprod][$indexsucu] > 0)? number_format($ventaSucuProd[$indexprod][$indexsucu] + 0,2,',','.') : ''}}</td>
                                                @endforeach
                                                <td class="text-end tdline">{{ number_format($ventaProductos[$indexprod] ,2,',','.')}}</td>
                                            </tr>

                                        @endforeach
                                    @endif

                                    <tr>
                                        <td> </td>
                                        @foreach($sucursales as $indexsucu => $sucursal)
                                            <td height="30" class="text-center tdlineff ">{{(isset($tantassucuprod[$indexsucu]))? number_format($tantassucuprod[$indexsucu],2,',','.') : ''}}</td>
                                        @endforeach
                                        <td class="text-center tdlineff">{{ number_format($tantosprd,2,',','.') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane " id="tabcontent2" role="tabpanel">
                        <div class="card" >
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1"> Baterias Vendidos</h4>
                                <small> {{ str_replace('to','al',$fechasreport) }} </small>
                            </div>

                            <div class="card-body" >
                                <table width="100%">
                                    <tr>
                                        <td  width="20%" class="p-2 text-start tdlineff">Baterias</td>
                                        @foreach($sucursales as $indexsucu => $sucursal)
                                            <td class="text-center tdlineff" width="17%">{{$sucursal}}</td>
                                        @endforeach
                                        <td width="17%" class="tdlineff text-center">Total</td>
                                    </tr>
                                    @php
                                        $porc      = 0;
                                        $tantosprd = 0;
                                        $tantassucuprod = [];
                                        if(isset($ventaInstancia)){
                                            foreach ($ventaInstancia as $cant){
                                                $tantosprd += $cant;
                                            }
                                        }
                                    @endphp
                                    @if(isset($instancias))
                                        @foreach($instancias as $indexinst => $instancia)

                                            <tr>
                                                <td class="text-start tdline p-1">{{(isset($instancia))? $instancia : ''}}</td>
                                                @foreach($sucursales as $indexsucu => $sucursal)
                                                    @php
                                                        if(!isset($tantassucuprod[$indexsucu])) $tantassucuprod[$indexsucu] = 0;
                                                        $tantassucuprod[$indexsucu] += (isset($ventainsprodsucu[$indexinst][$indexsucu]))?  $ventainsprodsucu[$indexinst][$indexsucu] :0;
                                                    @endphp
                                                    <td class="text-end tdline">{{(isset($ventainsprodsucu[$indexinst][$indexsucu]) and $ventainsprodsucu[$indexinst][$indexsucu] > 0)? number_format($ventainsprodsucu[$indexinst][$indexsucu] + 0,2,',','.') : ''}}</td>
                                                @endforeach
                                                <td class="text-end tdline">{{ number_format($ventaInstancia[$indexinst],2,',','.') }}</td>
                                            </tr>

                                        @endforeach
                                    @endif

                                    <tr>
                                        <td> </td>
                                        @foreach($sucursales as $indexsucu => $sucursal)
                                            <td height="30" class="text-center tdlineff ">{{(isset($tantassucuprod[$indexsucu]))? number_format($tantassucuprod[$indexsucu],2,',','.') : ''}}</td>
                                        @endforeach
                                        <td class="text-end tdlineff">{{ number_format($tantosprd,2,',','.') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-md-12 mb-4">
                    <form  method="post" name="form1" id="form1" action="/reporte/baterias">
                            <input type="hidden" value="" id="inspadre" name="inspadre">
                            <div class="input-group mb-4">
                                <input type="text" class="form-control" data-provider="flatpickr"
                                       data-range-date="true" data-date-format="d/m/Y"
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
                                    <span class="badge badge-soft-dark float-end">{{ number_format($ventaSucursal[$indexsucu],2,',','.') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                    <hr>
                        <div class="row">
                        @foreach($instanciaspadre as $indexpadre => $padre)
                            <div class="col-sm-12 col-lg-6 ">
                                <div class="card">
                                    <a href="javascript:;"
                                       onclick="
                                               @if(isset($inspadre) and $inspadre > 0)
                                                  $('#inspadre').val(0);
                                               @else
                                                  $('#inspadre').val({{$indexpadre}});
                                               @endif
                                        $('#form1').submit()"

                                       class="card-header align-items-center d-flex {{(isset($inspadre) and $inspadre > 0)? ' tdlineff ': '  '}} ">
                                        <h4 class="card-title mb-0 flex-grow-1">{{$padre}}</h4>
                                        <span class="badge {{(isset($inspadre) and $inspadre > 0)? ' badge-soft-light ': ' badge-soft-dark '}} float-end">{{ number_format($ventainspadre[$indexpadre],2,',','.') }}</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
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
    </script>

@endsection
