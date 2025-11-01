<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="keywords" content="@yield('meta_keywords')">
    <meta name="description" content="@yield('meta_description')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ url()->current() }}"/>
    <link rel="shortcut icon" href="{{ asset(setting('site_favicon','global')) }}" type="image/x-icon"/>
    <link rel="icon" href="{{ asset(setting('site_favicon','global')) }}" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('front/theme-2/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/theme-2/css/fontawesome-pro.css') }}">
    <link rel="stylesheet" href="{{ asset('front/theme-2/css/odometer-default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('front/theme-2/css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('global/css/custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('front/theme-2/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('front/theme-2/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('front/theme-2/css/spacing.css') }}">
    <link rel="stylesheet" href="{{ asset('front/theme-2/css/styles.css') }}">
    @stack('style')
    @yield('style')
    <style>
      {{ \App\Models\CustomCss::first()->css }}
    </style>
    <title>{{ setting('site_title', 'global') }} - @yield('title')</title>
  </head>
