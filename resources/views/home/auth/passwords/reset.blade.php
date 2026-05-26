@extends('layout-homepage.master')

@push('plugin-styles')
    {!! Html::style('/assets/plugins/sweetalert2/sweetalert2.min.css') !!}
    {!! Html::style('https://unpkg.com/element-ui@2.5.4/lib/theme-chalk/index.css') !!}
@endpush

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
                <div class="triangle b_seven" data-parallax='{"x": 20, "y": 150}'><img src="{{asset('img/seo/triangle_one.png')}}" alt=""></div>
                <div class="triangle b_eight" data-parallax='{"x": 120, "y": -10}'><img src="{{asset('img/seo/triangle_two.png')}}" alt=""></div>
                <div class="triangle b_nine"><img src="{{asset('img/seo/triangle_three.png')}}" alt=""></div>
            </div>
            <div class="banner_top" style=" padding-top: 160px !important;">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 seo_banner_content">
                            <section class="sign_in_area  ">
                                <div class="container">
                                    <div class="sign_info">
                                        <div class="row">

                                            <div class="col-lg-7">
                                                <div class="login_info  ">
                                                    <h2 class="f_p f_600 f_size_24 t_color3 mb_40">Reset Password</h2>

                                                    <el-form  method="POST" action="{{ route('password.update') }}" id="passwordUser" status-icon  :model="passwordUser" :rules="rules" ref="passwordUser" label-width="100px" class="login-ruleForm" v-on:submit.prevent="submitForm()"  >
                                                        @csrf

                                                        <input type="hidden" name="token" value="{{ $token }}">

                                                        <div class="form-group text_box">
                                                            <label for="email" class="f_p text_c f_400">Email address</label>
                                                            <el-form-item label="" prop="email" >
                                                                <el-input name="email"  placeholder="your.email@example.com" class=" form-control "  v-model="passwordUser.email" ></el-input>
                                                            </el-form-item>
                                                        </div>

                                                        <div class="form-group text_box">
                                                            <label for="password" class="f_p text_c f_400">Password</label>
                                                            <el-form-item label="" prop="password" >
                                                                <el-input name="password"  placeholder="*******" type="password"  class=" form-control "  v-model="passwordUser.password"autocomplete="off" ></el-input>
                                                            </el-form-item>
                                                        </div>

                                                        <div class="form-group text_box">
                                                            <label for="password_confirmation" class="f_p text_c f_400"> Password Confirmation</label>
                                                            <el-form-item label="" prop="password_confirmation">
                                                                <el-input name="password_confirmation"  placeholder="*******"  type="password" class=" form-control " placeholder="******"  v-model="passwordUser.password_confirmation" autocomplete="off"></el-input>
                                                            </el-form-item>
                                                        </div>


                                                        <div class="d-flex justify-content-between align-items-center ">
                                                            <div class="formstack">
                                                                <el-button  class="btn_three sign_btn_transparent pl-4 pr-4 pt-2 pb-2"  type="button"  v-on:click="submitForm('passwordUser')"   id="submitpasswordUser">Recovery</el-button>
                                                            </div>
                                                            <div class="forgotten-password">
                                                                <a href="{{route('loginform')}}"> Sign in</a>
                                                            </div>

                                                        </div>

                                                    </el-form>


                                                </div>
                                            </div>

                                            <div class="col-lg-5 text-right">

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

        .text_box input[type="text"]:focus, .text_box textarea:focus, .text_box input[type="password"]:focus, .text_box input[type="email"]:focus, .text_box input[type="text"], .text_box textarea, .text_box input[type="password"], .text_box input[type="email"] {
            border-color: unset;
            border:none;
            -webkit-box-shadow: unset;
            box-shadow: none;
            outline: unset;
        }

        .el-form-item.is-error .el-input__inner, .el-form-item.is-error .el-input__inner:focus, .el-form-item.is-error .el-textarea__inner, .el-form-item.is-error .el-textarea__inner:focus, .el-message-box__input input.invalid, .el-message-box__input input.invalid:focus{
            border:none;
            box-shadow: none;
        }

        .seo_home_area{margin-bottom: 0px !important;
            padding-bottom: 135px !important;
        }
        .seo_banner_content{
            margin-top: 0px !important;
        }

        .sec_pad {
            padding: 0px !important;
        }

        #submitpasswordUser{
            border:1px solid #0071ba !important;

        }


        .sign_info{
            background: rgba(251,251,251,0.8) !important;
        }


    </style>




@endsection



@push('plugin-scripts')

@endpush

@push('custom-scripts')

    <script src="{{ asset('js/vue.min.js') }}"></script>

    <script  src="https://unpkg.com/element-ui@2.5.4/lib/index.js"></script>

    <script>

        Vue.config.devtools = true;

        const app = new Vue({
            data() {

                var validateEmail = (rule, value, callback) => {
                    if (value === '') {
                        callback(new Error('Please input the email'));
                    } else {
                        callback();
                    }
                };

                var validatePass = (rule, value, callback) => {

                    if (value === '') {
                        callback(new Error('Please input the password'));
                    } else {

                        callback();
                    }
                };

                return {

                    passwordUser: {
                        email: '',
                        password: '',
                        password_confirmation: '',
                    },

                    rules: {
                        email: [
                            { validator: validateEmail, trigger: ['blur', 'change'] },
                            { type: 'email', message: 'Please input correct email address', trigger: ['blur', 'change'] }
                        ],
                        password: [
                            { validator: validatePass, trigger: ['blur', 'change']}
                        ],
                        password_confirmation: [
                            { validator: validatePass, trigger: ['blur', 'change']}
                        ]
                    }
                };
            },

            methods: {

                submitForm(ref) {
                    var $that = this;
                    this.$refs[ref].validate((valid) => {
                        if (valid) {
                            var form = document.getElementById(ref);
                            form.submit();
                        } else {
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


    </script>






@endpush
