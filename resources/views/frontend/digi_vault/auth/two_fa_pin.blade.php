@extends('frontend::layouts.auth')
@section('title')
    {{ __('2FA Security') }}
@endsection
@section('content')
    <!-- Authentication section start -->
    <section class="td-authentication-section">
        <div class="container">
            <div class="auth-from-main">
                <div class="auth-intro-content">
                    <h2 class="title">{{ __('2FA Verification') }}</h2>
                </div>
                @if ($errors->any())
                    <div class="alert bg-danger">
                        @foreach($errors->all() as $error)
                            <p class="text-light">{{$error}}</p>
                        @endforeach
                    </div>
                @endif

                <div class="auth-from-box">
                    <form action="{{ route('user.setting.2fa.verify') }}" method="POST" >
                        @csrf
                        <div class="row gy-24 mb-4">
                            <div class="col-xxl-12">
                                <div class="td-form-group">
                                    <label class="input-label">{{ __('One Time Password') }}<span>*</span> </label>
                                    <div class="input-field">
                                        <input type="text" name="one_time_password" class="form-control" required>
                                    </div>
                                    <p class="feedback-invalid">{{ __('This field is required') }}</p>
                                </div>
                            </div>

                        </div>

                        <div class="auth-from-btn-wrap">
                            <button class="td-btn gradient-btn radius-8 w-100" type="submit">
                                <span class="btn-text">{{ __('Authenticate Now') }}</span>
                                <span class="btn-icon">
                              <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                 <path fill-rule="evenodd" clip-rule="evenodd" d="M2.94092 9.47925C2.94092 9.16858 3.19276 8.91675 3.50342 8.91675H15.8784C16.1891 8.91675 16.4409 9.16858 16.4409 9.47925C16.4409 9.78992 16.1891 10.0417 15.8784 10.0417H3.50342C3.19276 10.0417 2.94092 9.78992 2.94092 9.47925Z" fill="white"/>
                                 <path fill-rule="evenodd" clip-rule="evenodd" d="M10.4182 4.01912C10.6378 3.79945 10.994 3.79945 11.2136 4.01912L16.2761 9.08163C16.4958 9.30128 16.4958 9.65746 16.2761 9.87711L11.2136 14.9396C10.994 15.1593 10.6378 15.1593 10.4182 14.9396C10.1985 14.72 10.1985 14.3638 10.4182 14.1441L15.0829 9.47937L10.4182 4.81462C10.1985 4.59495 10.1985 4.23879 10.4182 4.01912Z" fill="white"/>
                              </svg>
                           </span>
                            </button>
                        </div>
                    </form>
                    <div class="auth-privacy-policy">
                        <p class="description">{{ __("Don't have an account?") }} <a class="td-underline-btn" href="{{route('register')}}">{{ __('Create account') }}</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Authentication section end -->
@endsection