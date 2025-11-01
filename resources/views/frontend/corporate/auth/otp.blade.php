@extends('frontend::layouts.auth')
@section('title')
    {{ __('Verify OTP') }}
@endsection
@section('content')
<section class="td-authentication-section">
    <div class="container">
       <div class="auth-main-grid">
          <div class="auth-main-from">
             <div class="auth-from-inner">
                <div class="auth-from-top-content">
                    <h3 class="title">{{ __('OTP Verification') }}</h3>
                </div>
                <div class="auth-from-box">
                    <form id="otp-form" action="{{ route('otp.verify.post') }}" method="POST">
                        @csrf
                        <input type="hidden" name="phone" value="{{ auth()->user()->phone }}">

                       <div class="row gy-24">
                        <div class="col-xxl-12">
                            @if(session('success'))
                            <div class="otp-code-status">
                               <span class="title">{{ __('Enter OTP code sent to') }}
                                    <strong>{{ auth()->user()->phone }}</strong>
                                </span>
                               <span class="otp-count-time" id="otptimer"></span>
                            </div>
                            @endif
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
                               <div class="otp-verification">
                                   <input name="otp[]" type="text" maxlength="1" class="control-form" autofocus>
                                   <input name="otp[]" type="text" maxlength="1" class="control-form">
                                   <input name="otp[]" type="text" maxlength="1" class="control-form">
                                   <input name="otp[]" type="text" maxlength="1" class="control-form">
                               </div>
                           </div>
                         </div>
                      </div>
                      <div class="auth-bottom-content mt-35">
                        <div class="auth-from-btn-wrap">
                            <button class="site-btn gdt-btn w-100" type="submit">{{ __('Verify & Proceed') }}</button>
                        </div>
                        <div class="auth-account">
                            <p class="description">{{ __('Don\'t receive code ?') }} <a class="link" href="{{ route('otp.resend') }}">{{ __('Resend again') }}</a></p>
                         </div>
                      </div>
                   </form>
                </div>
             </div>
          </div>
          <div class="auth-thumb-wrapper">
             <div class="auth-otp-thumb">
                <img src="{{ asset(getPageSetting('breadcrumb')) }}" alt="otp">
             </div>
          </div>
       </div>
    </div>
 </section>
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
