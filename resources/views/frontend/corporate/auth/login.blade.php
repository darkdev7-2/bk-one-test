@extends('frontend::layouts.auth')

@section('title')
    {{ __('Login') }}
@endsection
@section('content')
    <section class="td-authentication-section">
        <div class="container">
            <div class="auth-main-grid">
                <div class="auth-main-from">
                <div class="auth-from-inner">
                    <div class="auth-from-top-content">
                        <h3 class="title">{{ $data['title'] }}</h3>
                    </div>
                    @if ($errors->any())
                        <div class="alert bg-danger">
                            @foreach($errors->all() as $error)
                                <p class="text-light">{{$error}}</p>
                            @endforeach
                        </div>
                    @endif
                    <div class="auth-from-box">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="row gy-24">
                            <div class="col-xxl-12">
                                <div class="single-floating-input">
                                    <span class="input-icon">
                                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.19241 0.492188C4.70982 0.492188 2.69727 2.50474 2.69727 4.98734C2.69727 7.46993 4.70982 9.48248 7.19241 9.48248C9.67499 9.48248 11.6876 7.46993 11.6876 4.98734C11.6876 2.50474 9.67499 0.492188 7.19241 0.492188ZM3.5963 4.98734C3.5963 3.00125 5.20633 1.39122 7.19241 1.39122C9.17846 1.39122 10.7885 3.00125 10.7885 4.98734C10.7885 6.97342 9.17846 8.58346 7.19241 8.58346C5.20633 8.58346 3.5963 6.97342 3.5963 4.98734Z" fill="#585858"/>
                                        <path d="M5.20909 10.3816C2.28096 10.3816 0 13.0524 0 16.2253V19.8214C0 20.0696 0.201257 20.2709 0.449515 20.2709H13.935C14.1832 20.2709 14.3845 20.0696 14.3845 19.8214C14.3845 19.5732 14.1832 19.3719 13.935 19.3719H0.89903V16.2253C0.89903 13.4399 2.87992 11.2806 5.20909 11.2806H9.17541C10.4408 11.2806 11.5921 11.9083 12.3904 12.9335C12.543 13.1293 12.8254 13.1644 13.0213 13.0119C13.2172 12.8594 13.2522 12.5769 13.0998 12.381C12.1538 11.1664 10.7531 10.3816 9.17541 10.3816H5.20909Z" fill="#585858"/>
                                        <rect x="14.896" y="15" width="5" height="1" rx="0.5" fill="#585858"/>
                                        <rect x="16.896" y="18" width="5" height="1" rx="0.5" transform="rotate(-90 16.896 18)" fill="#585858"/>
                                        </svg>
                                    </span>
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="email" placeholder="" name="email">
                                        <label for="floatingPassword">{{ __('Email or Username') }} <span>*</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div class="single-floating-input">
                                    <span class="input-icon">
                                        <svg width="17" height="21" viewBox="0 0 17 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.184 8.48899H15.2011V6.25596C15.2011 2.6897 12.3193 0 8.49924 0C4.67919 0 1.7974 2.6897 1.7974 6.25596V8.48899H1.81568C0.958023 9.76774 0.457031 11.3049 0.457031 12.9569C0.457031 17.3921 4.06482 21 8.49924 21C12.9341 21 16.5424 17.3922 16.5428 12.9569C16.5428 11.3049 16.0417 9.76774 15.184 8.48899ZM2.69098 6.25596C2.69098 3.14895 5.13312 0.893578 8.49924 0.893578C11.8654 0.893578 14.3075 3.14895 14.3075 6.25596V7.39905C12.8423 5.86897 10.7804 4.91468 8.49966 4.91468C6.21837 4.91468 4.15607 5.86946 2.69098 7.40017V6.25596ZM8.49966 20.1064C4.55762 20.1064 1.35061 16.8989 1.35061 12.9569C1.35061 9.01534 4.5572 5.80826 8.49924 5.80826C12.4422 5.80826 15.6488 9.01534 15.6492 12.9569C15.6492 16.8989 12.4426 20.1064 8.49966 20.1064Z" fill="#585858"/>
                                        <path d="M8.49908 8.93567C7.26726 8.93567 6.26514 9.93779 6.26514 11.1696C6.26514 11.8679 6.60198 12.5283 7.15871 12.9474V14.7439C7.15871 15.4829 7.76013 16.0843 8.49908 16.0843C9.23761 16.0843 9.83945 15.4829 9.83945 14.7439V12.9474C10.3961 12.5278 10.733 11.8679 10.733 11.1696C10.733 9.93779 9.73041 8.93567 8.49908 8.93567ZM9.16744 12.3228C9.02984 12.4023 8.94587 12.5502 8.94587 12.7088V14.7439C8.94587 14.9906 8.74524 15.1907 8.49908 15.1907C8.25293 15.1907 8.05229 14.9906 8.05229 14.7439V12.7088C8.05229 12.5502 7.96784 12.4032 7.83023 12.3228C7.40977 12.078 7.15871 11.6468 7.15871 11.1696C7.15871 10.4307 7.76013 9.82925 8.49908 9.82925C9.23761 9.82925 9.83945 10.4307 9.83945 11.1696C9.83945 11.6468 9.58832 12.078 9.16744 12.3228Z" fill="#585858"/>
                                        </svg>
                                    </span>
                                    <div class="form-floating">
                                        <input type="password" class="form-control"  name="password" id="password" placeholder="Password">
                                        <label for="password">{{ __('Password') }} <span>*</span> </label>
                                    </div>
                                </div>
                            </div>
                            </div>
                            @if($googleReCaptcha)
                                <div class="g-recaptcha mb-3" id="feedback-recaptcha"
                                    data-sitekey="{{ json_decode($googleReCaptcha->data,true)['site_key'] }}">
                                </div>
                            @endif
                            <div class="auth-login-option">
                            <div class="animate-custom">
                                <input class="inp-cbx" id="auth_remind" type="checkbox" style="display: none;" name="remember"  />
                                <label class="cbx" for="auth_remind">
                                    <span>
                                        <svg width="12px" height="9px" viewbox="0 0 12 9">
                                        <polyline points="1 5 4 8 11 1"></polyline>
                                        </svg>
                                    </span>
                                    <span>{{ __('Remember me') }}</span>
                                </label>
                            </div>
                            @if (Route::has('password.request'))
                            <div class="forget-content">
                                <span><a href="{{ route('password.request') }}">{{ __('Forget Password') }}</a></span>
                            </div>
                            @endif
                            </div>
                            <div class="auth-bottom-content">
                            <div class="auth-from-btn-wrap">
                                <button class="site-btn gdt-btn w-100" type="submit">{{ __('Sign in') }}</button>
                            </div>
                            <div class="auth-account">
                                <p class="description">{{ __("Don't have an account?") }} <a class="link" href="{{route('register')}}">{{ __('Create account') }}</a></p>
                            </div>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
                <div class="auth-thumb-wrapper">
                <div class="auth-sing-in-thumb">
                    <img src="{{ asset($data['right_image']) }}" alt="auth-banner">
                </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    @if($googleReCaptcha)
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
    <script>
        (function($) {
            'use strict';

            // password hide show
            let eyeicon = document.getElementById('eyeicon')
            let passo = document.getElementById('passo')
            eyeicon.onclick = function() {
                if(passo.type === "password") {
                    passo.type = "text";
                    eyeicon.src = '{{ asset('front/images/icons/eye.svg') }}'
                } else {
                    passo.type = "password";
                    eyeicon.src = '{{ asset('front/images/icons/eye-off.svg') }}'
                }
            }

        })(jQuery);
    </script>
@endsection
