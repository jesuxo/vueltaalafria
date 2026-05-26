@if(isset($compra->items[0]))
    @foreach($compra->items as $items)
        @if(isset($items->producto->existen))
            <div class="divitem{{$items->id}}">
                <div class="module-wrapper module-wrapper">
                    <div style="height: 8px; background-color: rgb(247, 247, 247);"></div>
                </div>
                <div class="module-wrapper module-wrapper" style="position: relative">
                    <div class="{{($items->cantidad > $items->producto->existen)? 'cantmayor': ''}}"
                         style="display: flex; position: relative; padding: 12px 0px;" >
                        <div style="line-height: 50px; width: 15%; margin-top: 2px;" >
                            <div style="margin: 0px auto; position: relative; width: 50px;" >
                                <a style=" color: #0071ba; cursor: pointer;" href="{{route('ver.producto', $items->producto->id)}}" data-bypass="false" >
                                    @if(isset($items->producto->imagen))
                                        <img class="img-fluid ml-1" src="{{asset('img/productos/th'.$items->producto->imagen)}}" alt="" style="max-height: 50px; margin: auto;  ">
                                    @else
                                        <img class="img-fluid ml-1" src="{{asset('img/default.jpg')}}" alt="" style="max-height: 50px; margin: auto;  ">
                                    @endif
                                </a>
                                @if(isset(auth()->user()->type) and auth()->user()->type !='admin')
                                    <div class="close_btn-delete" data-url="{{route('compraitems.destroy', $items->id)}}" data-id="{{$items->id}}" >
                                        x
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div style="font-size: 13px; width: 85%; display: flex; color: black;" class="rmq-9151282f" >
                            <div style="width: {{($items->producto->existen > 0)? '84': '50'}}%; display: flex; flex-wrap: wrap;" class="rmq-3d3589b" >
                                <div style="width: 100%; display: flex;" >
                                    <div style="width: {{($items->producto->existen > 0)? '75': '100'}}%;" class="rmq-a40185f5" >
                                        <div >
                                            <a class="rmq-9484430c" style="color: black; line-height: 15px; font-size: 13px; cursor: pointer;" href="{{route('ver.producto', $items->producto->id)}}" data-bypass="false" >
                                            <a class="rmq-9484430c" style="color: black; line-height: 15px; font-size: 13px; cursor: pointer;" href="{{route('ver.producto', $items->producto->id)}}" data-bypass="false" >
                                                <div style="width: 100%;" class="rmq-9484430c clamped-name" >{{$items->producto->descrip}}</div>
                                            </a>
                                        </div>
                                        <div style="align-items: center; align-content: center; font-size: 11px;" data-testid="cartItemSizing" class="rmq-10c8c9ce d-flex"  >
                                            @if($items->producto->color)
                                                <div class="mr-1" style="height: 20px; width: 20px; background: {{'#'.$items->producto->color}}; border-radius: 50%; border: 2px solid #fff; box-shadow: 0px 2px 10px rgba(0,0,0,.1) "></div>
                                            @endif
                                            <div style="font-weight: 200">Mod: {{$items->producto->referencia}}</div>
                                        </div>
                                        @if($items->cantidad > $items->producto->existen and $items->producto->existen > 0)
                                            <div class="text-danger">Existencia menor a la cantidad solicitada</div>
                                        @endif
                                    </div>
                                    <div style="width: 25%; text-align: center;" class="rmq-ec61a5f1  {{($items->producto->existen > 0)? '': 'display_none'}}" >
                                        @if(isset(auth()->user()->type) and auth()->user()->type !='admin')
                                        <button style="cursor: pointer; height: 44px; width: 56px; border: 1px solid rgb(224, 224, 224); margin: 0px auto; padding: 0px; line-height: 40px; border-radius: 4px; font-size: 16px; color: black; font-weight: 600; z-index: 1; background-color: transparent;" data-id="{{$items->id}}" class="rmq-1687e5cd btn-cantidad cantidad{{$items->id}}" >
                                            {{$items->cantidad}}
                                        </button>
                                        @else
                                            {{$items->cantidad}}
                                        @endif
                                    </div>
                                </div>

                            </div>
                            <div style="position: relative; width:  {{($items->producto->existen > 0)? '16': '50'}}%; font-size: 14px; color: black; text-align: right; padding-right: 12px; height: 44px; display: flex; align-items: center;" class="rmq-9484430c rmq-3630089a" >
                                <div class="symbol-dolar  {{($items->producto->existen > 0)? '': 'display_none'}} " style="margin: 0px 0px 0px auto; font-weight: 200"   >
                                     {{number_format($items->precio, 2, ',', '.')}}
                                </div>
                                <div style="width: 100%; text-align: center; color: red;" class="{{($items->producto->existen > 0)? 'display_none': ''}}">
                                    Agotado
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="display_none botones-cambiar cambiar_cantidad{{$items->id}}">
                        <div class="content-btn-cambiar content-btn-cambiar{{$items->id}}">
                            <div style="position: relative">
                                <div style="display: flex; height: 3em;" >
                                    <button type="button" class="rmq-16d35361 btn-action_cantidad minus{{$items->id}} {{($items->cantidad < 2 )? 'btn-blocked' : ''}}" data-id="{{$items->id}}" data-oper="-" data-cantidad="{{$items->cantidad}}" >
                                        <svg width="24px" height="24px" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor">
                                            <path d="M5.007 11C4.45 11 4 11.448 4 12c0 .556.451 1 1.007 1h13.986C19.55 13 20 12.552 20 12c0-.556-.451-1-1.007-1H5.007z"></path>
                                        </svg>
                                    </button>
                                    <div style="width: 40%; height: 100%; display: inline-flex; justify-content: center; align-items: center;" aria-live="polite" aria-atomic="true" data-testid="quantitySelectorQty" class="rmq-b2e10ea7" >
                                        <span style="border: 0px none; clip: rect(0px, 0px, 0px, 0px); height: 1px; width: 1px; margin: -1px; overflow: hidden; padding: 0px; position: absolute;">Quantity:</span>
                                        <span class="cantidad{{$items->id}}" style="font-weight: 200; color: black;"> {{$items->cantidad}} </span>
                                    </div>
                                    <button type="button" class="rmq-16d35361 btn-action_cantidad plus{{$items->id}} {{($items->cantidad >= $items->producto->existen)? 'btn-blocked' : ''}}" data-id="{{$items->id}}" data-oper="+"  data-cantidad="{{$items->cantidad}}" >
                                        <svg width="24px" height="24px" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor">
                                            <path d="M13 5.007C13 4.45 12.552 4 12 4c-.556 0-1 .451-1 1.007V11H5.007C4.45 11 4 11.448 4 12c0 .556.451 1 1.007 1H11v5.993c0 .557.448 1.007 1 1.007.556 0 1-.451 1-1.007V13h5.993C19.55 13 20 12.552 20 12c0-.556-.451-1-1.007-1H13V5.007z"></path>
                                        </svg>
                                    </button>
                                </div>

                                <div class="close_btn-cantidades" >
                                    x
                                </div>
                            </div>
                        </div>
                        <div class="overlay-cantidad"  style="width: 100%; height: 100%; top: 0px; position: absolute; background-color: rgba(255, 255, 255, 0.7);" >

                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    @if(isset(auth()->user()->type) and auth()->user()->type =='admin')
        <div style="text-align: right; display: block;">
            Monto Total a Pagar: $ {{number_format($compra->monto,2,',','.')}}
        </div>
    @endif
@endif
