@extends('frontend::layouts.auth')
@section('title')
    {{ __('Verify Email') }}
@endsection
@section('content')
<section class="td-authentication-section">
    <div class="container">
        <div class="auth-main-grid">
            <div class="auth-main-from">
            <div class="auth-from-inner">
                <div class="auth-from-top-content">
                    <h3 class="title">{{ __('Email Verification') }}</h3>
                </div>
                <div class="auth-from-box">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <div class="row gy-24">
                            <div class="col-xxl-12">
                                @if (session('status') === 'verification-link-sent')
                                    <div class="alert alert-success">
                                        <p>{{ __('A new verification link has been sent to the email address you provided during registration.') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="auth-bottom-content">
                            <div class="auth-from-btn-wrap">
                               <button type="submit" class="site-btn gdt-btn w-100">{{ __('Resend the email') }}</button>
                            </div>
                            <div class="auth-account">
                               <p class="description">{{ __('Already have an account?') }} <a class="link" href="{{ route('login') }}">{{ __('Login') }}</a></p>
                            </div>
                         </div>
                    </form>
                </div>
            </div>
            </div>
            <div class="auth-thumb-wrapper">
                <div class="auth-sing-in-thumb">
                    <img src="{{ asset(getPageSetting('breadcrumb')) }}" alt="auth-banner">
                </div>
            </div>
        </div>
    </div>
</section>
@endsection



