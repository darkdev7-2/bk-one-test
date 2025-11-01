@extends('frontend::layouts.auth')

@section('title')
    {{ __('Register') }}
@endsection

@section('content')
<section class="td-authentication-section">
    <div class="container">
       <div class="auth-main-grid">
        <div class="auth-main-from">
            <div class="auth-from-step ">
               <div class="single-step">
                  <span class="number active">1</span>
                  <h4 class="title">{{ __('Sign up') }}</h4>
                </div>
               <div class="single-step">
                  <span class="number">2</span>
                  <h4 class="title">{{ __('Information') }}</h4>
                </div>
            </div>
            <div class="auth-from-inner">
                <div class="auth-from-top-content">
                    <h3 class="title">{{ data_get($data,'title',__('Create an account')) }}</h3>
                </div>
                @if ($errors->any())
                    <div class="alert bg-danger">
                        @foreach($errors->all() as $error)
                            <p class="text-light">{{$error}}</p>
                        @endforeach
                    </div>
                @endif
               <div class="auth-from-box">
                <form method="POST" enctype="multipart/form-data" action="{{ route('register') }}">
                    @csrf
                    <div class="row gy-24">
                        <div class="col-xxl-12">
                            <div class="single-floating-input">
                               <span class="input-icon">
                                  <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                     <path fill-rule="evenodd" clip-rule="evenodd" d="M7.19241 0.492188C4.70982 0.492188 2.69727 2.50474 2.69727 4.98734C2.69727 7.46993 4.70982 9.48248 7.19241 9.48248C9.67499 9.48248 11.6876 7.46993 11.6876 4.98734C11.6876 2.50474 9.67499 0.492188 7.19241 0.492188ZM3.5963 4.98734C3.5963 3.00125 5.20633 1.39122 7.19241 1.39122C9.17846 1.39122 10.7885 3.00125 10.7885 4.98734C10.7885 6.97342 9.17846 8.58346 7.19241 8.58346C5.20633 8.58346 3.5963 6.97342 3.5963 4.98734Z" fill="#585858"/>
                                     <path d="M5.20909 10.3816C2.28096 10.3816 0 13.0524 0 16.2253V19.8214C0 20.0696 0.201257 20.2709 0.449515 20.2709H13.935C14.1832 20.2709 14.3845 20.0696 14.3845 19.8214C14.3845 19.5732 14.1832 19.3719 13.935 19.3719H0.89903V16.2253C0.89903 13.4399 2.87992 11.2806 5.20909 11.2806H9.17541C10.4408 11.2806 11.5921 11.9083 12.3904 12.9335C12.543 13.1293 12.8254 13.1644 13.0213 13.0119C13.2172 12.8594 13.2522 12.5769 13.0998 12.381C12.1538 11.1664 10.7531 10.3816 9.17541 10.3816H5.20909Z" fill="#585858"/>
                                     <rect x="14.896" y="15" width="5" height="1" rx="0.5" fill="#585858"/>
                                     <rect x="16.896" y="18" width="5" height="1" rx="0.5" transform="rotate(-90 16.896 18)" fill="#585858"/>
                                  </svg>
                               </span>
                               <div class="form-floating">
                                  <input type="email" class="form-control" id="email" placeholder="" name="email" value="{{ old('email') }}">
                                  <label for="email">{{ __('Email') }} <span>*</span></label>
                               </div>
                            </div>
                         </div>
                        @if(getPageSetting('country_show'))
                        <div class="col-xxl-12">
                           <div class="single-floating-input">
                              <span class="input-icon">
                                 <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9 17.004C10.7093 17.004 12.095 13.4232 12.095 9.006C12.095 4.58882 10.7093 1.008 9 1.008C7.29068 1.008 5.905 4.58882 5.905 9.006C5.905 13.4232 7.29068 17.004 9 17.004Z" stroke="#585858"/>
                                    <path d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17Z" stroke="#585858"/>
                                    <path d="M1 8.995L17 9.005" stroke="#585858" stroke-linecap="round" stroke-linejoin="round"/>
                                 </svg>
                              </span>
                              <div class="custom-select-container">
                                 <div class="form-floating">
                                   <input type="text" class="form-control custom-select-trigger" id="floatingSelectInput" placeholder="Country" readonly>
                                   <label for="floatingSelectInput">{{ __('Country') }} @if(getPageSetting('country_validation'))<span>*</span>@endif</label>
                                 </div>
                                 <div class="custom-options">
                                    @foreach( getCountries() as $country)
                                    <div class="custom-option" data-flag="https://flagcdn.com/48x36/{{ strtolower(data_get($country,'code')) }}.png" data-value="{{ $country['name'].':'.$country['dial_code'] }}" data-code="{{ $country['dial_code'] }}">
                                        <img src="https://flagcdn.com/48x36/{{ strtolower(data_get($country,'code')) }}.png" alt="{{ $country['name'] }} Flag">
                                        <span>{{ $country['name'] }}</span>
                                    </div>
                                    @endforeach
                                 </div>
                                 <input type="hidden" id="floatingSelectValue" name="country">
                              </div>
                           </div>
                        </div>
                        @endif
                        @if(getPageSetting('phone_show'))
                        <div class="col-xxl-12">
                           <div class="floating-group">
                              <div class="country-code">
                                <input class="form-control" type="text" readonly value="{{ getLocation()->dial_code }}" id="countryCode">
                              </div>
                              <div class="single-floating-input">
                                 <span class="input-icon">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                       <path d="M1.08856 4.63C1.74233 7.18074 3.06963 9.50888 4.93157 11.3708C6.79352 13.2328 9.12166 14.5601 11.6724 15.2138C13.635 15.7134 15.3024 14.0258 15.3024 12V11.0833C15.3024 10.5773 14.8908 10.1713 14.3876 10.1208C13.5529 10.0386 12.7336 9.84145 11.9529 9.53508L10.5596 10.9284C8.29089 9.84093 6.46147 8.01151 5.37398 5.74283L6.76731 4.3495C6.46063 3.56882 6.26321 2.74952 6.18065 1.91483C6.13115 1.41067 5.72506 1 5.21906 1H4.3024C2.27657 1 0.588982 2.66742 1.08856 4.63Z" stroke="#585858" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                 </span>
                                 <div class="form-floating">
                                    <input type="text" class="form-control" id="dial-code" placeholder="" name="phone" value="{{ old('phone') }}">
                                    <label for="dial-code">{{ __('Phone') }} @if(getPageSetting('phone_validation'))<span>*</span> @endif</label>
                                 </div>
                              </div>
                           </div>
                        </div>
                        @endif
                        @if(getPageSetting('referral_code_show'))
                         <div class="col-xxl-12">
                            <div class="single-floating-input">
                               <span class="input-icon">
                                  <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                     <path d="M5.57628 5.42374C5.57628 2.98059 7.55685 1.00001 10 1.00001C12.4432 1.00001 14.4237 2.98059 14.4237 5.42374C14.4237 7.8669 12.4432 9.84747 10 9.84747C7.55685 9.84747 5.57628 7.8669 5.57628 5.42374Z" stroke="#585858" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                     <path d="M8.5127 18.9999H2.55184C1.56413 18.9999 0.832156 18.0906 1.03347 17.1236C1.89919 12.9653 5.58473 9.84103 9.99999 9.84103C11.614 9.84103 13.1305 10.2585 14.4475 10.9915" stroke="#585858" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                     <path d="M17.2458 12.2119L18.6622 13.6126C19.1117 14.0572 19.1127 14.779 18.6645 15.2248L17.2458 16.6356" stroke="#585858" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                     <path d="M18.4661 14.3865H13.3364C12.0625 14.3865 11.0297 15.4192 11.0297 16.6932C11.0297 17.9672 12.0438 19 13.3178 19" stroke="#585858" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                  </svg>
                               </span>
                               <div class="form-floating">
                                  <input type="text" class="form-control" id="ref_code" placeholder="" value="{{ old('invite',$referralCode) }}" name="invite">
                                  <label for="ref_code">{{ __('Referral Code') }} @if(getPageSetting('referral_code_validation'))<span>*</span> @endif</label>
                               </div>
                            </div>
                         </div>
                         @endif
                         <div class="col-xxl-12">
                            <div class="single-floating-input">
                               <span class="input-icon">
                                  <svg width="17" height="21" viewBox="0 0 17 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                     <path d="M15.184 8.48899H15.2011V6.25596C15.2011 2.6897 12.3193 0 8.49924 0C4.67919 0 1.7974 2.6897 1.7974 6.25596V8.48899H1.81568C0.958023 9.76774 0.457031 11.3049 0.457031 12.9569C0.457031 17.3921 4.06482 21 8.49924 21C12.9341 21 16.5424 17.3922 16.5428 12.9569C16.5428 11.3049 16.0417 9.76774 15.184 8.48899ZM2.69098 6.25596C2.69098 3.14895 5.13312 0.893578 8.49924 0.893578C11.8654 0.893578 14.3075 3.14895 14.3075 6.25596V7.39905C12.8423 5.86897 10.7804 4.91468 8.49966 4.91468C6.21837 4.91468 4.15607 5.86946 2.69098 7.40017V6.25596ZM8.49966 20.1064C4.55762 20.1064 1.35061 16.8989 1.35061 12.9569C1.35061 9.01534 4.5572 5.80826 8.49924 5.80826C12.4422 5.80826 15.6488 9.01534 15.6492 12.9569C15.6492 16.8989 12.4426 20.1064 8.49966 20.1064Z" fill="#585858"/>
                                     <path d="M8.49908 8.93567C7.26726 8.93567 6.26514 9.93779 6.26514 11.1696C6.26514 11.8679 6.60198 12.5283 7.15871 12.9474V14.7439C7.15871 15.4829 7.76013 16.0843 8.49908 16.0843C9.23761 16.0843 9.83945 15.4829 9.83945 14.7439V12.9474C10.3961 12.5278 10.733 11.8679 10.733 11.1696C10.733 9.93779 9.73041 8.93567 8.49908 8.93567ZM9.16744 12.3228C9.02984 12.4023 8.94587 12.5502 8.94587 12.7088V14.7439C8.94587 14.9906 8.74524 15.1907 8.49908 15.1907C8.25293 15.1907 8.05229 14.9906 8.05229 14.7439V12.7088C8.05229 12.5502 7.96784 12.4032 7.83023 12.3228C7.40977 12.078 7.15871 11.6468 7.15871 11.1696C7.15871 10.4307 7.76013 9.82925 8.49908 9.82925C9.23761 9.82925 9.83945 10.4307 9.83945 11.1696C9.83945 11.6468 9.58832 12.078 9.16744 12.3228Z" fill="#585858"/>
                                  </svg>
                               </span>
                               <div class="form-floating">
                                  <input type="password" class="form-control" id="password" placeholder="" name="password" value="{{ old('password') }}">
                                  <label for="password">{{ __('Password') }} <span>*</span> </label>
                               </div>
                            </div>
                         </div>
                         @if(getPageSetting('register_custom_fields'))
                         @php
                             $customFields = json_decode(getPageSetting('register_custom_fields'),true);
                         @endphp
                         @foreach($customFields as $key => $field)
                            <div class="col-xxl-12">
                              @if(in_array($field['type'],['file','camera']))
                              <div class="td-form-group">
                                 <label class="input-label" for="{{ $key }}">{{ $field['name'] }} <span>*</span></label>
                                 <div class="input-field">
                                    <div id="uploadItems">
                                       <div class="upload-custom-file">
                                             <input type="file" @if ($field['type'] == 'camera') capture="user" @endif name="custom_fields_data[{{ $field['name'] }}]"  class="upload-input" accept=".gif, .jpg, .png, .jpeg, .webp, .pdf, .svg" id="{{ $key }}">
                                             <label for="{{ $key }}">
                                                <img class="upload-icon" src="{{ asset('front/images/icons/upload.svg') }}" alt="upload file">
                                                <span><b>Upload {{ $field['name'] }}</b></span>
                                             </label>
                                             <button type="button" class="file-upload-close" style="display: none;">
                                                <i class="fa-regular fa-circle-xmark"></i>
                                             </button>
                                       </div>
                                    </div>
                                 </div>
                                 <p class="feedback-invalid">This field is required</p>
                           </div>
                              @else
                              <div class="single-floating-input">
                                  <span class="input-icon">
                                      <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM10 18C5.59 18 2 14.41 2 10C2 5.59 5.59 2 10 2C14.41 2 18 5.59 18 10C18 14.41 14.41 18 10 18Z" fill="#585858"/>
                                      </svg>
                                  </span>
                                  <div class="form-floating">
                                      @if($field['type'] == 'textarea')
                                          <textarea class="form-control" id="{{ $key }}" name="custom_fields_data[{{ $field['name'] }}]" placeholder="" @if($field['validation'] == 'required') required @endif></textarea>
                                      @else
                                          <input type="text" class="form-control" id="{{ $key }}" name="custom_fields_data[{{ $field['name'] }}]" placeholder="" @if($field['validation'] == 'required') required @endif>
                                      @endif
                                      <label for="{{ $key }}">{{ $field['name'] }} @if($field['validation'] == 'required')<span>*</span>@endif</label>
                                  </div>
                              </div>
                                @endif
                            </div>
                         @endforeach
                     @endif
                     </div>
                     <div class="auth-bottom-content mt-35">
                        <div class="auth-from-btn-wrap">
                           <button type="submit" class="site-btn gdt-btn w-100">{{ __('1/2 Next Step') }}</button>
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
             <div class="auth-sing-up-thumb">
                <img src="{{ asset($data['right_image']) }}" alt="auth-banner">
             </div>
          </div>
       </div>
    </div>
