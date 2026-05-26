@extends('layouts.master')

@section('css-section')
    <style>

        body{
            background: #fafafa !important;
        }
        .el-form-item__content{
            margin-left: 0 !important;
        }
    </style>
@endsection
@section('content')
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

                                <div class="row">

                                    <div class="col-lg-5">
                                            <div class="login_info formstack">
                                                <h2 class="f_p f_600 f_size_24 t_color3 mb_40">Registro</h2>

                                                <el-form method="POST"  action="{{ route('register') }}" id="newuser" status-icon  :model="newuser" :rules="rules" ref="newuser" label-width="100px" class="login-ruleForm" v-on:submit.prevent="submitForm()"  >
                                                    @csrf
                                                    <input type="hidden" name="type" value="{{(isset($type))? $type : 'cliente'}}" />
                                                     <div class="form-group text_box">

                                                        <el-form-item label="" prop="name" >
                                                            <el-input name="name" placeholder="Ingrese su nombre "   v-model.text="newuser.name" ></el-input>
                                                        </el-form-item>
                                                    </div>

                                                    <div class="form-group text_box">

                                                        <el-form-item label="" prop="email" >
                                                            <el-input name="email"  placeholder="correo@ejemplo.com"  v-model.text="newuser.email" ></el-input>
                                                        </el-form-item>
                                                    </div>

                                                    <div class="form-group text_box">

                                                        <el-form-item label="" prop="password">
                                                            <el-input name="password"  type="password" placeholder="******"  v-model="newuser.password" autocomplete="off"></el-input>
                                                        </el-form-item>
                                                    </div>


                                                    <div class="d-flex justify-content-between align-items-center">

                                                        <button class="btn_three  pl-4 pr-4 pt-2 pb-2"  type="button" id="submitNewUser" class="" @click="submitForm()" :class="{ 'bg_disabled': request_sent}" :disabled="request_sent">
                                                            <div v-if="!request_sent"  >
                                                               Registrarse
                                                            </div>

                                                            <div v-else class="spinner">
                                                                <div class="bounce1"></div>
                                                                <div class="bounce2"></div>
                                                                <div class="bounce3"></div>
                                                            </div>
                                                        </button>

                                                    </div>

                                                </el-form>


                                            </div>
                                        </div>
                                    <div class="col-lg-2"></div>

                                    <div class="col-lg-5 text-right">
                                            <div class="sign_info_content">
                                                <h3 class="f_p f_600 f_size_24 t_color3 mb_40 mt-4">Ya tienes un usuario?</h3>
                                                <h2 class="f_p f_400 f_size_30 mb-30" style="line-height: 60px; color: #555">Inicia sesi&oacute;n<br> empieza a buscar <br> productos </h2>
                                                <a href="{{route('login')}}" class="seo_btn seo_btn_two btn_hover" style="margin: 30px 0;">Ingresar</a>
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
    <style>

        .seo_home_area{margin-bottom: 0px !important;
            padding-bottom: 135px !important;
        }
        .seo_banner_content{
            margin-top: 0px !important;
        }

        .sec_pad {
            padding: 0px !important;
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

        #submitNewUser{
            border:1px solid #0071ba !important;

        }

        .sign_info{
            background: rgba(251,251,251,0.8) !important;
        }
    </style>
@endsection

@section('js-section')

    <script src="{{ asset('js/vue.min.js') }}"></script>
    <script  src="https://unpkg.com/element-ui@2.5.4/lib/index.js"></script>

    <script>

        Vue.config.devtools = true;

        const app = new Vue({
            data() {

                var validateForgottenEmail = (rule, value, callback) => {
                    if (value === '') {
                        callback(new Error('Ingrese su correo electrónico'));
                    } else {

                        callback();
                    }
                };
                var validateEmail = (rule, value, callback) => {
                    if (value === '') {
                        callback(new Error('Ingrese su correo electrónico'));
                    } else {
                        callback();
                    }
                };
                var validatePass = (rule, value, callback) => {

                    if (value === '') {
                        callback(new Error('Ingrese la contraseña'));
                    } else {

                        callback();
                    }
                };
                var validatestring = (rule, value, callback) => {
                    if (value === '') {
                        callback(new Error('No puede quedar vacio'));
                    } else {
                        callback();
                    }
                };

                return {

                    elements: null,
                    card    : null,
                    request_sent: null,
                    newuser: {
                        password: '',
                        name : '',
                        email: '',
                    },

                    rules: {
                        password: [
                            { validator: validatePass, trigger: ['blur', 'change']}
                        ],
                        email: [
                            { validator: validateEmail, trigger: ['blur', 'change'] },
                            { type: 'email', message: 'Por favor revise su correo electrónico', trigger: ['blur', 'change'] }
                        ],
                        name: [
                            { validator: validatestring, trigger: ['blur', 'change'] },
                            { type: 'string', message: 'No puede quedar vacio', trigger: ['blur', 'change'] }
                        ]
                    }
                };
            },

            mounted: function(){
                this.request_sent = false;
            },

            methods: {

                submitForm() {
                    this.request_sent = true;
                    var $that = this;
                    this.$refs['newuser'].validate((valid) => {
                        if (valid) {

                            var form = document.getElementById('newuser');
                            form.submit();

                        } else {
                            notify('error','Por favor revise los datos ingresados');
                            this.request_sent = false;
                            return false;
                        }
                    });
                }
            },

        }).$mount('#app')

        @foreach ($errors->all() as $error)
            notify('error','{{$error}}');
        @endforeach
    </script>
@endsection
