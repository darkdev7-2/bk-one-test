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
    <!-- About section start -->
    <section class="td-about-section azure-bg-10 section_space">
        <div class="container">
            <div class="row gy-30 align-items-center">
                <div class="col-xxl-5 col-xl-5">
                    <div class="about-thumb-box">
                        <div class="about-thumb">
                            <img src="{{ asset($data['right_img']) }}" alt="About Thumb">
                        </div>
                    </div>
                </div>
                <div class="col-xxl-7 col-xl-7">
                    <div class="about-thumb-contents">
                        <div class="section-title-wrapper section_title_space">
                            <span class="section-subtitle has_fade_anim">{{ $data['title_small'] }}</span>
                            <h2 class="section-title has_fade_anim mb-16">{{ $data['title_big'] }}</h2>
                            <div class="text">
                                <div class="description has_fade_anim">
                                    <p>{!! $data['content'] !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About section end -->
@endsection
