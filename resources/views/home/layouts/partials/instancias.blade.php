
<div class="widget mb-4">
    <div class="linklist_item linklist_big">
        <div class="menu_wrap linklist_menu_item">
            <ul>
                @foreach($instancias as $indx => $inst)
                    <?php
                    // style="background: url({{asset('img/instancias/'.$inst->icon)}}) -20px -20px no-repeat ; background-size: 80px; padding-left: 50px"
                    ?>
                    <li class="link_item categoria" data-codinst="{{$inst->codinst}}" @if(($indx%2)==0)style="background: #f5f5f5" @endif>
                        <a  href="#" class="linklist_link  f_size_15" >
                            {{$inst->descrip}}
                        </a>
                    </li>

                @endforeach
            </ul>
        </div>
    </div>
</div>




