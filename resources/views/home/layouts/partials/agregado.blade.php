<style>
    .woocommerce-Price-amount:before{
        content: '$';
        font-size: 12px;
        position: absolute;
        top: -1px;
        left: 0;
        font-weight: 100;
    }
</style>

@if(isset($producto->id))
    <div class="row shop_list_item p-0 m-0">
        <div class="col-3">
            <div class="shop_list_img text-center" style="margin-top: 8px;">
                <a href="{{route('ver.producto', $producto->id)}}" >
                    @if(isset($producto->imagen))
                        <img class="img-fluid ml-1" src="{{asset('img/productos/th'.$producto->imagen)}}" alt="" style="max-height: 160px; margin: auto">
                    @else
                        <img class="img-fluid ml-1" src="{{asset('img/default.jpg')}}" alt="" style="max-height: 160px; margin: auto">
                    @endif
                </a>
            </div>
        </div>
        <div class="col-9 single_product_item" style="margin: 8px 0px !important;">
            <div class="single_pr_details text-left">
                <a href="{{route('ver.producto', $producto->id)}}" class="s_list_title" >
                    <h3 class="f_p f_100 f_size_13">{{$producto->descrip}}</h3>
                </a>
                <div class="price {{(!$producto->precio_visible)? 'display_none' : ''}}">
                    <ins style="position:relative;">
                        <span class="woocommerce-Price-amount amount">
                            {{  number_format($precio,2,',','.')}}
                        </span>
                    </ins>
                </div>
            </div>
        </div>
    </div>
@endif


