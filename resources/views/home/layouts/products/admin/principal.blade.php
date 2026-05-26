<div class="search-form input-group mb-4" style="max-width: 100%; padding: 0"  >
    <input type="text" autocomplete="off" data-prodid="{{$prod->id}}"  style="font-weight: 200" name="producto_principal" value="" class="form-control search-field producto_principal" placeholder="Buscar producto principal">
    <span class="input-group-addon">
        <button type="button"   style="opacity: 0 !important;">
            <i class="ti-search cursor_pointer btn-search"></i>
        </button>
    </span>
    <input type="hidden" name="post_type"  data-unset="1"   value="product">
</div>


<div>
    @foreach($principales as $p)
        <div class="email-list-item mb-2">
            <div class="email-list-detail">
                <div class="d-flex">
                    <div class="text-center">
                        @if(isset($p->imagenes[0]))
                            <img class="rounded-circle avatar-logo mr-2" src="{{asset('img/productos/th'.$p->imagenes[0]->url)}}" alt="" style="max-height: 60px; margin: auto">
                        @else
                            <img class="rounded-circle avatar-logo mr-2" src="{{asset('img/default.jpg')}}" alt="" style="max-height: 60px; margin: auto">
                        @endif
                    </div>
                    <div class="d-flex flex-column ml-2 open_producto" data-url="{{route('productos.edit', $p->id)}}">
                        <span class="from"> {{$p->descrip1}} </span>
                        <p class="msg">Ref: <span class="text-primary"> {{$p->referencia}} </span> | Precio: <span class="text-primary"> $ {{number_format($p->precio, 2, ',', '.')}} </span></p>
                    </div>
                    <div class="text-right pr-3" style="flex-grow: 1;">
                        <i class="icon_box-checked cursor_pointer text-primary {{(isset($prod->padre->id)  and $prod->padre->id == $p->id)? 'h4' : ''}}" data-padre="{{$p->id}}"  data-prodid="{{$prod->id}}"></i>
                    </div>
                </div>
                @if(isset($p->hijos[0]))
                    @foreach($p->hijos as $hijo)
                        <div class="row">
                            <div class="col-10">
                                <div class="d-flex flex-column ml-2 hijo open_producto" data-url="{{route('productos.edit', $hijo->id)}}" style="margin-left: 82px !important;">
                                    {{$hijo->descrip1}} | ref: {{$hijo->referencia}}
                                </div>
                            </div>
                            <div class="col-2">
                                <span class="remove_hijo" data-hijo="{{$hijo->id}}" data-prodid="{{$prod->id}}" style="color: red;"> x</span>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endforeach
</div>

@if(!isset($principales[0]))
    <div class="card-body card-new-prod">
        <div class="field-content mb-3">
            <div class="mr-3">Descripci&oacute;n 1:</div>
            <div>
                <input data-unset="1" type="text" value="{{ucfirst($prod->descrip1)}}" class="form-control form-input form-control-rounded" id="newdescrip1" name="descrip1"  placeholder="Primera descripci&oacute;n " >
            </div>
        </div>

        <div class="field-content mb-3">
            <div class="mr-3">Precio?</div>
            <div>
                <input data-unset="1" type="checkbox" name="precio_visible"  checked value="1" id="newprecio_visible"   >
            </div>
        </div>

        <div class="field-content mb-3">
            <div class="mr-3">Referencia:</div>
            <div>
                <input data-unset="1" type="text" value="{{ucfirst($prod->referencia)}}" class="form-control form-input form-control-rounded" id="newreferencia" name="referencia"  placeholder="Referencia del producto" >
            </div>
        </div>

        <div class="field-content mb-3">
            <div class="mr-3">Marca:</div>
            <div>
                <input data-unset="1" type="text" value="{{ucfirst($prod->marca)}}" class="form-control form-input form-control-rounded" id="newmarca" name="marca"  placeholder="Marca del producto" >
            </div>
        </div>

        <div class="field-content mb-3">
            <div class="mr-3">Descripci&oacute;n 2:</div>
            <div>
                <input data-unset="1" type="text" value="{{ucfirst($prod->descrip2)}}" class="form-control form-input form-control-rounded" id="newdescrip2" name="descrip2"  placeholder="Segunda descripci&oacute;n" >
            </div>
        </div>

        <div class="field-content mb-3">
            <div class="mr-3">Descripci&oacute;n 3:</div>
            <div>
                <input data-unset="1" type="text" value="{{ucfirst($prod->descrip3)}}" class="form-control form-input form-control-rounded" id="newdescrip3" name="descrip3"  placeholder="Tercera descripci&oacute;n" >
            </div>
        </div>

        <div class="field-content mb-3">
            <div class="mr-3">Descripci&oacute;n 4:</div>
            <div>
                <input data-unset="1" type="text" value="{{ucfirst($prod->descrip4)}}" class="form-control form-input form-control-rounded" id="newdescrip4" name="descrip4"  placeholder="Tercera descripci&oacute;n" >
            </div>
        </div>

        <input type="hidden" value="{{$prod->precio}}"     id="newprecio"     name="precio">
        <input type="hidden" value="{{$prod->codinst}}"    id="newcodinst"    name="codinst">
        <input type="hidden" value="{{$prod->existencia}}" id="newexistencia" name="existencia">

        <button class="btn btn-primary guardar-principal mb-5" data-prodid="{{$prod->id}}" >Guardar producto principal</button>
    </div>
@endif
