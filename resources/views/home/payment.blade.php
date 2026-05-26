@extends('layouts.master')

@section('css-section')
    <style>
    header,  .footer_top{
        display: none !important;
    }
        .seo_home_area{

            z-index: 2;
            background: transparent;
        }
    .sign_info{
        padding: 50px !important;
        background: rgba(0,0,0,.6) !important;
        color: white;
    }

    canvas{
            padding: 0px 5px 11px 4px;
            border-radius: 4px;
    }

    .StripeElement {
        background-color: white;
        height: 55px;
        padding: 19px 12px;
        border-radius: 0.25rem;
        border: 1px solid #ced4da;
    }


    #submitpayment:hover{
        color: white;
        border:1px solid white !important;
    }

    .el-form-item__content{
        margin-left: 0 !important;
    }

    #card-element {
        margin: 0px;
        width: 100%;
        line-height: 1.5;
    }

    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }

    .StripeElement--invalid {
        border-color: #fa755a;
    }

    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }

    .text_box input[type="text"], .text_box textarea, .text_box input[type="password"], .text_box input[type="email"]{
        height: 40px;
    }

    .spinner {
        margin: 0px auto 0;
        width: 70px;
        text-align: center;
    }

    .bg_disabled{
        background-color: #ccc5fa;
    }

    .spinner>div {
        width: 10px;
        height: 10px;
        background-color: #fff;

        border-radius: 100%;
        display: inline-block;
        -webkit-animation: sk-bouncedelay 1.4s infinite ease-in-out both;
        animation: sk-bouncedelay 1.4s infinite ease-in-out both;
    }

    .spinner .bounce1 {
        -webkit-animation-delay: -0.32s;
        animation-delay: -0.32s;
    }

    .spinner .bounce2 {
        -webkit-animation-delay: -0.16s;
        animation-delay: -0.16s;
    }

    @-webkit-keyframes sk-bouncedelay {

        0%,
        80%,
        100% {
            -webkit-transform: scale(0)
        }

        40% {
            -webkit-transform: scale(1.0)
        }
    }

    @keyframes sk-bouncedelay {

        0%,
        80%,
        100% {
            -webkit-transform: scale(0);
            transform: scale(0);
        }

        40% {
            -webkit-transform: scale(1.0);
            transform: scale(1.0);
        }
    }
        .pagardiv{
            position: relative;
            color: white !important;
            font-size: 80px !important;
            padding-left: 10px;
        }
        .pagardiv:before{
            content: '$';
            top: -10px;
            left: -10px;
            position: absolute;
            font-size: 32px;
        }

    .text_box input[type="text"], .text_box textarea, .text_box input[type="password"], .text_box input[type="email"]{
        line-height: 30px !important;
    }
    </style>
@endsection

@section('content')
    @if(request()->getHttpHost() == 'drogueriaelarca.com')
        <div id="app">
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
                                <div class="container">
                                    <div class="sign_info">
                                        <div class="row">
                                            <div class="col-lg-5 col-sm-12 ">
                                                <div style="margin-top: auto;  text-align:center; width: 100%; border-radius: 4px; padding: 0px 0px 15px 10px; margin-top: 5px;">
                                                    <canvas id="canvas" width="205" height="200"></canvas>
                                                    <div class="widget-wrap text-left ">
                                                        <p class="f_400 f_p f_size_15 mb-0 l_height34"><span class="text-white">Email:</span> <a href="mailto:drogueriaelarca@gmail.com" class="f_400 text-white">drogueriaelarca@gmail.com</a></p>
                                                        <p class="f_400 f_p f_size_15 mb-0 l_height34"><span class="text-white">Phone:</span> <a href="https://wa.me/584247105601?text=Hola%20Srs.%20de%20DROARCA%20quisiera%20informacion%20sobre:%20" class="f_400 text-white">+58 424 7105601</a></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-7">
                                                @if(isset($amount) and $amount > 0)
                                                    <div class="login_info">
                                                    <h2 class="f_p f_600 f_size_24 text-white mb_40 ">Pago Electr&oacute;nico</h2>
                                                        <p class="pagardiv">
                                                            <?php
                                                            $length  = strlen($amount);
                                                            $decimal = substr($amount, -2, 2);
                                                            $entero  = substr($amount, 0, ($length - 2));
                                                            $int = number_format($entero,0,'','.');
                                                            echo "$int,$decimal";
                                                            ?>
                                                        </p>

                                                    <el-form method="POST" action="{{ route('procesar.pago') }}" id="payment"  :rules="rules" status-icon :model="payment" ref="payment" label-width="100px" class="login-ruleForm"   v-on:submit.prevent="submitForm()">
                                                        @csrf

                                                        @method('put')
                                                        <input type="hidden" name="monto" value="{{$amount}}" style="font-weight: 100 !important; padding: 0" />
                                                        <label>Datos de Facturaci&oacute;n</label>
                                                        <div class="form-group text_box">
                                                            <el-form-item label="" prop="cedula" >
                                                                <el-input name="cedula" required  placeholder="C&eacute;dula/Rif"  v-model="payment.cedula" ></el-input>
                                                            </el-form-item>
                                                        </div>

                                                        <div class="form-group text_box">
                                                            <el-form-item label="" prop="nombre" >
                                                                <el-input name="nombre" required  placeholder="Nombre/Raz&oacute;n Social"   v-model="payment.nombre" ></el-input>
                                                            </el-form-item>
                                                        </div>

                                                        <label>N&uacute;mero deTarjeta</label>

                                                        <div class="form-group text_box">

                                                            <div id="card-element"  >
                                                                <!-- A Stripe Element will be inserted here. -->
                                                            </div>

                                                            <div id="card-errors" role="alert"></div>
                                                        </div>



                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="formstack">
                                                                <button class="btn_three  pl-4 pr-4 pt-2 pb-2" type="submit" id="submitpayment"  @click.stop.prevent="submitForm()"  :class="{ 'bg_disabled': request_sent}" :disabled="request_sent">
                                                                    <div v-if="!request_sent"  >
                                                                        Presiona este boton para enviar tu pago
                                                                    </div>

                                                                    <div v-else class="spinner">
                                                                        <div class="bounce1"></div>
                                                                        <div class="bounce2"></div>
                                                                        <div class="bounce3"></div>
                                                                    </div>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </el-form>

                                                </div>
                                                @endif
                                                @if(isset($error))
                                                        <div class="login_info">
                                                            <h2 class="f_p f_600 f_size_24 text-white mb_40">{{$error}}</h2>
                                                        </div>
                                                @endif

                                                @if(isset($success))
                                                    <div class="login_info">
                                                        <h2 class="f_p f_600 f_size_24 text-white mb_40">Pago Exitoso</h2>
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @endif
@endsection

