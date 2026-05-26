@extends('layouts.master')

@section('css-section')
    <style>

        body{
            background: #fafafa !important;
        }
        .el-form-item__content{
            margin-left: 0 !important;
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
                                            <div class="login_info  ">
                                                <h2 class="f_p f_600 f_size_24 t_color3 mb_40"  >Inicio de sesi&oacute;n</h2>

                                                <el-form  method="POST"  action="{{ route('login') }}" id="LoginUser" status-icon  :model="LoginUser" :rules="rules" ref="LoginUser" label-width="100px" class="login-ruleForm" v-on:submit.prevent="submitForm()"  >
                                                    @csrf

                                                    <div class="form-group text_box">
                                                        <el-form-item label="" prop="email" >
                                                            <el-input name="email"  placeholder="correo@ejemplo.com"   @keyup.enter.native="submitForm('LoginUser')"   v-model="LoginUser.email" ></el-input>
                                                        </el-form-item>
                                                    </div>

                                                    <div class=" text_box">

                                                        <el-form-item label="" prop="password">
                                                            <el-input name="password"  type="password"   placeholder="******"  @keyup.enter.native="submitForm('LoginUser')"  v-model="LoginUser.password" autocomplete="off"></el-input>
                                                        </el-form-item>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="formstack">
                                                            <button class="btn_three  pl-4 pr-4 pb-2" type="button" id="submitLoginUser"  @click="submitForm('LoginUser')" :class="{ 'bg_disabled': request_sent}" :disabled="request_sent">
                                                                <div v-if="!request_sent"  >
                                                                    Entrar
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
                                        </div>
                                        <div class="col-lg-2 text-right"></div>
                                        <div class="col-lg-5 text-right">
                                            <div class="sign_info_content">
                                                <h3 class="f_p f_600 f_size_24 t_color3 mb_40 mt-4">Si no tienes un usuario</h3>
                                                <h2 class="f_p f_400 f_size_30 mb-30" style="line-height: 60px; color: #555">Por favor presiona el  <br> bot&oacute;n de abajo    </h2>
                                                <a href="{{route('signup')}}" class="seo_btn seo_btn_two btn_hover" style="margin: 30px 0;">Registrarme</a>
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


@endsection

@section('js-section')

    <script src="{{ asset('js/vue.min.js') }}"></script>

    <script  src="https://unpkg.com/element-ui@2.5.4/lib/index.js"></script>

    <script>

        Vue.config.devtools = true;

        const app = new Vue({
            data() {

                var validateEmail = (rule, value, callback) => {
                    if (value === '') {
                        callback(new Error('Por favor ingrese su email'));
                    } else {
                        callback();
                    }
                };
                var validatePass = (rule, value, callback) => {

                    if (value === '') {
                        callback(new Error('Por favor intrese su contraseña'));
                    } else {

                        callback();
                    }
                };
                var validatestring = (rule, value, callback) => {
                    if (value === '') {
                        callback(new Error('Campo no puede quedar vacio'));
                    } else {
                        callback();
                    }
                };


                return {
                    request_sent: '',
                    reset_sent  : '',

                    LoginUser: {
                        password: '',
                        name: '',
                        email: '',
                    },

                    forgotForm: {
                        email: ''
                    },

                    rules: {
                        password: [
                            { validator: validatePass, trigger: ['blur', 'change']}
                        ],
                        email: [
                            { validator: validateEmail, trigger: ['blur', 'change'] },
                            { type: 'email', message: 'Por favor revise su correo electrónico', trigger: ['blur', 'change'] }
                        ]
                    },

                    forgotRules: {
                        email: [
                            { validator: validateEmail, trigger: ['blur', 'change'] },
                            { type: 'email', message: 'Por favor revise su correo electrónico', trigger: ['blur', 'change'] }
                        ]
                    }
                };
            },

            mounted(){

                this.request_sent = false;
            },
            methods: {

                submitForm(ref) {
                    if(ref == 'LoginUser')
                        this.request_sent = true;

                    var $that = this;
                    this.$refs[ref].validate((valid) => {
                        if (valid) {
                            var form = document.getElementById(ref);
                            form.submit();
                        } else {
                            this.request_sent = false;
                            this.reset_sent   = false;
                            return false;
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

        @if(session()->has('status'))
        Toast.fire({
            icon: 'success',
            title: 'Recovery Details -  Password reset link sent to your email'
        });

        @endif
    </script>

@endsection