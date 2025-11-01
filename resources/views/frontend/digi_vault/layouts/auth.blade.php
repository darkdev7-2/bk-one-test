<!doctype html>
<html class="no-js" lang="zxx">

@include('frontend::include.__head')

<body>

<!--[if lte IE 9]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
<![endif]-->

<!-- body overlay -->
<div class="auth-overlay-bg"></div>

<!-- Pre loader start -->
<div class="preloader">
    <div class="sk-three-bounce">
        <div class="sk-child sk-bounce1"></div>
        <div class="sk-child sk-bounce2"></div>
        <div class="sk-child sk-bounce3"></div>
    </div>
</div>
<!-- Pre loader start -->

<!-- Back to top start -->
<div class="back-to-top-wrap">
    <svg class="backtotop-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
    </svg>
</div>
<!-- Back to top end -->

<!-- Header section start -->
@include('frontend::include.__head')
<header>
    <div class="header-area header-style-one auth-header">
        <div class="container">
            <div class="header-inner">
                <div class="header-left">
                    <div class="header-logo">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset(setting('site_logo','global')) }}" alt="logo not found">
                        </a>
                    </div>
                </div>
                <div class="header-right">
                    <div class="header-quick-actions">
                        @if(setting('language_switcher'))
                            @php
                                $languages = \App\Models\Language::where('status',true)->get();
                                $current_lang = app()->getLocale();
                            @endphp
                            <div class="language-dropdown">
                                <div class="language-box language-nav">
                                    <div class="translate_wrapper">
                                        <div class="current_lang">
                                            <div class="lang"><span class="lang-txt">{{ $languages->where('locale',$current_lang)->value('name') }}</span><svg width="9" height="6" viewBox="0 0 9 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8 1.5L4.5 4.5L1 1.5" stroke="#171717" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="more_lang">
                                            @foreach(\App\Models\Language::where('status',true)->get() as $lang)
                                                @if ($current_lang != $lang->locale)
                                                    <a href="{{ route('language-update',['name'=> $lang->locale]) }}" data-lang="{{$lang->name}}" class="change_lang">
                                                        <div class="lang selected" data-value="en"><span class="lang-txt">{{$lang->name}}<span> </span></span></div>
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="header-hamburger d-lg-none">
                            <a class="sidebar-toggle" href="javascript:void(0)">
                           <span class="menu-icon">
                              <span></span>
                              <span></span>
                              <span></span>
                           </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Header section end -->

<!-- Body main wrapper start -->
<main>
@yield('content')
</main>
<!-- Body main wrapper end -->

<!-- JS here -->
@include('frontend::cookie.gdpr_cookie')
@include('frontend::include.__script')
@include('frontend::include.__notify')
{{--<script src="../assets/js/jquery-3.7.1.min.js"></script>--}}
{{--<script src="../assets/js/bootstrap.bundle.min.js"></script>--}}
{{--<script src="../assets/js/select2.js"></script>--}}
{{--<script src="../assets/js/main.js"></script>--}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        'use strict';

        // Function to toggle password visibility
        function togglePasswordVisibility(event) {
            const eyeIconSpan = event.currentTarget;
            const passwordInput = eyeIconSpan.previousElementSibling;
            const icon = eyeIconSpan.querySelector('i');

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.classList.replace('icon-eye-slash', 'icon-eye');
            } else {
                passwordInput.type = "password";
                icon.classList.replace('icon-eye', 'icon-eye-slash');
            }
        }

        // Function to validate form input

        // Attach event listener to eye icon spans
        document.querySelectorAll('.eyeicon').forEach(function (eyeIconSpan) {
            eyeIconSpan.addEventListener('click', togglePasswordVisibility);
        });
    });
</script>
</body>

</html>