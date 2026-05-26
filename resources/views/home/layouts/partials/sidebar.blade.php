<div class="pr_sidebar">


    @if(isset($instancias[0]))
        <div class="widget mb-4 p-3  bg-white" style="border-radius: 5px; max-height: 1181px; overflow: auto;   border: 1px solid #0071ba" >
            <div class="sp_widget_title d-flex align-items-center justify-content-between flex-grow">
                <div> Departamentos </div>
                <div class="text-primary remove_by_category"> x </div>
            </div>
            <div id="instrender" >
                @include('home.layouts.partials.instancias')
            </div>
        </div>

    @endif

    @if(isset($marcas[0]))
        <div class="widget mb-4 p-3  bg-white" style="border-radius: 5px;   border: 1px solid #0071ba; max-height: 1181px; overflow: auto;">
            <div class="sp_widget_title d-flex align-items-center justify-content-between flex-grow">
                <div> Laboratorios </div>
                <div class="text-primary remove_by_marca"> x </div>
            </div>
            <div id="marcasrend">
                @include('home.layouts.partials.marcas')
            </div>
        </div>
    @endif

        <div class="widget widget_price bg-white p-3 mb-3" style="border-radius: 5px; display: none;">
            <div class="sp_widget_title d-flex align-items-center justify-content-between flex-grow">
                <div> Filtrar por precio </div>
                <div class="remove_by_price text-primary"> x </div>
            </div>
            <div class="filter_slider_area">
                <div id="slider-range"></div>
                <div class="filter_content d-flex align-items-center justify-content-between flex-grow ">
                    <div class="text-left">
                        Rango:
                        <input type="text" id="amount" readonly   style="width: 115px">
                    </div>
                    <button class="btn btn-outline-primary loading_by_prices">Filtrar </button>
                </div>
            </div>
        </div>

</div>

