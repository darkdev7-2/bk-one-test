<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <meta name="keywords" content="@yield('meta_keywords')">
    <meta name="description" content="@yield('meta_description')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ url()->current() }}"/>
    <link rel="shortcut icon" href="{{ asset(setting('site_favicon','global')) }}" type="image/x-icon"/>
    <link rel="icon" href="{{ asset(setting('site_favicon','global')) }}" type="image/x-icon" />

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Place favicon.ico in the root directory -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('front/digi_vault/images/favicon.ico') }}">
    <!-- CSS here -->
    <link rel="stylesheet" href="{{ asset('front/digi_vault/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/digi_vault/css/fontawesome-pro.css') }}">
    <link rel="stylesheet" href="{{ asset('front/digi_vault/css/iconsax.css') }}">
    <link rel="stylesheet" href="{{ asset('front/digi_vault/css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/digi_vault/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('front/digi_vault/css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('front/digi_vault/css/styles.css') }}">

    @stack('style')
    @yield('style')
    <style>
        {{ \App\Models\CustomCss::first()->css }}
    </style>
    <title>{{ setting('site_title', 'global') }} - @yield('title')</title>
</head>