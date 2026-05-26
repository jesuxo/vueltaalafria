


    <div class="row row-reverse">

        <div class="col-lg-12"  >
            <style>
                .linkverproducto{
                    display: none;
                }
            </style>
            <div class="p-3">
                @include('home.layouts.partials.producto')
            </div>
        </div>
    </div>


@php $countdata = 0; @endphp
@if(isset($data->instancia->relacionados[0]))

    <div class="container mt-5 mb-5 related_div">
        <div class="hr mt-2 mb-3"> </div>
        <div class="sec_title text-center mb_70">
            <h2 class="f_p f_size_30 l_height50 f_600 t_color3">Productos Relacionados</h2>
            <p class="f_400 f_size_16 mb-0">Aqu&iacute; encontrar&aacute;s algunos productos que tambi&eacute;n pudieran llamar tu atenci&oacute;n</p>
        </div>
        <div class=" pb-5  mb_30 owl-carousel related1 carousel">

            @foreach($data->instancia->relacionados as $index => $rel)
                @if($rel->id != $data->id and $rel->existencia > 0)
                    <div class="single_product_item mt-0 studies_item">
                        <div class="product_img ">
                            @if(isset($rel->imagenes[0]))
                                <a  href="javascript:;" class="productomodal related_listed" data-id="{{$rel->id}}" data-url="{{route('ver.producto', $rel->id)}}" data-toggle="modal" data-target="#verproducto"  > <img class="img-fluid" src="{{asset('img/productos/th'.$rel->imagenes[0]->url)}}" alt=""  ></a>
                            @else
                                <a  href="javascript:;" class="productomodal related_listed" data-id="{{$rel->id}}" data-url="{{route('ver.producto', $rel->id)}}" data-toggle="modal" data-target="#verproducto"  >  <img class="img-fluid" src="{{asset('img/default.jpg')}}" alt=""></a>
                            @endif
                            {{--                            <div class="hover_content">--}}
                            {{--                                <a href="#"><i class="ti-heart"></i></a>--}}
                            {{--                                <a href="#" title="Add to cart"><i class="ti-bag"></i></a>--}}
                            {{--                            </div>--}}
                        </div>
                        <div class="single_pr_details">
                            <a  href="javascript:;" class="productomodal" data-id="{{$rel->id}}" data-url="{{route('ver.producto', $rel->id)}}" data-toggle="modal" data-target="#verproducto" >
                                <h3 class="f_p f_100 f_size_13"  style=" width: 100%; height: 18px; overflow: hidden;">   {{$rel->descrip1}}</h3>
                            </a>
                            @if($rel->precio_visible)
                                <div class="price">
                                    <ins style="position:relative;">
                                        <span class="woocommerce-Price-amount amount">
                                            {{number_format($rel->precio, 2, ',', '.')}}
                                        </span>
                                    </ins>
                                </div>
                            @endif
                        </div>
                    </div>
                    @php
                        if($countdata > 10)
                            break;
                        $countdata++;
                    @endphp
                @endif
            @endforeach
        </div>
    </div>

@endif

