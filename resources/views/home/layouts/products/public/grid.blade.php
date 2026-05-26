
<style>
    .woocommerce-Price-amount:before{
        content: '$';
        font-size: 12px;
        position: absolute;
        top: -3px;
        left: 0;
        font-weight: 100;
    }
</style>

@if(isset($productos) and $productos)
<div class="bg-white p-3 " style="border-radius: 5px;    border: 1px solid #0071ba">
    <div class="row align-items-center mb-3">
        <div class="col-4 text-left">
            <div class="shop_menu_left text-left">
                @if($productos->total() > 0)
                    <p style="font-size: 15px !important;">
                        @php
                            if($vertodos){
                                echo ' Total de productos  = '.$productos->total()." ";
                            }else{

                               if($productos->total() <= 12){
                                   echo "1 / ".$productos->total();
                               }else{
                                   if($productos->currentPage() == 1){
                                       echo "1 - 12 ";
                                   }else{
                                       $currentpage = $productos->currentPage();
                                       echo $first  = ($currentpage * 12) - 11;
                                       $resta       = $productos->total() - ($first);

                                       if($resta < 12){
                                           echo ' - '.$productos->total()." ";
                                       }else{
                                           echo ' - '.($first + 8);
                                       }
                                   }
                               }
                               echo "de ".$productos->total();
                            }
                        @endphp

                    </p>
                @endif
            </div>
        </div>
        <div class="col-8 text-right">
            <div class="shop_menu_right">
                <div class="view-style shop_grid  d-flex align-items-center justify-content-end">
                    @if($vertodos)
                        <span class="cursor-pointer vertodos" data-vertodos="0">Ver Paginado &nbsp; -</span>
                    @else
                        <span class="cursor-pointer vertodos" data-vertodos="1">Ver Todos &nbsp;- </span>
                    @endif

                    <span class=" ml-2 cursor-pointer ordenprecio " data-ordenprecio="{{($ordenprecio)? '2': '1'}}">Orden Precios
                        <i class="ti-arrow-up ml-1"></i><i class="ti-arrow-down"></i>
                    </span>
                    <div class="list-style active ml-2 ">
                        <a href="javascript:;" class="change_view change_view0" data-vista="0"><i class="ti-layout-grid2"></i></a>
                    </div>
                    <div class="grid-style " style="display: none">
                        <a href="javascript:;" class="change_view change_view1" data-vista="1"><i class="ti-menu-alt"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <style>
            .product_img td{border-top:  none !important;}
            .product_img a{
                font:200 15px "Gotham",sans-serif !important;
                color: white !important;
                text-shadow: 2px 1px 2px #555 !important;
                line-height: 1.5;
                font-weight: bold !important;
            }
            .product_img a:hover{
                color: white !important;
                text-decoration: underline;
            }
            .single_product_item:hover a{
                color: white !important;
            }

        </style>
        @foreach($productos as $prod)

            <div class="col-lg-3 col-sm-3" style="color: #7f6d4f">
                <div class="single_product_item" style="position: relative">
                    <div class="product_img" style="width: 100%; height: 320px; display: block;  padding-top: 196px;
                            background: url({{((isset($prod->imagen) and strlen($prod->imagen)>3))?  '/img/productos/th'.$prod->imagen :  ('img/default.jpg')}}  ) no-repeat center top">
                        <table class="prodtd" width="100%" border="0" style="border: none; font-size: 10px !important; font-weight: bold">
                            <tr>
                                <td style="line-height: 16px; padding-left: 15px  " align="left">{{$prod->descrip3}}</td>
                                <td style="line-height: 16px;  padding-right: 15px  " align="right">{{$prod->descrip4}}</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="line-height: 16px; padding: 0px 15px !important;  ">
                                    <a href="javascript:;" class="productomodal"
                                       data-id="{{$prod->id}}" data-url="{{route('ver.producto', $prod->id)}}"
                                       data-toggle="modal" data-target="#verproducto"
                                       style="font-size: 12px !important; height: 30px; overflow:hidden; display: block   " >
                                        {{substr($prod->descrip,0,80)}}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td style="line-height: 16px; padding-left: 40px; font-size: 20px !important;  " align="left">
                                    <a href="javascript:;"
                                       style="font-size: 20px !important; height: 30px; overflow:hidden; display: block   " >
                                        ${{number_format($prod->costod3,2,',','.')}}
                                    </a>
                                    </td>
                                <td style="line-height: 16px;  padding-right: 15px  " align="right">
                                    <button type="button" class="btn btn-primary" style='font: 200 16px "Gotham",sans-serif;
                                    font-weight: 200; margin-right: 30px'>Agregar </button>
                                </td>
                            </tr>

                        </table>

                    </div>

                </div>
            </div>

        @endforeach

        @if(!isset($productos[0]))
            <div class="col-md-3 mb-4 mt-4 mb-4">
                <div class="shop_list_img">
                    <a href="/home">
                        <img class="img-fluid" src="{{asset('img/default.jpg')}}" alt="">
                    </a>
                </div>
            </div>
            <div class="col-md-9  mb-4 mt-4 single_product_item mt-0">
                <div class="single_pr_details text-left" style="padding: 40px;
  border-radius: 5px;">
                    <a href="/" class="s_list_title"  style="color: white !important;">
                        <h3 class="f_p f_500 f_size_22" style="color: white !important;">No se encontraron resultados para su busqueda o filtros</h3>
                    </a>
                    <p class="f_p f_400 f_size_15 mt_30 ">
                        Le invitamos a volver a buscar de nuevo<br>
                        o tambien puede contactarnos para poder ofrecerle cualquier ayuda que necesite.
                    </p>
                </div>
            </div>
        @endif

        <div class="hr mt-4"></div>
        @if(isset($productos[0]) and $productos->lastPage() > 1)

                <div class="col-4 text-right">

                        <button style="transform: rotate(180deg); {{($productos->previousPageUrl() != '')?'': 'display:none'}} "  id="paginar-prev" data-route="{{$productos->previousPageUrl()}}" class="text-muted paginar cursor_pointer">
                            <i class="ti-control-play" ></i>
                        </button>

                        <button style="{{($productos->nextPageUrl() != '')?'': 'display:none'}}" id="pagina-next" data-route="{{$productos->nextPageUrl()}}" class="text-muted paginar cursor_pointer">
                            <i class="ti-control-play"></i>
                        </button>

                </div>


                <div class="col-8 text-right">
                    <div class="shop_menu_right">
                        <div class="view-style shop_grid  d-flex align-items-center justify-content-end">
                            @if($vertodos)
                                <span class="cursor-pointer vertodos" data-vertodos="0">Paginado</span>
                            @else
                                <span class="cursor-pointer vertodos" data-vertodos="1">Todos - </span>
                            @endif

                            <span class=" ml-2 cursor-pointer ordenprecio" data-ordenprecio="{{($ordenprecio)? '0': '1'}}">Precios <i class="ti-arrow-up"></i><i class="ti-arrow-down"></i></span>
                            <div class="list-style active">
                                <a href="javascript:;" class="change_view change_view0" data-vista="0"><i class="ti-layout-grid2"></i></a>
                            </div>
                            <div class="grid-style ">
                                <a href="javascript:;" class="change_view change_view1" data-vista="1"><i class="ti-menu-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

        @endif
    </div>
</div>
@endif
