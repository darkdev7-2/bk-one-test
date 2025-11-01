@extends('frontend::layouts.auth')
@section('title')
    {{ __('Forgot password') }}
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
                @if(session('error'))
                    <div class="alert alert-danger">
                            <p>{{ session('error') }}</p>
                    </div>
                @endif
                @if(session('status'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>{{ session('status') }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                    </div>
                @endif
                <div class="auth-from-box">
                    <form method="POST" action="{{ route('password.email') }}">
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
                                  <input type="email" class="form-control" id="username" placeholder="" name="email" value="{{ old('email') }}">
                                  <label for="username">{{ __('Email') }} <span>*</span></label>
                               </div>
                            </div>
                         </div>
                      </div>
                      <div class="auth-bottom-content mt-25">
                         <div class="auth-from-btn-wrap">
                            <button class="site-btn gdt-btn w-100" type="submit">{{ __('Reset Password') }}</button>
                         </div>
                         <div class="auth-account">
                            <p class="description">{{ __('Already have an account?') }} <a class="link" href="{{ route('login') }}">{{ __('Login here') }}</a></p>
                         </div>
                      </div>
                   </form>
                </div>
             </div>
          </div>
          <div class="auth-thumb-wrapper">
             <div class="auth-reset-thumb">
                <img src="{{ asset($data['right_image']) }}" alt="auth-thumb">
             </div>
          </div>
       </div>
    </div>
 </section>
@endsection


