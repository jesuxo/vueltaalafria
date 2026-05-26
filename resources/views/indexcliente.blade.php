@extends('layouts.master')
@section('title')
    Inicio
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css">

    <!--Swiper slider css-->
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        .botoncal{
            background: transparent;
            border: none;
            color: white;
        }
        .botoncal:hover{
            font-size: 13px;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class=" col-lg-3  ">
            <div class="row  ">

                <div class="col-12">

                </div>

                <div class="col-12" style="display: none">
                    <!-- card -->

                </div>

                <div class="col-12" style="display: none">
                    <!-- card -->

                </div>

            </div>
        </div>

    </div>



@endsection
@section('scripts')

    <!-- App js -->
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

@endsection
