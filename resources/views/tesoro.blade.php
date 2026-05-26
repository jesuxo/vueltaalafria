@extends('layouts.master')
@section('title')
    BANCO DEL TESORO
@endsection
@section('css')

    <!--Swiper slider css-->
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

    <form  method="post" name="form1" id="form1" action="/tesoro" class="needs-validation">
        <div class="col-md-3 order-last" onclick="$('#resultado').html('...'); $('#resultado').addClass('alert alert-light');">

            <div class="input-group">
                <span class="input-group-text" id="basic-addon3"  style="width: 100px">Referencia</span>
                <input type="number" step="1" min="0" maxlength="9999999999" class="form-control" id="referencia" required placeholder="Ej: 565645454">
            </div>

            <div class="input-group mt-3">
                <span class="input-group-text" id="basic-addon3"  style="width: 100px">Monto Bs.  </span>
                <input type="number" min="0.01" max="99999999.99" step="0.01" class="form-control" id="monto" required style="text-align: right; " placeholder="0.00"  >
            </div>

            <button type="submit" class="btn mt-3 btn-primary" >Consultar</button>

            <br>
            <div id="resultado" class="mt-3" role="alert">

            </div>

        </div>
        @csrf
        @method('POST')
    </form>

@endsection
@section('scripts')


    <script>
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    console.log('asd');
                } else {
                    event.preventDefault();

                    $('#resultado').removeClass('alert alert-danger');
                    $('#resultado').removeClass('alert alert-light');
                    $('#resultado').html('Consultando...');

                    var monto = $('#monto').val();
                    var refer = $('#referencia').val();

                    $.ajax({
                        type: 'POST',
                        url: '/tesoro',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{ referencia: refer ,monto: monto, idReceptor: 'J308244562' },
                        success: function (data) {

                            $('#resultado').removeClass('alert alert-danger');
                            $('#resultado').removeClass('alert alert-light');

                            if(data.status == 'Error'){
                                $('#resultado').addClass('alert alert-danger');
                            }else{
                                $('#resultado').addClass('alert alert-success');
                            }

                            $('#resultado').html(data.status+': '+data.mensaje);
                        }
                    });
                }

                form.classList.add('was-validated');

            }, false)
        });

    </script>

@endsection
