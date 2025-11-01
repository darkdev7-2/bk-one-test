<!doctype html>
@php
    $isRtl = isRtl(app()->getLocale());
@endphp
<html lang="{{ app()->getLocale() }}" @if($isRtl) dir="rtl" @endif>

@include('frontend::include.__head')

<body @class([
    'dark-theme' => session()->get('site-color-mode',setting('default_mode')) == 'dark',
    'rtl_mode' => $isRtl,
    'body-landing-bg'
])>
    <!--Notification-->
    @include('global._notify')

    <!-- Pre loader start -->
    <div id="preloader">
        <div class="sk-three-bounce">
        <div class="sk-child sk-bounce1"></div>
        <div class="sk-child sk-bounce2"></div>
        <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!-- Pre loader end -->

    <!-- Backtotop start -->
    @if(setting('back_to_top','permission'))
        <div class="back-to-top-wrap coinefy cursor-pointer">
            <svg class="backtotop-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
            </svg>
        </div>
    @endif
    <!-- Backtotop end -->

   <!-- Offcanvas area start -->
   <div class="fix">
      <div class="offcanvas-area">
         <div class="offcanva-wrapper">
            <div class="offcanvas-content">
               <div class="offcanvas-top d-flex justify-content-between align-items-center">
                  <div class="offcanvas-logo">
                     <a href="/">
                     <img src="{{ asset(setting('site_logo','global')) }}" alt="logo not found">
                     </a>
                  </div>
                  <div class="offcanvas-close">
                     <button class="offcanvas-close-icon animation--flip">
                     <span class="offcanvas-m-lines">
                     <span class="offcanvas-m-line line--1"></span><span
                        class="offcanvas-m-line line--2"></span><span
                        class="offcanvas-m-line line--3"></span>
                     </span>
                     </button>
                  </div>
               </div>
               <div class="mobile-menu fix"></div>
               <div class="offcanvas-content">
                  <div class="offcanvas-btn mb-3">
                     <div class="offcanvas-header-btn-wrap d-flex flex-wrap gap-2">
                        @auth('web')
                            <a class="shape-shifter-btn" href="{{ route('user.dashboard') }}">
                                <div class="btn-hover-slide">
                                   <span>{{ __('Dashboard') }}</span>
                                </div>
                            </a>
                        @else
                            <a class="shape-shifter-btn" href="{{ route('login') }}">
                                <div class="btn-hover-slide">
                                   <span>{{ __('Log In') }}</span>
                                </div>
                            </a>
                            <a class="shape-shifter-btn primary-btn" href="{{ route('register') }}">
                               <div class="btn-hover-slide">
                                  <span> {{ __('Sign Up') }}</span>
                               </div>
                            </a>
                        @endauth
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="offcanvas-overlay"></div>
   <div class="offcanvas-overlay-white"></div>
   <!-- Offcanvas area start -->

   @include('frontend::include.__header')

   <!-- Body main wrapper start -->
   <main>
        @yield('content')
   </main>
   <!-- Body main wrapper end -->

   @include('frontend::include.__footer')
   @include('frontend::cookie.gdpr_cookie')
   @include('frontend::include.__script')
</body>
</html>
