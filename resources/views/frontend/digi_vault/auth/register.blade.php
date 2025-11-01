@extends('frontend::layouts.auth')

@section('title')
    {{ __('Register') }}
@endsection
@section('content')
    <!-- Authentication section start -->
    <section class="td-authentication-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="auth-from-main">
                    <div class="auth-intro-content">
                        <h3 class="title">{{ $data['title'] }}</h3>
                    </div>
                    <div class="auth-from-box">
                        <form enctype="multipart/form-data" method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="row gy-24">
                                <div class="col-xxl-12">
                                    <div class="td-form-group">
                                        <label class="input-label">{{ __('Email') }} <span>*</span></label>
                                        <div class="input-field">
                                            <input type="email" class="form-control" name="email" required>
                                        </div>
                                    </div>
                                </div>
                                @if(getPageSetting('country_show'))
                                    <div class="col-lg-12">
                                        <div class="td-form-group">
                                            <label class="input-label">{{ __('Country') }} @if(getPageSetting('country_validation'))<span>*</span>@endif</label>
                                            <div class="input-field">
                                                <select name="country" id="countrySelect" class="select2-icons form-select">
                                                    @foreach(getCountries() as $country)
                                                        <option value="{{ $country['name'].':'.$country['dial_code'] }}" data-country-code="{{ strtolower($country['code']) }}">{{ $country['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(getPageSetting('phone_show'))
                                    <div class="col-lg-12">
                                        <div class="td-form-group">
                                            <label class="input-label">{{ __('Phone') }} @if(getPageSetting('phone_validation'))<span>*</span> @endif</label>
                                            <div class="input-field input-group">
                                                <span class="input-group-text" id="countryCode">{{ getLocation()->dial_code }}</span>
                                                <input class="form-control" type="text" name="phone" value="{{ old('phone') }}">
                                            </div>
                                            <p class="feedback-invalid">{{ __('This field is required') }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if(getPageSetting('referral_code_show'))
                                <div class="col-lg-12">
                                    <div class="td-form-group">
                                        <label class="input-label">{{ __('Referral Code') }} @if(getPageSetting('referral_code_validation'))<span>*</span> @endif</label>
                                        <div class="input-field">
                                            <input type="text" class="form-control" value="{{ old('invite',$referralCode) }}" name="invite">
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="col-lg-12">
                                    <div class="td-form-group has-right-icon">
                                        <label class="input-label">{{ __('Password') }} <span>*</span></label>
                                        <div class="input-field">
                                            <input type="password" class="form-control password-input" name="password" value="{{ old('password') }}" required>
                                            <span class="input-icon eyeicon"><i class="icon-eye-slash"></i></span>
                                        </div>
                                    </div>
                                </div>
                                @if(getPageSetting('register_custom_fields'))
                                @php
                                    $customFields = json_decode(getPageSetting('register_custom_fields'),true);
                                @endphp
                                @foreach($customFields as $key => $field)
                                    <div class="td-form-group">
                                        <label class="input-label" for="">{{ $field['name'] }} @if($field['validation'] == 'required')<span class="required">*</span> @endif</label>
                                        @if($field['type'] == 'textarea')
                                            <div class="input-field">
                                                <textarea name="custom_fields_data[{{ $field['name'] }}]" class="form-control" @if($field['validation'] == 'required') required @endif></textarea>
                                            </div>
                                        @elseif(in_array($field['type'],['file','camera']))
                                            <div class="input-field">
                                                <div class="upload-custom-file">
                                                    <input @if ($field['type'] == 'camera')
                                                        capture="user"
                                                    @endif class="upload-input" type="file" name="custom_fields_data[{{ $field['name'] }}]" id="{{ $key }}" accept=".gif, .jpg, .png" @if($field['validation'] == 'required') required @endif />
                                                    <label for="{{ $key }}">
                                                        <img class="upload-icon" src="{{ asset('front/images/icons/upload.svg') }}" alt="" />
                                                        <span>{{ $field['name'] }}</span>
                                                    </label>
                                                    <button type="button" class="file-upload-close" style="display: none;">
                                                        <i class="icon-close-circle"></i>
                                                  </button>

                                                </div>
                                            </div>  
                                            @else
                                            <div class="input-field">
                                                <input type="text" name="custom_fields_data[{{ $field['name'] }}]" class="form-control" @if($field['validation'] == 'required') required @endif>
                                            </div>


                                        @endif
                                    </div>
                                @endforeach
                            @endif

                                <div class="col-lg-12">
                                    <div class="auth-from-btn-wrap">
                                        <button type="submit" class="td-btn gradient-btn radius-8 w-100">
                                            <span class="btn-text">{{ __('Next Step') }}</span>
                                            <span class="btn-icon">
                                       <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path fill-rule="evenodd" clip-rule="evenodd" d="M2.94092 9.47925C2.94092 9.16858 3.19276 8.91675 3.50342 8.91675H15.8784C16.1891 8.91675 16.4409 9.16858 16.4409 9.47925C16.4409 9.78992 16.1891 10.0417 15.8784 10.0417H3.50342C3.19276 10.0417 2.94092 9.78992 2.94092 9.47925Z" fill="white"/>
                                          <path fill-rule="evenodd" clip-rule="evenodd" d="M10.4182 4.01912C10.6378 3.79945 10.994 3.79945 11.2136 4.01912L16.2761 9.08163C16.4958 9.30128 16.4958 9.65746 16.2761 9.87711L11.2136 14.9396C10.994 15.1593 10.6378 15.1593 10.4182 14.9396C10.1985 14.72 10.1985 14.3638 10.4182 14.1441L15.0829 9.47937L10.4182 4.81462C10.1985 4.59495 10.1985 4.23879 10.4182 4.01912Z" fill="white"/>
                                        </svg>
                                    </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="auth-account text-center mt-10">
                                <p class="description">{{ __('Already have an account?') }} <a class="td-underline-btn" href="{{ route('login') }}"> {{ __('Sign in') }}</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Authentication section end -->
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#countrySelect').select2({
                templateResult: formatState,
                templateSelection: formatState
            });

            function formatState (state) {
                if (!state.id) {
                    return state.text;
                }
                let baseUrl = "https://flagcdn.com/48x36";
                let countryCode = state.element.getAttribute('data-country-code'); // Get from data attribute

                let $state = $(
                    '<span style="display: inline-flex; align-items: center;"><img src="' + baseUrl + '/' + countryCode + '.png" style="width: 20px; height: 15px; margin-right: 5px; vertical-align: middle;" /> ' + state.text + '</span>'
                );
                return $state;
            };

            $('#countrySelect').on('change', function (e) {
                "use strict";
                e.preventDefault();
                var country = $(this).val();
                if (country) {
                    $('#countryCode').text(country.split(":")[1]);
                } else {
                    $('#countryCode').text(" "); // Or a default value
                }
            });

            // Set initial dial code
            var initialCountry = $('#countrySelect').val();
            if(initialCountry){
                $('#countryCode').text(initialCountry.split(":")[1]);
            }

        });
    </script>

