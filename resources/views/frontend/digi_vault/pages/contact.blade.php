 @extends('frontend::pages.index')
 @section('title')
     {{ $data['title'] }}
 @endsection
 @section('meta_keywords')
     {{ $data['meta_keywords'] }}
 @endsection
 @section('meta_description')
     {{ $data['meta_description'] }}
 @endsection
 @section('page-content')
     <!-- Contact info section start -->
     <div class="td-contact-info-section position-relative z-index-11 section_space fix">
         <div class="container">
             <div class="row">
                 <div class="contact-info-grid">
                     <div class="section-title-wrapper text-xs-center">
                         <span class="section-subtitle has_fade_anim">{{ $data['title_small'] }}</span>
                         <h2 class="section-title has_fade_anim mb-15">{{ $data['title_big'] }}</h2>
                     </div>
                     <div class="contact-info has_fade_anim">
                         <div class="icon">
                             <i class="{{ $data['widget_one_icon'] }}"></i>
                         </div>
                         <div class="contents">
                             <h3 class="title">{{ $data['widget_one_title'] }}</h3>
                             <p class="description">{{ $data['widget_one_description'] }}</p>
                         </div>
                     </div>
                     <div class="contact-info has_fade_anim">
                         <div class="icon">
                             <i class="{{ $data['widget_two_icon'] }}"></i>
                         </div>
                         <div class="contents">
                             <h3 class="title">{{ $data['widget_two_title'] }}</h3>
                             <p class="description">{{ $data['widget_two_description'] }}</p>
                         </div>
                     </div>
                     <div class="contact-info has_fade_anim">
                         <div class="icon">
                             <i class="{{ $data['widget_three_icon'] }}"></i>
                         </div>
                         <div class="contents">
                             <h3 class="title">{{ $data['widget_three_title'] }}</h3>
                             <p class="description">{{ $data['widget_three_description'] }}</p>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         <div class="contact-info-glows">
             <div class="glow-one">
                 <img src="{{ asset('front/digi_vault/images/contact/glow-01.png') }}" alt="Contact Glow">
             </div>
         </div>
     </div>
     <!-- Contact info section end -->

     <!-- Contact form section start -->
     <div class="td-contact-form-section alice-blue-bg position-relative z-index-11 section_space">
         <div class="container">
             <div class="row justify-content-center">
                 <div class="col-xxl-8">
                     <div class="section-title-wrapper section_title_space text-center">
                         <h2 class="section-title has_fade_anim mb-15">{{ $data['form_title'] }}</h2>
                         <div class="text has_fade_anim">
                             <p>{{ $data['form_description'] }}</p>
                         </div>
                     </div>
                 </div>
                 <div class="col-xxl-8">
                     <div class="row gy-30">
                         <div class="col-md-6">
                             <div class="social-contact has_fade_anim">
                                 <div class="icon">
                                     <i class="{{ $data['contact_one_icon'] }}"></i>
                                 </div>
                                 <div class="contents">
                                     <h4 class="title">{{ $data['contact_one_title'] }}</h4>
                                     <p class="description "><a class="td-underline-btn"
                                             href="tel:{{ $data['contact_one_value'] }}">{{ $data['contact_one_value'] }}</a>
                                     </p>
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="social-contact has_fade_anim">
                                 <div class="icon">
                                     <i class="{{ $data['contact_two_icon'] }}"></i>
                                 </div>
                                 <div class="contents">
                                     <h4 class="title">{{ $data['contact_one_title'] }}</h4>
                                     <p class="description"><a class="td-underline-btn"
                                             href="tel:{{ $data['contact_one_value'] }}">+{{ $data['contact_one_value'] }}</a>
                                     </p>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <hr class="mt-45 mb-40">
                     <div class="contact-form has_fade_anim">
                         <form action="{{ route('mail-send') }}" method="post">
                             @csrf
                             <div class="row gy-24">
                                 <div class="col-lg-6">
                                     <div class="td-form-group">
                                         <label class="input-label">{{ __('Name') }} <span>*</span></label>
                                         <div class="input-field">
                                             <input type="text" name="name" class="form-control">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-lg-6">
                                     <div class="td-form-group">
                                         <label class="input-label">{{ __('Email Address') }} <span>*</span></label>
                                         <div class="input-field">
                                             <input type="text" class="form-control" name="email">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-lg-12">
                                     <div class="td-form-group">
                                         <label class="input-label">{{ __('Subject') }} <span>*</span></label>
                                         <div class="input-field">
                                             <input type="text" class="form-control" name="subject">
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-lg-12">
                                     <div class="td-form-group">
                                         <label class="input-label">{{ __('Message') }} <span>*</span></label>
                                         <div class="input-field">
                                             <textarea class="form-control" name="msg"></textarea>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-lg-12">
                                     <button class="td-btn gradient-btn radius-8" type="submit">
                                         <span class="btn-icon">
                                             <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                 <g clip-path="url(#clip0_268_16030)">
                                                     <path
                                                         d="M15.2381 0.761809C14.5121 0.0351427 13.4628 -0.190857 12.4808 0.179143L1.55145 4.70848C0.260783 5.19448 -0.137884 6.39914 0.040116 7.37648C0.218783 8.35514 1.01612 9.34181 2.39612 9.34181H6.64878V13.6045C6.64878 14.9838 7.63545 15.7818 8.61345 15.9591C8.75945 15.9858 8.91012 15.9998 9.06278 15.9998C9.93545 15.9998 10.8681 15.5505 11.2754 14.4698L15.8288 3.49848C16.1915 2.53714 15.9654 1.48781 15.2381 0.761809ZM14.5888 3.00781L10.0354 13.9791C9.80478 14.5891 9.27212 14.7245 8.85278 14.6485C8.43145 14.5718 7.98145 14.2571 7.98145 13.6051V8.67581C7.98145 8.30714 7.68345 8.00914 7.31478 8.00914H2.39612C1.74345 8.00914 1.42878 7.55914 1.35212 7.13781C1.27612 6.71714 1.41145 6.18514 2.04212 5.94781L12.9714 1.41848C13.1221 1.36181 13.2748 1.33381 13.4248 1.33381C13.7455 1.33381 14.0528 1.46114 14.2961 1.70448C14.6528 2.06114 14.7588 2.55648 14.5888 3.00781Z"
                                                         fill="white" />
                                                 </g>
                                                 <defs>
                                                     <clipPath id="clip0_268_16030">
                                                         <rect width="16" height="16" fill="white" />
                                                     </clipPath>
                                                 </defs>
                                             </svg>
                                         </span>
                                         <span class="btn-text">{{ __('Submit Now') }}</span>
                                     </button>
                                 </div>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>
         <div class="contact-form-glows">
             <div class="glow-one">
                 <img src="{{ asset('front/digi_vault/images/contact/glow-02.png') }}" alt="Contact Glow">
             </div>
         </div>
     </div>
     <!-- Contact form section end -->
 @endsection
