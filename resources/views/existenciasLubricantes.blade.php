@extends('layouts.master')
@section('title')
    Existencias de productos
@endsection
@section('css')
    <style>
        #clearall{
            text-decoration: none !important;
        }

        .tdline{
            border:1px solid #0072c5 !important;

        }
        .tdlineff{
            border-left:1px solid #fff !important;

            color: white !important;
            background-color: #0072c5 !important;
        }
    </style>

@endsection
@section('content')
    <div class=" col-lg-12 ">
        <div class=" row ">
            <div class="card card-height-100">
                <div class="card-header align-items-center text-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">  EXISTENCIAS POR MARCAS</h4>
                </div>
                <div class="card-body" data-simplebar  >
                    <table width="1000px" border="0" class="table table-borderless table-centered align-middle table-nowrap mb-0 ">
                        <thead  style="position: sticky; top: 0;">
                        <tr>
                            <td width="20%" align="center" class="titulo tdlineff  "> MARCA  </td>
                            @foreach($arraysucursal as $index => $sucursal)
                                <td width="13%"  align="center" class="titulo tdlineff   "> {{ $sucursal }}</td>
                            @endforeach
                            <td width="13%"  align="center" class="titulo tdlineff   "> </td>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $tantos  = $totalline = 0;
                            $totalessucu = [];
                        @endphp

                        @foreach($arrayinstanci as $indexinst => $instancia)
                            @php
                                $tantos++;
                                $bgcolor = "#f2f2f2";
                                if(($tantos%2)==0){ $bgcolor = "#ffffff"; }

                                $tline = 0;
                            @endphp

                            <tr  bgcolor="{{$bgcolor}}" style="color:#333;">
                                <td align="left"   class="titulo tdline  ">
                                     {{$instancia}}
                                </td>
                                @foreach($arraysucursal as $index => $sucursal)
                                    <td align="center" class="titulo tdline ">
                                        @php
                                            if(!isset($totalessucu[$index]))
                                                $totalessucu[$index] = 0;

                                            $tline += (isset($arraycantidad[$indexinst][$index]))? $arraycantidad[$indexinst][$index]  : 0;
                                            $totalessucu[$index] += (isset($arraycantidad[$indexinst][$index]))? $arraycantidad[$indexinst][$index]  : 0;

                                        @endphp
                                        {{(isset($arraycantidad[$indexinst][$index]))? $arraycantidad[$indexinst][$index] +0 :'' }}
                                    </td>
                                @endforeach
                                <td align="center" class="titulo tdline text-primary " style="font-weight: bold">
                                    {{ $tline  }}
                                    @php $totalline += $tline @endphp
                                </td>
                            </tr>
                        @endforeach
                        <tr    style="color:#333;">
                            <td align="left"   class="titulo tdline  ">   </td>
                            @foreach($arraysucursal as $index => $sucursal)
                                <td align="center"class="titulo tdline text-primary " style="font-weight: bold">
                                    {{(isset($totalessucu[$index]))? $totalessucu[$index]   :'' }}
                                </td>
                            @endforeach
                            <td align="center" class="titulo tdline text-primary " style="font-weight: bold">
                                {{ $totalline  }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade flip" id="showModalModelos" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body p-5 text-center" id="showModalModelosContent">

                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script>
        $('.showModalModelos').unbind('click').bind('click',function (e) {
            e.preventDefault();
            e.stopPropagation();

            var modalContent = $('#showModalModelosContent');
            var inspadre     = $(this).data('inspadre');

            modalContent.html(`
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Espere por favor...</p>
                    </div>
                `);

            $.ajax({
                type: 'POST',
                url: '/existencia/motos/modelos',
                data:{inspadre:inspadre},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (data) {
                    modalContent.html(data);
                },
                error: function(xhr, status, error) {
                    modalContent.html(`
                            <div class="alert alert-danger">
                                Error al cargar los modelos. Por favor intente nuevamente.
                            </div>
                        `);
                    console.error(error);
                }
            });
        });
    </script>
@endsection
