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
    <section class="td-authentication-section">
        <div class="container">
            <div class="auth-main-grid">
                <div class="auth-main-from">
                    <div class="auth-from-step">
                        <div class="single-step active">
                            <span class="number"><img src="{{ asset('front/theme-2/images/icons/check-bold.svg') }}"
                                    alt="check"></span>
                            <h4 class="title">{{ __('Sign up') }}</h4>
                        </div>
                        <div class="single-step">
                            <span class="number active">2</span>
                            <h4 class="title">{{ __('Information') }}</h4>
                        </div>
                    </div>
                    <div class="auth-from-inner">
                        <div class="auth-from-top-content">
                            <h3 class="title">{{ __('We\'re almost there!') }}</h3>
                        </div>
                        @if ($errors->any())
                            <div class="alert bg-danger">
                                @foreach ($errors->all() as $error)
                                    <p class="text-light">{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif
                        <div class="auth-from-box">
                            <form action="{{ route('register.now.step2') }}" method="POST">
                                @csrf

                                <div class="row gy-24">
                                    @if (getPageSetting('username_show'))
                                        <div class="col-xxl-12">
                                            <div class="single-floating-input">
                                                <span class="input-icon">
                                                    <img src="{{ asset('front/theme-2/images/icons/username.svg') }}"
                                                        alt="username">
                                                </span>
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="username" placeholder=""
                                                        name="username" value="{{ old('username') }}" required>
                                                    <label for="username">{{ __('Username') }}<span>*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-xxl-12">
                                        <div class="single-floating-input">
                                            <span class="input-icon">
                                                <img src="{{ asset('front/theme-2/images/icons/username.svg') }}"
                                                    alt="username">
                                            </span>
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="first_name" placeholder=""
                                                    name="first_name" value="{{ old('first_name') }}" required>
                                                <label for="first_name">{{ __('First Name') }}<span>*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xxl-12">
                                        <div class="single-floating-input">
                                            <span class="input-icon">
                                                <img src="{{ asset('front/theme-2/images/icons/username.svg') }}"
                                                    alt="username">
                                            </span>
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="last_name" placeholder=""
                                                    name="last_name" value="{{ old('last_name') }}" required>
                                                <label for="last_name">{{ __('Last Name') }}<span>*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                    @if (getPageSetting('gender_show'))
                                        <div class="col-xxl-12">
                                            <div class="single-floating-input">
                                                <span class="input-icon">
                                                    <svg width="26" height="19" viewBox="0 0 26 19" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M6.01401 1.09836e-06C4.46159 -0.000895122 2.96901 0.546702 1.84888 1.5281C0.728753 2.50949 0.0679794 3.84855 0.00495897 5.26481C-0.0580614 6.68107 0.48156 8.06466 1.5108 9.12579C2.54004 10.1869 3.97905 10.8433 5.52643 10.9574V15.4329H2.92602C2.79671 15.4329 2.67269 15.4798 2.58125 15.5633C2.48981 15.6468 2.43844 15.76 2.43844 15.8781C2.43844 15.9961 2.48981 16.1094 2.58125 16.1929C2.67269 16.2764 2.79671 16.3233 2.92602 16.3233H5.52643V18.5492C5.52643 18.6672 5.57781 18.7805 5.66924 18.8639C5.76068 18.9474 5.8847 18.9943 6.01401 18.9943C6.14333 18.9943 6.26734 18.9474 6.35878 18.8639C6.45022 18.7805 6.50159 18.6672 6.50159 18.5492V16.3233H9.10201C9.23132 16.3233 9.35534 16.2764 9.44677 16.1929C9.53821 16.1094 9.58958 15.9961 9.58958 15.8781C9.58958 15.76 9.53821 15.6468 9.44677 15.5633C9.35534 15.4798 9.23132 15.4329 9.10201 15.4329H6.50159V10.9574C8.04897 10.8433 9.48799 10.1869 10.5172 9.12579C11.5465 8.06466 12.0861 6.68107 12.0231 5.26481C11.96 3.84855 11.2993 2.50949 10.1791 1.5281C9.05902 0.546702 7.56643 -0.000895122 6.01401 1.09836e-06ZM6.01401 10.0907C5.01753 10.0907 4.04343 9.82094 3.21488 9.31547C2.38634 8.80999 1.74056 8.09154 1.35923 7.25097C0.97789 6.41039 0.878115 5.48545 1.07252 4.5931C1.26692 3.70075 1.74678 2.88107 2.45139 2.23772C3.15601 1.59438 4.05375 1.15625 5.03109 0.978752C6.00842 0.801253 7.02146 0.892352 7.94209 1.24053C8.86272 1.58871 9.64959 2.17832 10.2032 2.93482C10.7568 3.69132 11.0523 4.58072 11.0523 5.49055C11.0515 6.71036 10.5204 7.87998 9.57568 8.74251C8.631 9.60504 7.34999 10.09 6.01401 10.0907Z"
                                                            fill="#585858" />
                                                        <path
                                                            d="M20.1539 8.03698V1.37115L22.4097 3.43085C22.5012 3.51422 22.6251 3.56105 22.7543 3.56105C22.8835 3.56105 23.0074 3.51422 23.0988 3.43085C23.1444 3.38972 23.1807 3.34073 23.2054 3.28673C23.2301 3.23273 23.2428 3.17478 23.2428 3.11626C23.2428 3.05773 23.2301 2.99979 23.2054 2.94578C23.1807 2.89178 23.1444 2.8428 23.0988 2.80166L20.1734 0.130586C20.121 0.0834597 20.0573 0.048179 19.9873 0.0275653C19.9173 0.00695169 19.843 0.00157728 19.7703 0.0118715C19.7021 -0.00395715 19.6305 -0.00395715 19.5623 0.0118715C19.4896 0.00157728 19.4153 0.00695169 19.3453 0.0275653C19.2753 0.048179 19.2116 0.0834597 19.1592 0.130586L16.2337 2.80166C16.1881 2.8428 16.1519 2.89178 16.1272 2.94578C16.1025 2.99979 16.0898 3.05773 16.0898 3.11626C16.0898 3.17478 16.1025 3.23273 16.1272 3.28673C16.1519 3.34073 16.1881 3.38972 16.2337 3.43085C16.3252 3.51422 16.4491 3.56105 16.5783 3.56105C16.7075 3.56105 16.8314 3.51422 16.9229 3.43085L19.1787 1.37115V8.03698C17.6312 8.15192 16.1924 8.80891 15.1633 9.87044C14.1343 10.932 13.5948 12.3158 13.6578 13.7322C13.7208 15.1487 14.3814 16.488 15.5013 17.4698C16.6212 18.4517 18.1137 19 19.6663 19C21.2189 19 22.7114 18.4517 23.8313 17.4698C24.9512 16.488 25.6118 15.1487 25.6748 13.7322C25.7378 12.3158 25.1983 10.932 24.1693 9.87044C23.1402 8.80891 21.7014 8.15192 20.1539 8.03698ZM19.6663 18.104C18.6698 18.104 17.6957 17.8342 16.8672 17.3287C16.0386 16.8232 15.3928 16.1048 15.0115 15.2642C14.6302 14.4236 14.5304 13.4987 14.7248 12.6063C14.9192 11.714 15.3991 10.8943 16.1037 10.251C16.8083 9.60761 17.706 9.16948 18.6834 8.99199C19.6607 8.81449 20.6737 8.90558 21.5944 9.25376C22.515 9.60194 23.3019 10.1916 23.8555 10.9481C24.4091 11.7046 24.7046 12.594 24.7046 13.5038C24.7037 14.7236 24.1726 15.8932 23.228 16.7557C22.2833 17.6183 21.0023 18.1032 19.6663 18.104Z"
                                                            fill="#585858" />
                                                    </svg>
                                                </span>
                                                <div class="custom-select-container">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control custom-select-trigger"
                                                            id="floatingSelectInput" placeholder="Gender" readonly>
                                                        <label for="floatingSelectInput">{{ __('Gender') }} @if (getPageSetting('country_validation'))
                                                                <span>*</span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                    <div class="custom-options">
                                                        <div class="custom-option" data-value="Male">
                                                            <span>{{ __('Male') }}</span>
                                                        </div>
                                                        <div class="custom-option" data-value="Female">
                                                            <span>{{ __('Female') }}</span>
                                                        </div>
                                                        <div class="custom-option" data-value="Others">
                                                            <span>{{ __('Others') }}</span>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" id="floatingSelectValue" name="gender">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if (getPageSetting('branch_show') && branch_enabled())
                                        <div class="col-xxl-12">
                                            <div class="single-floating-input">
                                                <span class="input-icon">
                                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M9 17.004C10.7093 17.004 12.095 13.4232 12.095 9.006C12.095 4.58882 10.7093 1.008 9 1.008C7.29068 1.008 5.905 4.58882 5.905 9.006C5.905 13.4232 7.29068 17.004 9 17.004Z"
                                                            stroke="#585858" />
                                                        <path
                                                            d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17Z"
                                                            stroke="#585858" />
                                                        <path d="M1 8.995L17 9.005" stroke="#585858" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <div class="form-floating">
                                                    <select name="branch_id" class="box-input" id="branch_id">
                                                        @foreach ($branches as $branch)
                                                            <option @selected($branch->id == old('branch_id'))
                                                                value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label for="branch_id">{{ __('Branch') }} @if (getPageSetting('branch_validation'))
                                                            <span>*</span>
                                                        @endif </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($googleReCaptcha)
                                        <div class="g-recaptcha mb-3" id="feedback-recaptcha"
                                            data-sitekey="{{ json_decode($googleReCaptcha->data, true)['site_key'] }}">
                                        </div>
                                    @endif
                                    <div class="col-xxl-12">
                                        <div class="animate-custom">
                                            <input class="inp-cbx" id="auth_remind" type="checkbox"
                                                style="display: none;" name="i_agree" value="yes">
                                            <label class="cbx" for="auth_remind">
                                                <span>
                                                    <svg width="12px" height="9px" viewBox="0 0 12 9">
                                                        <polyline points="1 5 4 8 11 1"></polyline>
                                                    </svg>
                                                </span>
                                                <span>{{ __('I agree with the ') }}<a
                                                        href="{{ url('/terms-and-conditions') }}">{{ __('Terms & Condition') }}</a></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="auth-bottom-content mt-35">
                                    <div class="auth-from-btn-wrap">
                                        <button class="site-btn gdt-btn w-100"
                                            type="submit">{{ __('Finish up Account') }}</button>
                                    </div>
                                    <div class="auth-account">
                                        <p class="description">{{ __('Already have an account?') }} <a class="link"
                                                href="{{ route('login') }}">{{ __('Login') }}</a></p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="auth-thumb-wrapper">
                    <div class="auth-sing-up-thumb">
                        <img src="{{ asset($data['right_image']) }}" alt="auth-banner">
                    </div>
                </div>
            </div>
        </div>
    </section>
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
