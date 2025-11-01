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
    @php
        $landingContent =\App\Models\LandingContent::where('type','solutions')->where('locale',app()->getLocale())->get();
    @endphp

    <section class="professional-features-section section-space">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-7 col-xl-7 col-lg-7">
                    <div class="section-title-wrapper text-center section-title-space">
                        <h2 class="section-title mb-15">Our<span class="gd-text"> Professional</span> Features</h2>
                        <P class="description">

                        </P>
                    </div>
                </div>
            </div>
            <div class="row gy-30">
                @foreach ($landingContent as $content)
                <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                    <div class="professional-features-item">
                    <div class="icon">
                        <span>
                        <i class="{{ $content->icon }} fa-5x"></i>
                        </span>
                    </div>
                    <div class="content">
                        <h3 class="title"><a href="#">{{ $content->title }}</a></h3>
                        <p class="description">{{ $content->description }}</p>
                    </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
