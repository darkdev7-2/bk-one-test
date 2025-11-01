
<!-- JS here -->
<script src="{{ asset('front/digi_vault/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('front/digi_vault/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('front/digi_vault/js/cookie.js') }}"></script>
<script src="{{ asset('front/digi_vault/js/magnific-popup.min.js') }}"></script>
<script src="{{ asset('front/digi_vault/js/select2.js') }}"></script>
<script src="{{ asset('front/digi_vault/js/swiper.min.js') }}"></script>

<!-- Gsap script -->
<script src="{{ asset('front/digi_vault/js/gsap/gsap.min.js') }}"></script>
<script src="{{ asset('front/digi_vault/js/gsap/ScrollSmoother.min.js') }}"></script>
<script src="{{ asset('front/digi_vault/js/gsap/ScrollToPlugin.min.js') }}"></script>
<script src="{{ asset('front/digi_vault/js/gsap/ScrollTrigger.min.js') }}"></script>
<script src="{{ asset('front/digi_vault/js/gsap/SplitText.min.js') }}"></script>

<script src="{{ asset('front/digi_vault/js/main.js') }}"></script>
<script src="{{ asset('front/digi_vault/js/error-handling.js') }}"></script>

@include('global.__t_notify')
@if(auth()->check())
    <script src="{{ asset('global/js/pusher.min.js') }}"></script>
    @include('global.__notification_script',['for'=>'user','userId' => auth()->user()->id])
@endif
@yield('script')
@stack('js')
@php
    $googleAnalytics = plugin_active('Google Analytics');
    $tawkChat = plugin_active('Tawk Chat');
    $fb = plugin_active('Facebook Messenger');
@endphp

@if($googleAnalytics)
    @include('frontend::plugin.google_analytics',['GoogleAnalyticsId' => json_decode($googleAnalytics?->data,true)['app_id']])
@endif
@if($tawkChat)
    @include('frontend::plugin.tawk',['data' => json_decode($tawkChat->data, true)])
@endif
@if($fb)
    @include('frontend::plugin.fb',['data' => json_decode($fb->data, true)])
@endif


<script>
    var testimonial = new Swiper(".banner_partner_active", {
        slidesPerView: 3,
        spaceBetween: 30,
        loop: true,
        roundLengths: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false, // Keeps autoplay running after interaction
        },
        breakpoints: {
            1600: {
                slidesPerView: 7,
            },
            1200: {
                slidesPerView: 6,
            },
            992: {
                slidesPerView: 5,
            },
            768: {
                slidesPerView: 4,
            },
            576: {
                slidesPerView: 3,
            },
            460: {
                slidesPerView: 3,
            },
            0: {
                slidesPerView: 1,
            },
        },
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(function () {
            let alertBox = document.querySelector(".alert-show-status");
            if (alertBox) {
                alertBox.style.transition = "opacity 0.5s ease";
                alertBox.style.opacity = "0";
                setTimeout(() => alertBox.remove(), 500);
            }
        }, 5000);
    });

</script>