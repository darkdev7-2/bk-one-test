@extends('frontend::layouts.auth')
@section('title')
    {{ __('Reset Password') }}
@endsection
@section('content')
    <!-- Login Section -->
    <div class="half-authpage">
        <div class="authOne">
            <div class="auth-contents">
                @php
                    $height =
                        setting('site_logo_height', 'global') == 'auto'
                            ? 'auto'
                            : setting('site_logo_height', 'global') . 'px';
                    $width =
                        setting('site_logo_width', 'global') == 'auto'
                            ? 'auto'
                            : setting('site_logo_width', 'global') . 'px';
                @endphp
                <div class="logo">
                    <a href="{{ route('home') }}"><img src="{{ asset(setting('site_logo', 'global')) }}"
                            style="height:{{ $height }};width:{{ $width }};max-width:none" alt=""></a>
                    <div class="no-user-header">
                        @if (setting('language_switcher'))
                            <div class="language-switcher">
                                <select class="langu-swit small" name="language" id=""
                                    onchange="window.location.href=this.options[this.selectedIndex].value;">
                                    @foreach (\App\Models\Language::where('status', true)->get() as $lang)
                                        <option value="{{ route('language-update', ['name' => $lang->locale]) }}"
                                            @selected(app()->getLocale() == $lang->locale)>{{ $lang->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="color-switcher">
                            <img class="light-icon" src="{{ asset('front/images/icons/sun.png') }}" alt="">
                            <img class="dark-icon" src="{{ asset('front/images/icons/moon.png') }}" alt="">
                        </div>
                    </div>
                </div>
                <div class="contents">
                    <div class="content">
                        <h3>{{ __('Reset Password') }}</h3>
                        @if ($errors->any())
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                @foreach ($errors->all() as $error)
                                    <strong>{{ $error }}</strong>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <!-- Password Reset Token -->
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <div class="inputs">
                                <label for="">{{ __('Email Address') }}</label>
                                <input type="email" name="email" value="{{ old('email', $request->email) }}"
                                    class="box-input" required>
                            </div>

                            <div class="inputs">
                                <label for="">{{ __('New Password') }}</label>
                                <input type="password" name="password" class="box-input" required>
                            </div>
                            <div class="inputs">
                                <label for="">{{ __('Confirm Password') }}</label>
                                <input type="password" name="password_confirmation" class="box-input" required>
                            </div>

                            <div class="inputs">
                                <button type="submit" class="site-btn primary-btn w-100"><i
                                        data-lucide="check"></i>{{ __('Reset Password') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Login Section End -->
@endsection
