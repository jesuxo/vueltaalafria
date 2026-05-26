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

<div class="bg-white p-3 " style="border-radius: 5px">
    <div class="row align-items-center">
        <div class="col-4 text-left">
            <div class="shop_menu_left text-left f_size_13">
                @if($productos->total() > 0)
                    <p style="font-size: 15px !important;">
                        @php
                            if($productos->total() <= 9){
                                echo "1 / ".$productos->total();
                            }else{
                                if($productos->currentPage() == 1){
                                    echo "1 - 9 ";
                                }else{
                                    $currentpage = $productos->currentPage();
                                    echo $first  = ($currentpage * 9) - 8;
                                    $resta       = $productos->total() - ($first);

                                    if($resta < 9){
                                        echo ' - '.$productos->total()." ";
                                    }else{
                                        echo ' - '.($first + 8);
                                    }
                                }
                            }
                        @endphp
                        de {{$productos->total()}}
                    </p>
                @endif
            </div>
        </div>
        <div class="col-8">
            <div class="shop_menu_right ">
                {{--                <form method="get" action="#">--}}
                {{--                    <select class="selectpickers">--}}
                {{--                        <option value="menu_order">Default Sorting</option>--}}
                {{--                        <option value="popularity">Popularity</option>--}}
                {{--                        <option value="rating">Average rating</option>--}}
                {{--                        <option value="date">Feature</option>--}}
                {{--                        <option value="date">Newness</option>--}}
                {{--                    </select>--}}
                {{--                </form>--}}
                <div class="view-style shop_grid d-flex align-items-center justify-content-end">
                    @if($vertodos)
                        <span class="cursor-pointer vertodos" data-vertodos="0">Paginado</span>
                    @else
                        <span class="cursor-pointer vertodos" data-vertodos="1">Todos - </span>
                    @endif

                    <span class=" ml-2 cursor-pointer ordenprecio" data-ordenprecio="{{($ordenprecio)? '0': '1'}}">Precios <i class="ti-arrow-up"></i><i class="ti-arrow-down"></i></span>
                    <div class="list-style ">
                        <a href="javascript:;" class="change_view change_view0" data-vista="0"><i class="ti-layout-grid2"></i></a>
                    </div>
                    <div class="grid-style active">
                        <a href="javascript:;" class="change_view change_view1" data-vista="1"><i class="ti-menu-alt"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @foreach($productos as $prod)
        <div class="row shop_list_item"  style="width: 110%;">
            <div class="col-3">
                <div class="shop_list_img text-center" style="margin-top: 8px;">
                    <a href="javascript:;" class="productomodal" data-id="{{$prod->id}}" data-url="{{route('ver.producto', $prod->id)}}" data-toggle="modal" data-target="#verproducto" >
                        @if(isset($prod->imagenes[0]))
                            <img class="img-fluid" src="{{asset('img/productos/th'.$prod->imagenes[0]->url)}}" alt="" style="max-height: 160px; margin: auto">
                        @else
                            <img class="img-fluid" src="{{asset('img/default.jpg')}}" alt="" style="max-height: 160px; margin: auto">
                        @endif
                    </a>
                </div>
            </div>
            <div class="col-9 single_product_item mt-0">
                <div class="single_pr_details text-left" style="border-bottom: 1px solid rgba(0,0,0,0.04)">
                    <a href="javascript:;" class="productomodal s_list_title" data-id="{{$prod->id}}" data-url="{{route('ver.producto', $prod->id)}}" data-toggle="modal" data-target="#verproducto"  >
                        <h3 class="f_p f_100 f_size_16">{{$prod->descrip1}}</h3>
                    </a>

                    @if($prod->precio_visible)
                        <div class="price">
                            <ins style="position:relative;">
                                <span class="woocommerce-Price-amount amount">
                                    @if(isset($prod->hijos[0]->sugerido[0]->id))
                                        {{number_format($prod->hijos[0]->sugerido[0]->monto,2,',','.')}}
                                    @else
                                        {{ number_format($prod->precio,2,',','.')  }}
                                    @endif
                                </span>
                            </ins>
                        </div>
                    @endif

                    <p style="overflow: hidden; width: 100%; max-height: 80px;" class="f_p f_100 f_size_15  productomodal s_list_title" data-id="{{$prod->id}}" data-url="{{route('ver.producto', $prod->id)}}" data-toggle="modal" data-target="#verproducto" >
                        {{$prod->descrip2}}
                    </p>

                    <p class="f_p f_100 f_size_13  productomodal s_list_title" data-id="{{$prod->id}}" data-url="{{route('ver.producto', $prod->id)}}" data-toggle="modal" data-target="#verproducto" >
                        Marca: {{$prod->marca}}
                    </p>

                </div>
            </div>
        </div>
    @endforeach
    @if(!isset($prod) or !$prod)
        <div class="row shop_list_item mb-4">
            <div class="col-md-3 mb-4">
                <div class="shop_list_img text-center">
                    <a href="/home" style="margin: auto">
                        <img class="img-fluid" src="{{asset('img/default.jpg')}}" alt="">
                    </a>
                </div>
            </div>
            <div class="col-md-9 single_product_item mt-0">
                <div class="single_pr_details text-left" style="padding: 40px;
  border-radius: 5px;">
                    <a href="/" class="s_list_title" style="color: white !important;">
                        <h3 class="f_p f_500 f_size_22" style="color: white !important;">No se encontraron resultados para su busqueda o filtros</h3>
                    </a>


                    <p class="f_p f_400 f_size_15 mt_30 ">
                        Le invitamos a volver a buscar de nuevo<br>
                        o tambien puede contactarnos para poder ofrecerle cualquier ayuda que necesite.
                    </p>

                </div>
            </div>
        </div>
    @endif

    <div class="hr mt-4"></div>

    @if(isset($productos[0]) and $productos->lastPage() > 1)
        <div class="row">
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
        </div>
    @endif
</div>
