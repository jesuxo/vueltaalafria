
<div class="row content-producto">
    <div class="col-12">
        <ul class="product_meta list-unstyled d-flex" style="flex-direction: row-reverse; position: absolute; left: 0; top: 0;">
            <li class="ml-1">
                <a href="/busqueda/{{$data->instancia->descrip}}" class=" text-primary f_size_13">
                    > {{$data->instancia->descrip}}
                </a>
            </li>

            <?php
            $padre = $data->instancia->insPadre;

            while ($padre > 0){

            $instancia = \App\Models\Sainsta::where('codinst', $padre)->first();

            ?>
            <li class="{{($instancia->insPadre > 0)? 'ml-1':'ml-3'}} ">
                <a href="/busqueda/{{$data->instancia->descrip}}" class=" text-primary f_size_13" >
{{--             .categoria       data-codinst="{{$instancia->codinst}}"--}}
                    {{($instancia->insPadre > 0)? '>':''}} {{$instancia->descrip}}
                </a>
            </li>
            <?php
            $padre = $instancia->insPadre;

            }

            ?>
        </ul>
    </div>
    <div class="col-lg-6 mt-5">
        <div class="product_slider mb-4">
            <div class="pr_image owl-carousel">
                @if(isset($data->imagen))
                        <div class="item">
                            <a href="{{route('ver.producto', $data->id)}}">
                                <img src="{{asset('img/productos/'.$data->imagen)}}" alt=""  >
                            </a>
                        </div>

                @else
                    <div class="item">
                        <a href="{{route('ver.producto', $data->id)}}">
                            <img src="{{asset('img/defaultBig.jpg')}}" alt="">
                        </a>
                    </div>
                @endif
            </div>
        </div>

    </div>
    <div class="col-lg-6">
        <div class="pr_details">
            <a href="{{route('ver.producto', $data->id)}}" class="pr_title f_size_30">
                {{ ucfirst( $data->descrip )}}
            </a>
            <br>
            @if($data->existencia > 0)
                <span class="stock">Disponible</span>
            @else
                <span class="stock text-danger">Agotado</span>
            @endif

            @if($data->precio_visible)
                <div class="price mb-2">
                    <del class="display_none"><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">$</span>18.00</span></del>
                    <ins style="position:relative;">
                        <span class="woocommerce-Price-amount amount">
                                {{ number_format($data->costod3,2,',','.')  }}
                        </span>
                    </ins>
                </div>
            @endif

            <?php

                $go = 1;

                if(\Illuminate\Support\Facades\Auth::user()){
                    $added = \App\Models\CompraItems::whereHas('compra',
                        function ($query) {
                            $query->where(['encurso'=> 1, 'fk_user' => auth()->id()]);
                        })->with('compra')->where(['fk_producto' => $data->id])->first();
                    if(isset($added->fk_producto)){
                        $go = 0;
                    }
                }

            ?>
            <div class="d-flex">
                <div class="mr-2 {{(!$go)? 'display_none': ''}} btn-comprarlg btn-primary text-center cursor_pointer btn-comprar comprar btn-despuesdeagregar{{$data->id}}" data-id="{{$data->id}}" >
                    Agregar <i class="ti-shopping-cart" style="font-size: 20px"></i>
                </div>

                <div class="mr-2 text-center cursor_pointer btn-comprarlg btn-primary  {{(!$go)? '': 'display_none'}}  btn-agregado{{$data->id}}">
                    Agregado
                </div>


                <a href="{{route('ver.producto', $data->id)}}" class="mr-2 linkverproducto   btn-comprarlg btn-primary  text-center cursor_pointer" style="margin-top: 10px; border: 2px solid #fff; border-radius: 5px; font-size: 20px;" >
                    Ver producto
                </a>
            </div>


            <p class="f_size_15 f_100">
                {{$data->descrip2}}
                {!! ($data->descrip3)? ' <br>'.$data->descrip3 : '' !!}
                {!! ($data->descrip4)? ' <br>'.$data->descrip4 : '' !!}
            </p>

            <div class="pr_footer mb-2">

                <ul class="product_meta list-unstyled mt-2">
                        <li><span>Referencia:</span> <br>
                            <span class="f_size_20" style="font-weight: 100">{{$data->referencia}} </span>
                        </li>
                        <li><span>Marca:</span> <br>
                            <span class="f_size_20" style="font-weight: 100">{{$data->marca}} </span>
                        </li>

                </ul>
                <div class="share-link">
                    <label>Compartir en: </label><br>
                    <ul class="social-icon list-unstyled">
                        <li><a class="whatspp"  title="Whatsapp" target="_blank" href="https://api.whatsapp.com/send?text={{urlencode(route('ver.producto', $data->id))}}"><img style="height: 16px; margin-top: -3px;" src="{{asset('img/logowhatsapp.png')}}"></a></li>
                        <li><a class="facebook" title="Facebook" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={{urlencode(route('ver.producto', $data->id))}}"><i class="ti-facebook"> </i></a></li>
                        <li><a class="twitter"  title="Twitter"  target="_blank" href="https://twitter.com/intent/tweet?text={{urlencode(route('ver.producto', $data->id))}}"><i class="ti-twitter"> </i></a></li>
                        <li><a class="linkedin" title="Linkedin" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url={{urlencode(route('ver.producto', $data->id))}}&title={{urlencode(str_replace('"', '', $data->descrip))}}&summary=&source="><i class="ti-linkedin"> </i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
