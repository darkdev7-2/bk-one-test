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
<section class="about-section section-space">
    <div class="container">
       <div class="row justify-content-center">
          <div class="col-xxl-7 col-xl-6 col-lg-6">
             <div class="about-main-wrapper text-center">
                <div class="about-content-two mb-40">
                   <div class="section-title-wrapper">
                      <h2 class="section-title mb-20">{{ $data['title_big'] }} <span class="gd-text"> {{ setting('site_title', 'global') }}</span></h2>
                      <p class="description">
                        {!! $data['content'] !!}
                      </p>
                   </div>
                </div>
                <div class="about-thumb-wrap-two">
                   <div class="about-thumb">
                      <img src="{{ asset($data['right_img']) }}" alt="about-thumb">
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
</section>
@endsection