<script>
    // Initialize upload 
    $(document).on('change', 'input[type="file"]', function (event) {
       var $file = $(this),
          $label = $file.next('label'),
          $labelText = $label.find('span:first'),
          $typeFileText = $label.find('.type-file-text'),
          labelDefault = "Upload Image";

       var fileName = $file.val().split('\\').pop(),
          file = event.target.files[0],
          fileType = file ? file.type.split('/')[0] : null,
          tmppath = file ? URL.createObjectURL(file) : null;

       if (fileName) {
          if (fileType === "image") {

             $label.addClass('file-ok').css('background-image', 'url(' + tmppath + ')');
          } else {

             $label.addClass('file-ok').css('background-image', 'none');
          }
          $labelText.text(fileName);
          $typeFileText.hide();
          $label.siblings('.file-upload-close').show();
       } else {
          resetUpload($file, $label, $labelText, $typeFileText,
             labelDefault);
       }
    });

    $(document).on('click', '.file-upload-close', function () {
       var $button = $(this),
          $uploadWrapper = $button.closest('.upload-custom-file'),
          $fileInput = $uploadWrapper.find('input[type="file"]'),
          $label = $fileInput.next('label'),
          $labelText = $label.find('span:first'),
          $typeFileText = $label.find('.type-file-text'),
          labelDefault = "Upload Image";

       resetUpload($fileInput, $label, $labelText, $typeFileText, labelDefault);
    });

    function resetUpload($fileInput, $label, $labelText, $typeFileText, labelDefault) {
       $fileInput.val('');
       $label.removeClass('file-ok').css('background-image', 'none');
       $labelText.text(labelDefault);
       $typeFileText.show();
       $label.siblings('.file-upload-close').hide();
    }
 </script>
@endsection

