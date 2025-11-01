@extends('frontend::layouts.app')
@section('content')
    <!-- Breadcrumb area start -->
    <section class="td-breadcrumb-area valign breadcrumb-overlay breadcrumb-space p-relative z-index-1 fix" data-background="{{ asset('front/theme-2/images/breadcrumb/breadcrumb-bg.png') }}">
        <div class="container">
        <div class="row align-items-center justify-content-center">
            <div class="col-xxl-5 col-xl-6 col-lg-6">
                <div class="breadcrumb-content text-center">
                    <div class="breadcrumb-title-wraper mb-15">
                        <h1 class="breadcrumb-title">
                            @yield('title')
                            {{-- <span class="gd-text">@yield('color_title')</span> --}}
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
    <!-- Breadcrumb area end -->

    @yield('page-content')

@endsection
