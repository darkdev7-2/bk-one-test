@extends('frontend::layouts.auth')
@section('title')
    {{ __('OTP verification') }}
@endsection
@section('content')
    <!-- Authentication section start -->
    <section class="td-authentication-section">
        <div class="container">
            <div class="auth-from-main">
                <div class="auth-intro-content">
                    <h2 class="title">{{ __('OTP Verification') }}</h2>
                </div>
                @if ($errors->any())
                    <div class="alert bg-danger">
                        @foreach($errors->all() as $error)
                            <p class="text-light">{{$error}}</p>
                        @endforeach
                    </div>
                @endif

                <div class="auth-from-box">
                    <form id="otp-form" action="{{ route('otp.verify.post') }}" method="POST" >
                        @csrf
                        <input type="hidden" name="phone" value="{{ auth()->user()->phone }}">

                        <div class="row gy-24">
                            <div class="col-xxl-12">
                                <div class="otp-code-status">
                                       <span class="title">{{ __('Enter OTP code sent to') }}
                                           <strong>{{ auth()->user()->phone }}</strong>
                                       </span><span class="otp-count-time" id="otptimer"></span>
                                </div>
                                @if(session('error'))
                                    <div class="alert alert-outline td-alert-danger alert-outline d-flex gap-2 align-items-center alert-dismissible fade show" role="alert">
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11 16.5C11.3117 16.5 11.5731 16.3944 11.7843 16.1832C11.9955 15.972 12.1007 15.7109 12.1 15.4C12.0993 15.0891 11.9937 14.828 11.7832 14.6168C11.5727 14.4056 11.3117 14.3 11 14.3C10.6883 14.3 10.4273 14.4056 10.2168 14.6168C10.0063 14.828 9.90073 15.0891 9.89999 15.4C9.89926 15.7109 10.0049 15.9724 10.2168 16.1843C10.4287 16.3962 10.6898 16.5015 11 16.5ZM9.89999 12.1H12.1V5.5H9.89999V12.1ZM11 22C9.47833 22 8.04833 21.7111 6.71 21.1332C5.37167 20.5553 4.2075 19.7718 3.2175 18.7825C2.2275 17.7932 1.44393 16.6291 0.866801 15.29C0.289668 13.9509 0.000734725 12.5209 1.3924e-06 11C-0.00073194 9.47906 0.288201 8.04906 0.866801 6.71C1.4454 5.37093 2.22897 4.20677 3.2175 3.2175C4.20603 2.22823 5.3702 1.44467 6.71 0.8668C8.0498 0.288933 9.4798 0 11 0C12.5202 0 13.9502 0.288933 15.29 0.8668C16.6298 1.44467 17.794 2.22823 18.7825 3.2175C19.771 4.20677 20.555 5.37093 21.1343 6.71C21.7136 8.04906 22.0022 9.47906 22 11C21.9978 12.5209 21.7089 13.9509 21.1332 15.29C20.5575 16.6291 19.774 17.7932 18.7825 18.7825C17.791 19.7718 16.6269 20.5557 15.29 21.1343C13.9531 21.7129 12.5231 22.0015 11 22Z" fill="#FF0F00"/>
                                        </svg>
                                        <div class="danger-text">
                                            {{ session('error') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-xxl-12">
                                <div class="otp-verification-input">
                                    <div class="otp-verification d-flex align-items-center justify-content-center" style="gap: 10px;">
                                        <input name="otp[]" type="text" maxlength="1" class="control-form" style="width: 60px; height: 50px; border: 2px solid black !important; text-align: center; font-size: 20px; border-radius: 8px; color: black;" autofocus>
                                        <input name="otp[]" type="text" maxlength="1" class="control-form" style="width: 60px; height: 50px; border: 2px solid black !important; text-align: center; font-size: 20px; border-radius: 8px; color: black;">
                                        <input name="otp[]" type="text" maxlength="1" class="control-form" style="width: 60px; height: 50px; border: 2px solid black !important; text-align: center; font-size: 20px; border-radius: 8px; color: black;">
                                        <input name="otp[]" type="text" maxlength="1" class="control-form" style="width: 60px; height: 50px; border: 2px solid black !important; text-align: center; font-size: 20px; border-radius: 8px; color: black;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="auth-login-option">
                        </div>
                        <div class="auth-from-btn-wrap">
                            <button class="td-btn gradient-btn radius-8 w-100" type="submit">
                                <span class="btn-text">{{ __('Verify & Proceed') }}</span>
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
                        <p class="description">{{ __('Don\'t receive code ?') }}<a class="td-underline-btn" href="{{ route('otp.resend') }}">{{ __('Resend again') }}</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Authentication section end -->
@endsection
@section('script')
<script>
    (function ($) {
       'use strict';

       const form = document.getElementById('otp-form');
       const inputs = form.querySelectorAll('.otp-verification input');

       const KEY_CODES = {
          BACKSPACE: 8,
          ARROW_LEFT: 37,
          ARROW_RIGHT: 39
       };

       function handleInput(event) {
          const input = event.target;
          const nextInput = input.nextElementSibling;
          if (nextInput && input.value) {
             nextInput.focus();
             if (nextInput.value) {
                nextInput.select();
             }
          }
       }

       function handlePaste(event) {
          event.preventDefault();
          const pasteData = event.clipboardData.getData('text').slice(0, inputs.length);
          inputs.forEach((input, index) => {
             input.value = pasteData[index] || '';
          });
       }

       function handleBackspace(event) {
          const input = event.target;
          if (!input.value) {
             const previousInput = input.previousElementSibling;
             if (previousInput) {
                previousInput.focus();
             }
          }
       }

       function handleArrowNavigation(event, keyCode) {
          const input = event.target;
          if (keyCode === KEY_CODES.ARROW_LEFT) {
             const previousInput = input.previousElementSibling;
             if (previousInput) {
                previousInput.focus();
             }
          } else if (keyCode === KEY_CODES.ARROW_RIGHT) {
             const nextInput = input.nextElementSibling;
             if (nextInput) {
                nextInput.focus();
             }
          }
       }

       function setupInputEventListeners(input) {
          input.addEventListener('focus', event => {
             setTimeout(() => event.target.select(), 0);
          });

          input.addEventListener('input', handleInput);
          input.addEventListener('keydown', event => {
             if (event.keyCode === KEY_CODES.BACKSPACE) {
                handleBackspace(event);
             } else if (event.keyCode === KEY_CODES.ARROW_LEFT || event.keyCode === KEY_CODES.ARROW_RIGHT) {
                handleArrowNavigation(event, event.keyCode);
             }
          });
       }

       // Initialize the event listeners
       function initialize() {
          inputs.forEach(setupInputEventListeners);
       }

       // Run the initialization
       $(document).ready(initialize);

    })(jQuery);
 </script>
@endsection
