
<div class="widget mb-4" style="height: 300px; overflow: auto">
    <div class="linklist_item linklist_big">
        <div class="menu_wrap linklist_menu_item">
            <ul>
                @foreach($marcas as $marca)
                    <?php
                    // style="background: url({{asset('img/instancias/'.$inst->icon)}}) -20px -20px no-repeat ; background-size: 80px; padding-left: 50px"
                    ?>
                    <li class="link_item marca" data-marca="{{$marca->marca}}">
                        <a  href="#" class="linklist_link  f_size_15" >
                            {{$marca->marca}}
                        </a>
                    </li>

                @endforeach
            </ul>
        </div>
    </div>
</div>




