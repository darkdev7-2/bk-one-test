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
<section class="contact-info-section section-space">
    <div class="container">
       <div class="row justify-content-center">
          <div class="col-xxl-10 col-xl-6 col-lg-6">
             <div class="section-title-wrapper text-center section-title-space">
                <h2 class="section-title">
                    {{ $data['title_big'] }}
                </h2>
             </div>
          </div>
       </div>
       <div class="row gy-30">
          <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
             <div class="contact-info-item">
                <div class="icon">
                    <span><i class="{{ $data['widget_one_icon'] }} fa-6x"></i></span>
                   {{-- <span><img src="{{ asset('front/theme-2') }}/images/contact/call.svg" alt="call"></span> --}}
                </div>
                <div class="contents">
                   <h4 class="title">{{ $data['widget_one_title'] }}</h4>
                   <p class="description">{{ $data['widget_one_description'] }}</p>
                </div>
             </div>
          </div>
          <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
             <div class="contact-info-item">
                <div class="icon">
                   {{-- <span><img src="{{ asset('front/theme-2') }}/images/contact/mail.svg" alt="call"></span> --}}
                   <span><i class="{{ $data['widget_two_icon'] }} fa-6x"></i></span>
                </div>
                <div class="contents">
                   <h4 class="title">{{ $data['widget_two_title'] }}</h4>
                   <p class="description">{{ $data['widget_two_description'] }}</p>
                </div>
             </div>
          </div>
          <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
             <div class="contact-info-item">
                <div class="icon">
                   {{-- <span><img src="{{ asset('front/theme-2') }}/images/contact/chats.svg" alt="call"></span> --}}
                   <span><i class="{{ $data['widget_three_icon'] }} fa-6x"></i></span>
                </div>
                <div class="contents">
                   <h4 class="title">{{ $data['widget_three_title'] }}</h4>
                   <p class="description">
                    {{ $data['widget_three_description'] }}
                   </p>
                </div>
             </div>
          </div>
       </div>
    </div>
 </section>

 <section class="contact-form-section bg-sugar-milk section-space">
    <div class="container">
       <div class="row justify-content-center">
          <div class="col-xxl-8 col-xl-8 col-lg-6">
             <div class="section-title-wrapper text-center section-title-space">
                <h2 class="section-title mb-15">{{ $data['form_title'] }}</h2>
                <p class="description">
                    {{ $data['form_description'] }}
                </p>
             </div>
          </div>
          <div class="contact-form-wrapper">
            <form id="contact-form" action="{{ route('mail-send') }}" method="POST">
                @csrf
                <div class="row gy-30">
                   <div class="col-xxl-4 col-xl-4">
                      <div class="contact-form-input">
                         <input class="input" type="text" placeholder="Name*" name="name">
                      </div>
                   </div>
                   <div class="col-xxl-4 col-xl-4">
                      <div class="contact-form-input">
                         <input class="input" type="text" placeholder="Email Address*" name="email">
                      </div>
                   </div>
                   <div class="col-xxl-4 col-xl-4">
                      <div class="contact-form-input">
                         <input class="input" type="text" placeholder="Subject*" name="subject" >
                      </div>
                   </div>
                   <div class="col-xxl-12">
                      <div class="contact-form-input">
                         <textarea class="textarea" name="msg" placeholder="Message*"></textarea>
                      </div>
                   </div>
                   <div class="col-xxl-12">
                      <button type="submit" class="site-btn gdt-btn">{{ __('Submit Now') }}</button>
                   </div>
                </div>
             </form>
          </div>
       </div>
    </div>
  </section>
@endsection