</section>
@endsection
@section('script')
    <script>
        (function($) {
            'use strict';

            // password hide show
            let eyeicon2 = document.getElementById('eyeicon2')
            let passo2 = document.getElementById('passo2')

            eyeicon2.onclick = function() {
                if(passo2.type == "password") {
                    passo2.type = "text";
                    eyeicon2.src = '{{ url("assets/front/images/icons/eye.svg") }}'
                } else {
                    passo2.type = "password";
                    eyeicon2.src = '{{ url("assets/front/images/icons/eye-off.svg") }}'
                }
            }

            // Select 2 activation
            function formatState(state) {
                if (!state.id) {
                    return state.text;
                }

                var $state = $(
                    '<span><img src="' + $(state.element).data('flag') + '" class="img-icon" /> ' + state.text + '</span>'
                );

                return $state;
            };

            $('.select2-basic-active').select2({
                templateResult: formatState,
                templateSelection: formatState,
            });

            // Country Select
            $('#countrySelect').on('change', function (e) {
                "use strict";
                e.preventDefault();
                var country = $(this).val();
                $('#dial-code').html(country.split(":")[1])
            })

        })(jQuery);
    </script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
               const selectContainer = document.querySelector('.custom-select-container');
               const selectTrigger = selectContainer.querySelector('.custom-select-trigger');
               const options = selectContainer.querySelectorAll('.custom-option');
               const hiddenInput = document.getElementById('floatingSelectValue');
               const countryCodeInput = document.getElementById('countryCode');

               selectTrigger.addEventListener('click', function () {
                  selectContainer.classList.toggle('active');
               });

               options.forEach(option => {
                  option.addEventListener('click', function () {
                     const value = option.getAttribute('data-value');
                     const text = option.querySelector('span').innerText;
                     const flagUrl = option.getAttribute('data-flag');
                     const countryCode = option.getAttribute('data-code');

                     selectTrigger.value = text;
                     hiddenInput.value = value;
                      countryCodeInput.value = countryCode;
                     selectContainer.classList.remove('active');

                     options.forEach(option => option.classList.remove('selected'));
                     option.classList.add('selected');
                  });
               });

               document.addEventListener('click', function (e) {
                  if (!selectContainer.contains(e.target)) {
                     selectContainer.classList.remove('active');
                  }
               });
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

