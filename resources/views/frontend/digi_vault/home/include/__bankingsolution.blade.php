@php
    $landingContent = \App\Models\LandingContent::where('type', 'bankingsolution')
        ->where('locale', app()->getLocale())
        ->get();
@endphp

<!-- Professional features section start -->
<section class="professional-section p-relative z-index-11 section_space include-bg"
    data-background="{{ asset('front/digi_vault/images/bg/features-bg.png') }}">
    <div class="container">
        <div class="row">
            <div class="col-xxl-8 col-xl-8 col-lg-8">
                <div class="section-title-wrapper is-white mb-40">
                    <span class="section-subtitle has_fade_anim">{{ $data['title_small'] }}</span>
                    <h2 class="section-title has_fade_anim">{{ $data['title_big'] }}</h2>
                </div>
            </div>
        </div>
        <div class="row gy-30">
            @foreach ($landingContent as $content)
                <div class="col-xl-3 col-xl-3 col-lg-3 col-md-6">
                    <div class="has_fade_anim">
                        <div class="professional-features-item">
                            <div class="professional-features-icon">
                                <span>
                                    <img src="{{ asset($content->icon) }}" alt="{{ $content->title }}">
                                </span>
                            </div>
                            <div class="professional-features-contents">
                                <h4 class="title">{{ $content->title }}</h4>
                                <p class="description">{{ $content->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="professional-snake-shape">
        <img src="{{ asset('front/digi_vault/images/professional-features/snake.png') }}" alt="Snake Shape">
    </div>
    <div class="professional-features-shapes">
        <div class="shape-one">
            <img src="{{ asset('front/digi_vault/images/professional-features/cube-01.png') }}" alt="Shape">
        </div>
        <div class="shape-two">
            <img src="{{ asset('front/digi_vault/images/professional-features/cube-02.png') }}" alt="Shape">
        </div>
        <div class="glow-three">
            <img src="{{ asset('front/digi_vault/images/professional-features/union.png') }}" alt="Shape">
        </div>
    </div>
</section>
<!-- Professional features section end -->
