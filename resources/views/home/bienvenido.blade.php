@extends('home.layouts.master')

@section('css-section')
    <style>

        body{
            background: #fafafa !important;
        }
        footer,header,.breadcrumb_area{display: none}
    </style>
@endsection

@section('content')


        <section class="seo_home_area">
            <div class="home_bubble">
                <div class="bubble b_one"></div>
                <div class="bubble b_two"></div>
                <div class="bubble b_three"></div>
                <div class="bubble b_four"></div>
                <div class="bubble b_five"></div>
                <div class="bubble b_six"></div>
            </div>
            <div class="banner_top" style=" padding-top: 30px !important;">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 seo_banner_content">
                            <section class="sign_in_area  ">
                                 <div class="row">
                                     <div class="col-lg-5 text-right">
                                         <div class="sign_info_content">
                                             <h3 class="f_p f_600 f_size_24 t_color3 mb_40 mt-4">Gracias por formar parte <br>de nuestra gente</h3>
                                             <h2 class="f_p f_400 f_size_30 mb-30" style="line-height: 60px; color: #555">Cierra esta ventana para continuar   </h2>
                                         </div>
                                     </div>
                                 </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </section>


@endsection

@section('js-section')

<script>
    document.cookie = "micsrftoken={{csrf_token()}}";
</script>

@endsection
