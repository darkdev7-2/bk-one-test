@extends('frontend::layouts.auth')

@section('title')
    {{ __('Register') }}
@endsection
@push('js')
    <script>
        "use strict"

        $('#gender, #branch_id').select2();
    </script>
@endpush
@section('content')
    <!-- Authentication section start -->
    <section class="td-authentication-section">
        <div class="container">
            <div class="auth-from-main">
                <div class="auth-intro-content">
                    <div class="back-previous">
                        <a class="td-underline-btn" href="{{ route('register') }}">
                            <span>
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M15.1875 8.99988C15.1875 8.68921 14.9357 8.43738 14.625 8.43738H2.25C1.93933 8.43738 1.6875 8.68921 1.6875 8.99988C1.6875 9.31055 1.93933 9.56238 2.25 9.56238H14.625C14.9357 9.56238 15.1875 9.31055 15.1875 8.99988Z"
                                        fill="url(#paint0_linear_213_11333)" />
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M7.71026 3.53975C7.4906 3.32008 7.13443 3.32008 6.91477 3.53975L1.85227 8.60226C1.63261 8.82191 1.63261 9.17809 1.85227 9.39774L6.91477 14.4602C7.13443 14.6799 7.4906 14.6799 7.71026 14.4602C7.92991 14.2406 7.92991 13.8844 7.71026 13.6648L3.0455 9L7.71026 4.33525C7.92991 4.11558 7.92991 3.75942 7.71026 3.53975Z"
                                        fill="url(#paint1_linear_213_11333)" />
                                    <defs>
                                        <linearGradient id="paint0_linear_213_11333" x1="15.1875" y1="9.23238"
                                            x2="8.63342" y2="15.7185" gradientUnits="userSpaceOnUse">
                                            <stop offset="1" stop-color="#F425F4" />
                                        </linearGradient>
                                        <linearGradient id="paint1_linear_213_11333" x1="7.875" y1="11.325"
                                            x2="1.94127" y2="11.5941" gradientUnits="userSpaceOnUse">
                                            <stop offset="1" stop-color="#F425F4" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                            </span>
                            <span>{{ __('Go Back') }}</span>
                        </a>
                    </div>
                    <h3 class="title">{{ __("We're almost there!") }}</h3>
                </div>

                <form id="login-form" novalidate action="{{ route('register.now.step2') }}" method="POST">
                    @csrf
                    <div class="auth-from-box">

                        <div class="row gy-24">
                            @if (getPageSetting('username_show'))
                                <div class="col-xxl-12">
                                    <div class="td-form-group">
                                        <label class="input-label">{{ __('Username') }} <span>*</span></label>
                                        <div class="input-field">
                                            <input type="text" class="form-control" name="username"
                                                value="{{ old('username') }}" required>
                                        </div>

                                    </div>
                                </div>
                            @endif
                            <div class="col-xxl-12">
                                <div class="td-form-group">
                                    <label class="input-label">{{ __('First Name') }} <span>*</span></label>
                                    <div class="input-field">
                                        <input type="text" class="form-control" name="first_name"
                                            value="{{ old('first_name') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-12">
                                <div class="td-form-group">
                                    <label class="input-label">{{ __('Last Name') }} <span>*</span></label>
                                    <div class="input-field">
                                        <input type="text" class="form-control" name="last_name"
                                            value="{{ old('first_name') }}" required>
                                    </div>
                                </div>
                            </div>

                            @if (getPageSetting('gender_show'))
                                <div class="col-xxl-12">
                                    <div class="td-form-group">
                                        <label class="input-label">{{ __('Gender') }} @if (getPageSetting('country_validation'))
                                                <span>*</span>
                                            @endif
                                        </label>
                                        <div class="input-field">
                                            <select id="select2Icons" name="gender" class="select2-icons form-select">
                                                @foreach (['Male', 'Female', 'Others'] as $gender)
                                                    <option @selected($gender == old('gender')) value="{{ $gender }}">
                                                        {{ $gender }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @if ($googleReCaptcha)
                        <div class="g-recaptcha mb-3" id="feedback-recaptcha"
                            data-sitekey="{{ json_decode($googleReCaptcha->data, true)['site_key'] }}">
                        </div>
                    @endif
                    <div class="auth-login-option">
                        <div class="animate-custom">
                            <input class="inp-cbx" id="auth_remind" name="i_agree" type="checkbox">
                            <label class="cbx" for="auth_remind">
                                <span>
                                    <svg width="12px" height="9px" viewbox="0 0 12 9">
                                        <polyline points="1 5 4 8 11 1"></polyline>
                                    </svg>
                                </span>
                                <span>{{ __('I agree with the ?') }} <a class="td-underline-btn"
                                        href="{{ url('terms-and-conditions') }}">{{ __('Terms & Condition') }}</a> </span>
                            </label>
                        </div>
                    </div>
                    <div class="auth-from-btn-wrap">
                        <button type="submit" class="td-btn gradient-btn radius-8 w-100">
                            <span class="btn-text">{{ __('Finish Up Account') }}</span>
                            <span class="btn-icon">
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_213_11379)">
                                        <path
                                            d="M16.3043 6.51872L7.9123 14.9334C7.3443 15.5014 6.59163 15.8127 5.7903 15.8127H5.7843C4.9803 15.8107 4.2263 15.4954 3.65963 14.9254L0.892965 12.1901C0.500298 11.8021 0.496298 11.1687 0.884965 10.7761C1.27363 10.3827 1.9063 10.3794 2.29963 10.7681L5.07297 13.5094C5.2683 13.7061 5.51963 13.8114 5.7883 13.8121H5.7903C6.05697 13.8121 6.3083 13.7081 6.49763 13.5194L14.889 5.10605C15.2783 4.71405 15.9123 4.71405 16.303 5.10405C16.6943 5.49405 16.695 6.12738 16.305 6.51805L16.3043 6.51872ZM3.94563 8.32405C4.46963 8.85272 5.1683 9.14405 5.9123 9.14605H5.9183C6.6603 9.14605 7.35763 8.85672 7.8863 8.32872L13.307 2.84938C13.6956 2.45672 13.6923 1.82338 13.2996 1.43472C12.907 1.04672 12.2743 1.05072 11.885 1.44205L6.46763 6.91738C6.32096 7.06472 6.12563 7.14538 5.91763 7.14538H5.9163C5.70763 7.14538 5.5123 7.06338 5.3743 6.92405L2.98163 4.45005C2.59763 4.05272 1.96496 4.04272 1.56763 4.42672C1.17096 4.81072 1.1603 5.44338 1.5443 5.84072L3.9463 8.32338L3.94563 8.32405Z"
                                            fill="white" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_213_11379">
                                            <rect width="16" height="16" fill="white"
                                                transform="translate(0.596191 0.47937)" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </span>
                        </button>
                    </div>
                    <div class="auth-account text-center mt-10">
                        <p class="description">{{ 'Already have an account?' }} <a class="td-underline-btn"
                                href="{{ route('login') }}">{{ __('Sign in') }}</a></p>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </section>
    <!-- Authentication section end -->
@endsection

@section('script')
    @if ($googleReCaptcha)
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectContainer = document.querySelector('.custom-select-container');
            const selectTrigger = selectContainer.querySelector('.custom-select-trigger');
            const options = selectContainer.querySelectorAll('.custom-option');
            const hiddenInput = document.getElementById('floatingSelectValue');

            selectTrigger.addEventListener('click', function() {
                selectContainer.classList.toggle('active');
            });

            options.forEach(option => {
                option.addEventListener('click', function() {
                    const value = option.getAttribute('data-value');
                    const text = option.querySelector('span').innerText;

                    selectTrigger.value = text;
                    hiddenInput.value = value;
                    selectContainer.classList.remove('active');

                    options.forEach(option => option.classList.remove('selected'));
                    option.classList.add('selected');
                });
            });

            document.addEventListener('click', function(e) {
                if (!selectContainer.contains(e.target)) {
                    selectContainer.classList.remove('active');
                }
            });
        });
    </script>
@endsection