@section('js-section')

    <script src="https://js.stripe.com/v3/"></script>

    <script src="{{ asset('js/vue.min.js') }}"></script>

    <script  src="https://unpkg.com/element-ui@2.5.4/lib/index.js"></script>

    <script>

        Vue.config.devtools = true;

        const app = new Vue({
            data() {

                var validateEmail = (rule, value, callback) => {
                    if (value === '') {
                        callback(new Error('Please input the email'))
                    } else {
                        callback()
                    }
                };

                var validatestring = (rule, value, callback) => {
                    if (value === '') {
                        callback(new Error('Can not be empty'));
                    } else {
                        callback();
                    }
                };

                return {
                    request_sent: '',
                    card        : null,
                    stripetoken : '',
                    payment: {
                        cedula  : '',
                        nombre  : '',
                    },

                    rules: {
                        cedula: [
                            { validator: validatestring, trigger: ['blur', 'change'] },
                            { type: 'string', message: 'Cedula requerido', trigger: ['blur', 'change'] }
                        ],
                        nombre: [
                            { validator: validatestring, trigger: ['blur', 'change'] },
                            { type: 'string', message: 'Nombre requerido', trigger: ['blur', 'change'] }
                        ]
                    }
                };
            },
            watch: {
                stripetoken(after, before) {
                    if( this.stripetoken){
                        var form        = document.getElementById('payment')
                        var hiddenInput = document.createElement('input')
                        hiddenInput.setAttribute('type', 'hidden')
                        hiddenInput.setAttribute('name', 'stripeToken')
                        hiddenInput.setAttribute('value', this.stripetoken)
                        form.appendChild(hiddenInput)
                        form.submit();
                    }
                }
            },
            mounted(){
                this.loadStripe();
                this.request_sent = false
            },
            methods: {
                loadStripe(){
                    var $that = this
                    this.stripe = Stripe('pk_live_51Hbp7MCKOhyGF8Q26qzLCvA6ddaIDS3fNyRBJVBAoRFXDshW0wd2aWf4VD1O7r7BDKKojSQsTDylnaLMZTUIHeQ3008zSxuS8u')
                    //this.stripe = Stripe('pk_test_51Hbp7MCKOhyGF8Q2Fmumm2cnd9OwXm7ErAoaUB4oCyTQsvwS6dBay84IpyDlcQQkeaYZr5qrko4PAKSTfMdCDAb500JAnFCeZ2')
                    this.elements = this.stripe.elements()

                    var style = {
                        base: {
                            color: '#32325d',
                            fontFamily: 'Sans-serif',
                            fontSmoothing: 'antialiased',
                            fontSize: '14px',
                            fontWeight: '300',
                            marginLeft: '1px',
                            '::placeholder': {
                                fontSize: '15px',
                                fontWeight: '300',
                                color: '#bcc2cb',
                                fontFamily:'Sans-serif',
                            }
                        },
                        invalid: {
                            color: '#fa755a',
                            iconColor: '#fa755a'
                        }
                    }

                    this.card = this.elements.create('card', {hidePostalCode: true, style: style})
                    this.card.mount('#card-element')

                    this.card.addEventListener('change', function(event) {

                        var displayError = document.getElementById('card-errors')

                        if (event.error) {
                            displayError.textContent = event.error.message
                            $that.request_sent = false

                        } else {
                            displayError.textContent = ''

                        }
                    });
                },

                submitForm() {


                    this.request_sent = true
                    var $that = this

                    this.$refs['payment'].validate((valid) => {
                        if (valid) {

                            $that.stripe.createToken($that.card).then(function (result) {

                                if (result.error) {
                                    var errorElement         = document.getElementById('card-errors')
                                    errorElement.textContent = result.error.message
                                    $that.request_sent = false
                                    return false
                                } else {
                                    $that.stripetoken = result.token.id
                                }
                            });

                        } else {
                            $that.request_sent = false
                            return false
                        }
                    });
                },

            },

        }).$mount('#app')

        @foreach ($errors->all() as $error)
        Toast.fire({
            icon: 'error',
            title: '{{$error}}'
        });
        @endforeach


    </script>
@endsection
