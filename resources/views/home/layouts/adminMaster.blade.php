<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{asset('img/logoarca.png')}}" type="image/x-icon">
    <title>DRORCA DROGUERIA EL ARCA - drogueriaelarca@gmail.com</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
    <!--icon font css-->
    <link rel="stylesheet" href="{{asset('vendors/themify-icon/themify-icons.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/elagent/style.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/flaticon/flaticon.css')}}">
    <link rel="stylesheet" href="{{asset('/assets/plugins/sweetalert2/sweetalert2.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/animation/animate.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/owl-carousel/assets/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/magnify-pop/magnific-popup.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/nice-select/nice-select.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/ui-fliter/jquery-ui.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/scroll/jquery.mCustomScrollbar.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="stylesheet" href="{{asset('css/responsive.css')}}">
    <link rel="stylesheet" href="https://unpkg.com/element-ui@2.5.4/lib/theme-chalk/index.css">

    @yield('css-section')

    <style>
        .nav-link, .btn, a, button {
            font-weight: 100 !important;
        }
        .pr_details {
            padding-top: 50px;
        }
        .breadcrumb_area {
            padding: 10px;
            background: none;
        }
        .promo-bg{
            height: 80px;
            background-size: cover !important;
        }
        .body_wrapper .page_content {min-height: calc(100vh - 205px);}
    </style>
</head>

<body >

    <div class="body_wrapper">

        @include('home.layouts.adminHeader')
        @include('home.layouts.adminBreadcrumb')

        @yield('content')

        @include('home.layouts.adminFooter')

    </div>

    <div class="modal fade text-left" id="verproducto" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="width: 40px; right: 0; position: absolute; padding: 10px;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="modal-container" >
                    <div id="content_modal-producto" class="p-4">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{{asset('js/jquery-3.2.1.min.js')}}"></script>
    <script src="{{asset('js/propper.js')}}"></script>
    <script src="{{asset('js/bootstrap.min.js')}}"></script>
    <script src="{{asset('vendors/wow/wow.min.js')}}"></script>
    <script src="{{asset('vendors/sckroller/jquery.parallax-scroll.js')}}"></script>
    <script src="{{asset('vendors/owl-carousel/owl.carousel.min.js')}}"></script>
    <script src="{{asset('vendors/imagesloaded/imagesloaded.pkgd.min.js')}}"></script>
    <script src="{{asset('vendors/isotope/isotope-min.js')}}"></script>
    <script src="{{asset('vendors/magnify-pop/jquery.magnific-popup.min.js')}}"></script>
    <script src="{{asset('vendors/nice-select/jquery.nice-select.min.js')}}"></script>
    <script src="{{asset('vendors/ui-fliter/jquery-ui.js')}}"></script>
    <script src="{{asset('vendors/scroll/jquery.mCustomScrollbar.concat.min.js')}}"></script>
    <script src="{{asset('js/plugins.js')}}"></script>
    <script src="{{asset('js/main.js')}}"></script>
    <script src="{{asset('assets/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{asset('assets/plugins/promise-polyfill/polyfill.min.js')}}"></script>
    <script src="{{asset('assets/js/sweet-alert.js')}}"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.6/dist/loadingoverlay.min.js"></script>

    <script>
        $(document).ready(function() {

            $(document).ajaxSend(function (event, jqxhr, settings) {
                $(settings.element_to_overlay).LoadingOverlay("show", {
                    imageColor: "#0071ba",
                    imageResizeFactor: 0.4,
                    imageAutoResize: false
                });
            });

            $(document).ajaxComplete(function (event, jqxhr, settings) {
               $(settings.element_to_overlay).LoadingOverlay("hide", true);
            });
        });
    </script>

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 3000
        });

        window.notify = function notify(status, message) {
            Toast.fire({
                icon: status,
                title: message
            })
        }
    </script>
    @yield('js-section')
</body>

</html>
