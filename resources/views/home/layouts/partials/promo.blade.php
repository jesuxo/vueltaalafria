@if(isset($data->id) )
    <div class="container">
        <div class="row content-producto ">
            <div class="col-lg-6 ">
                <div class="product_slider mb-4">
                    <div class="pr_image text-center">
                        @if(isset($data->imagen))
                            <div class="item text-center">
                                <a href="{{route('ver.promo', $data->id)}}" class="text-center">
                                    <img class="text-center" src="{{asset('img/promociones/'.$data->imagen)}}" alt="" style="max-width: 250px; margin: auto;"  >
                                </a>
                            </div>
                        @else
                            <div class="item">
                                <a href="{{route('ver.promo', $data->id)}}">
                                    <img src="{{asset('img/defaultBig.jpg')}}" alt="">
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6" style="display: flex;   align-items: center">
                <div class="pr_details">
                    <a href="{{route('ver.promo', $data->id)}}" class="pr_title f_size_30">
                        {{ ucfirst( $data->public_title )}}
                    </a>
                    <br>
                    <br>
                    @if($data->monto)
                        <div class="price mb-2">
                            <ins style="position:relative;">
                                    <span class="woocommerce-Price-amount amount">
                                        {{ number_format($data->monto,2,',','.')  }}
                                    </span>
                            </ins>
                        </div>
                    @endif

                    <p class="f_size_15 f_100">
                        {{$data->public_descrip}}
                    </p>
                    <br>
                    <div class="pr_footer mb-2">
                        <div class="share-link">
                            <label>Compartir en: </label><br>
                            <ul class="social-icon list-unstyled">
                                <li><a class="whatspp"  title="Whatsapp" target="_blank" href="https://api.whatsapp.com/send?text={{urlencode(route('ver.promo', $data->id))}}"><img style="height: 16px; margin-top: -3px;" src="{{asset('img/logowhatsapp.png')}}"></a></li>
                                <li><a class="facebook" title="Facebook" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={{urlencode(route('ver.promo', $data->id))}}"><i class="ti-facebook"> </i></a></li>
                                <li><a class="twitter"  title="Twitter"  target="_blank" href="https://twitter.com/intent/tweet?text={{urlencode(route('ver.promo', $data->id))}}"><i class="ti-twitter"> </i></a></li>
                                <li><a class="linkedin" title="Linkedin" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&url={{urlencode(route('ver.promo', $data->id))}}&title={{urlencode(str_replace('"', '', $data->public_title))}}&summary=&source="><i class="ti-linkedin"> </i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="container">
        <div class="row content-producto mt-5">
            <div class="col-lg-6 mt-5">
                <div class="product_slider mb-4">
                    <div class="pr_image owl-carousel">

                        <div class="item">
                            <a href="#">
                                <img src="{{asset('img/default.jpg')}}" alt="" style="max-width: 400px; text-align:  center; margin: auto">
                            </a>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-6" style="display: flex; justify-content: center; align-items: center">
                <div class="pr_details" >
                    <a href="#" class="pr_title f_size_30">
                        Esta promoci&oacute;n ya no esta disponible
                    </a>
                    <br><br><br>



                    <p class="f_size_15 f_100">
                        Siempre estamos renovando nuestras promociones, si deseas puedes revisar el siguiente link
                        para poder visualizar las promociones actuales

                    </p>

                    <br>
                    <p class="f_size_15 f_100">
                        <a href="http://drogueriaelarca.com/promociones" class="text-primary">Pagina de Promociones</a>

                    </p>


                </div>
            </div>
        </div>
    </div>
@endif
