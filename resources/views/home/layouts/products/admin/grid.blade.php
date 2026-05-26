<div class="developer_product_content">
    <ul class="nav nav-tabs develor_tab mb-30" id="myTab-20540485" role="tablist">
        <li class="nav-item"> <a class="nav-link active show" id="saasland-tab-5421" data-toggle="tab" role="tab" data-tab="ht2_tab_0_20540485" href="#saasland-tab-content-5421" aria-controls="saasland-tab-content-5421" aria-selected="false"> Productos ({{(isset($productos[0])? count($productos): '0')}}) </a> </li>
        <li class="nav-item"> <a class="nav-link abrir-promociones" id="saasland-tab-5422" data-toggle="tab" role="tab" data-tab="ht2_tab_1_20540485" href="#saasland-tab-content-5422" aria-controls="saasland-tab-content-5422" aria-selected="true"> Promociones  ({{(isset($promociones[0])? count($promociones): '0')}}) </a> </li>
    </ul>
    <div class="tab-content developer_tab_content">
        <div class="tab-pane fade active show" aria-labelledby="saasland-tab-5421" role="tabpanel" id="saasland-tab-content-5421">

            @if(isset($productos[0]))
                @foreach($productos as $prod)
                    <div class="card">
                        <div class="card-header" id="customer-support{{$prod->id}}">
                            <h5 class="mb-0">
                                <div class="btn btn-link d-flex align-items-center justify-content-between flex-grow {{($prod->fk_padre == '0')? '' : 'open_principal'}}" data-id="{{$prod->id}}" data-toggle="collapse" data-target="#customer-support-collapse-{{$prod->id}}" aria-expanded="true" aria-controls="customer-support-collapse-{{$prod->id}}">
                                    <div>
                                        @if($prod->fk_padre == '0')
                                            <input class="inputs" placeholder="Nombre de este producto" name="descrip1" value="{{$prod->descrip1}}" data-url="{{route('productos.update', $prod->id)}}" size="20" style="width: 400px; font-weight: 200" />
                                            <a href="javascript:;" class="productomodal" data-id="{{$prod->id}}" data-url="{{route('ver.producto', $prod->id)}}" data-toggle="modal" data-target="#verproducto" >
                                                open
                                            </a>
                                        @else
                                            {{$prod->descrip1}}
                                        @endif
                                    </div>
                                    <a href="{{route('ver.producto', $prod->id)}}" class="precio" target="_blank"  >
                                        $  {{number_format($prod->precio)}}
                                    </a>
                                </div>
                            </h5>
                        </div>
                        <div id="customer-support-collapse-{{$prod->id}}" class="{{(isset($collapse))? '' : 'collapse'}}" aria-labelledby="customer-support{{$prod->id}}" data-parent="#accordion-1" style="">
                            <div class="card-body">


                                <div class="field-content mb-3">
                                    <div class="mr-3 open-delete cursor-pointer" data-prod="{{$prod->id}}">Eliminar ?</div>
                                    <div class="display_none btn-delete{{$prod->id}}">
                                        <button type="button" class="btn btn-outline-success eliminece" data-action="{{route('productos.destroy', $prod->id)}}">Eliminece</button>
                                    </div>
                                </div>
                                @if($prod->fk_padre == '0')
                                    <div class="field-content mb-3">
                                        <div class="mr-3">Precio?</div>
                                        <div>
                                            <input type="checkbox" name="precio_visible" {{($prod->precio_visible)? 'checked': ''}} value="1" id="precio_visible"  data-url="{{route('productos.update', $prod->id)}}" >
                                        </div>
                                    </div>
                                    <div class="field-content mb-3">
                                        <div class="mr-3">Destacado?</div>
                                        <div>
                                            <input type="checkbox" name="destacado" {{($prod->destacado)? 'checked': ''}} value="1" id="destacado"  data-url="{{route('productos.update', $prod->id)}}" >
                                        </div>
                                    </div>
                                @endif
                                <div class="field-content mb-3">
                                    <div class="mr-3">Referencia:</div>
                                    <div class="{{($prod->fk_padre == '0')? '' : 'form-control'}}">
                                        @if($prod->fk_padre == '0')
                                            <input style="width: 400px; font-weight: 200" type="text" value="{{ucfirst($prod->referencia)}}" class="form-control form-input form-control-rounded" id="referencia" name="referencia" data-url="{{route('productos.update', $prod->id)}}" placeholder="Referencia del producto" >
                                        @else
                                            {{$prod->referencia}}
                                        @endif
                                    </div>
                                </div>
                                <div class="field-content mb-3">
                                    <div class="mr-3">Marca:</div>
                                    <div class="{{($prod->fk_padre == '0')? '' : 'form-control'}}">
                                        @if($prod->fk_padre == '0')
                                            <input style="width: 400px; font-weight: 200" type="text" value="{{ucfirst($prod->marca)}}" class="form-control form-input form-control-rounded" id="marca" name="marca" data-url="{{route('productos.update', $prod->id)}}" placeholder="Marca del producto" >
                                        @else
                                            {{$prod->marca}}
                                        @endif
                                    </div>
                                </div>
                                <div class="field-content mb-3">
                                    <div class="mr-3">Descripci&oacute;n 2:</div>
                                    <div class="{{($prod->fk_padre == '0')? '' : 'form-control'}}">
                                        @if($prod->fk_padre == '0')
                                            <!--<input style="width: 400px; font-weight: 200" type="text" value="{{ucfirst($prod->descrip2)}}" class="form-control form-input form-control-rounded" id="descrip2" name="descrip2" data-url="{{route('productos.update', $prod->id)}}" placeholder="Segunda descripci&oacute;n" >-->
                                            <textarea class="form-control form-input form-control-rounded" id="descrip2" name="descrip2" data-url="{{route('productos.update', $prod->id)}}" style="width: 400px; font-weight: 200">{{ucfirst($prod->descrip2)}}</textarea>
                                        @else
                                            {{$prod->descrip2}}
                                        @endif
                                    </div>
                                </div>
                                <div class="field-content mb-3">
                                    <div class="mr-3">Descripci&oacute;n 3:</div>
                                    <div class="{{($prod->fk_padre == '0')? '' : 'form-control'}}">
                                        @if($prod->fk_padre == '0')
                                            <input style="width: 400px; font-weight: 200" type="text" value="{{ucfirst($prod->descrip3)}}" class="form-control form-input form-control-rounded" id="descrip3" name="descrip3" data-url="{{route('productos.update', $prod->id)}}" placeholder="Tercera descripci&oacute;n" >
                                        @else
                                            {{$prod->descrip3}}
                                        @endif
                                    </div>
                                </div>
                                <div class="field-content mb-3">
                                    <div class="mr-3">Descripci&oacute;n 4:</div>
                                    <div class="{{($prod->fk_padre == '0')? '' : 'form-control'}}">
                                        @if($prod->fk_padre == '0')
                                            <input style="width: 400px; font-weight: 200" type="text" value="{{ucfirst($prod->descrip4)}}" class="form-control form-input form-control-rounded" id="descrip4" name="descrip4" data-url="{{route('productos.update', $prod->id)}}" placeholder="Tercera descripci&oacute;n" >
                                        @else
                                            {{$prod->descrip4}}
                                        @endif
                                    </div>
                                </div>

                                @if($prod->fk_padre == '0')
                                    <div class="image-content">
                                        <img src="{{asset('img/addpicture.png')}}" class="imageth cursor_pointer rounded-circle upload_image" data-id="{{$prod->id}}" data-url="{{route('upload.image', $prod->id)}}" >
                                        <div class="imagenes{{$prod->id}} d-flex">
                                            @if(isset($prod->imagenes[0]))
                                                @php $imagenes = $prod->imagenes; @endphp
                                                @include('home.dashboard.partials.images')
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <div class="field-content mb-3">
                                        <div class="mr-3">Color:</div>
                                        <div>
                                            <input type="text" style="width: 400px; font-weight: 200" value="{{ucfirst($prod->color)}}" class="form-control form-input form-control-rounded" id="color" name="color" data-url="{{route('productos.update', $prod->id)}}" placeholder="Color del producto" >
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card">
                    <div class="card-header" id="customer-support">
                        <h5 class="mb-0">
                            <div class="btn btn-link d-flex align-items-center justify-content-between flex-grow" data-toggle="collapse" data-target="#customer-support-collapse-00" aria-expanded="true" aria-controls="customer-support-collapse-00">
                                <div>
                                    No hay productos pendientes por revisar o su busqueda no arrojo resultados
                                </div>
                                <a href="#" class="precio" target="_blank">
                                    :)
                                </a>
                            </div>
                        </h5>
                    </div>
                </div>
            @endif



        </div>
        <div class="tab-pane fade promo-data" aria-labelledby="saasland-tab-5422" role="tabpanel" id="saasland-tab-content-5422">
            @if(isset($promociones[0]))
                @foreach($promociones as $promo)
                    <div class="card">
                        <div class="card-header" id="customer-support{{$promo->id}}">
                            <h5 class="mb-0">
                                <div class="btn btn-link d-flex align-items-center justify-content-between flex-grow" data-toggle="collapse" data-target="#customer-support-collapse-{{$promo->id}}" aria-expanded="true" aria-controls="customer-support-collapse-{{$promo->id}}">
                                    <div>

                                        <input class="promoinputs" placeholder="Nombre de esta promocion" name="descrip" value="{{$promo->descrip}}" data-url="{{route('promo.update')}}"  data-idpromo="{{$promo->id}}"  data-promo="1" data-method="post"  size="20" style="width: 400px; font-weight: 200" />

                                    </div>
                                    @if($promo->monto > 0)
                                        <span  class="precio"  >
                                            $  {{number_format($promo->monto)}}
                                        </span>
                                    @endif
                                </div>
                            </h5>
                        </div>
                        <div id="customer-support-collapse-{{$promo->id}}" class="{{(isset($collapse))? '' : 'collapse'}}" aria-labelledby="customer-support{{$promo->id}}" data-parent="#accordion-1" style="">
                            <div class="card-body">

                                <div class="field-content mb-3">
                                    <div class="mr-3">Titulo publico</div>
                                    <div>
                                        <input class="promoinputs" placeholder="Titulo publico" name="public_title" value="{{$promo->public_title}}" data-url="{{route('promo.update')}}"  data-idpromo="{{$promo->id}}"  data-promo="1" data-method="post"  size="20" style="width: 400px; font-weight: 200" />
                                    </div>
                                </div>
                                <div class="field-content mb-3">
                                    <div class="mr-3">Descripci&oacute;n publica</div>
                                    <div>
                                        <input class="promoinputs" placeholder="Descripci&oacute; publica" name="public_descrip" value="{{$promo->public_descrip}}" data-url="{{route('promo.update')}}"  data-idpromo="{{$promo->id}}"  data-promo="1" data-method="post"  size="20" style="width: 400px; font-weight: 200" />
                                    </div>
                                </div>
                                <div class="field-content mb-3">
                                    <div class="mr-3 open-promo-delete cursor-pointer" data-promo="{{$promo->id}}">Eliminar ?</div>
                                    <div class="display_none btn-promo-delete{{$promo->id}}">
                                        <button type="button" class="btn btn-outline-success promo_eliminece" data-action="{{route('promos.destroy', $promo->id)}}">Eliminece</button>
                                    </div>
                                </div>

                                <div class="field-content mb-3">
                                    <div class="mr-3">Destacada en el home</div>
                                    <div>
                                        <input type="checkbox" name="destacada" data-idpromo="{{$promo->id}}"  data-promo="1" data-method="post" {{($promo->destacada)? 'checked': ''}} value="1"  data-url="{{route('promo.update')}}" >
                                    </div>
                                </div>

                                <div class="image-content">
                                    <div class="mr-3">Imagen de promo</div><br>
                                    <img src="{{asset('img/addpicture.png')}}" class="imageth cursor_pointer rounded-circle upload_imagen_promo"  data-id="{{$promo->id}}" data-url="{{route('upload.imagen', $promo->id)}}" >
                                    <div class="imagenes{{$promo->id}} d-flex">
                                        @include('home.dashboard.partials.promo_imagen')
                                    </div>
                                </div>

                                <div class="image-content">
                                    <div class="mr-3">Imagen para el inicio de la pagina</div><br>
                                    <img src="{{asset('img/addpicture.png')}}" class="imageth cursor_pointer rounded-circle upload_imagenhome_promo"   data-id="{{$promo->id}}" data-url="{{route('upload.imagenhome', $promo->id)}}" >
                                    <div class="imageneshome{{$promo->id}} d-flex">
                                        @include('home.dashboard.partials.promo_imagen_home')
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card">
                    <div class="card-header" id="customer-support">
                        <h5 class="mb-0">
                            <div class="btn btn-link d-flex align-items-center justify-content-between flex-grow" data-toggle="collapse" data-target="#customer-support-collapse-00" aria-expanded="true" aria-controls="customer-support-collapse-00">
                                <div>
                                    No hay promociones segun la busqueda o resultados iniciales
                                </div>
                                <a href="#" class="precio" target="_blank">
                                    :)
                                </a>
                            </div>
                        </h5>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>



