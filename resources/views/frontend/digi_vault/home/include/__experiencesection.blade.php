@php
    $landingContent = \App\Models\LandingContent::where('type', 'experiencesection')
        ->where('locale', app()->getLocale())
        ->get();
@endphp

<!-- Key features section start -->
<section class="our-key-features-section cetacean-blue-bg p-relative z-index-11 section_space">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-6 col-xl-6 col-lg-8">
                <div class="section-title-wrapper is-white text-center section_title_space">
                    <span class="section-subtitle has_fade_anim">{{ $data['title_small'] }}</span>
                    <h2 class="section-title mb-16 has_fade_anim">{{ $data['title_big'] }}</h2>
                    <div class="text has_fade_anim">
                        <p class="description">{{ $data['description'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="key-features-grid">
            @foreach ($landingContent as $content)
                <div class="key-features-box has_fade_anim">
                    <div class="icon">
                        <img src="{{ asset($content->icon) }}">
                    </div>
                    <p class="description">{{ $content->title }}</p>
                </div>
            @endforeach
        </div>
    </div>
    <div class="key-features-shapes">
        <div class="shape-one d-none d-sm-block">
            <img src="{{ asset('front/digi_vault/images/key-features/cube-01.png') }}" alt="Cube Shape">
        </div>
        <div class="shape-two">
            <img src="{{ asset('front/digi_vault/images/key-features/glow-01.png') }}" alt="Glow">
        </div>
        <div class="shape-three">
            <img src="{{ asset('front/digi_vault/images/key-features/shape-01.png') }}" alt="shape">
        </div>
        <div class="shape-four">
            <img src="{{ asset('front/digi_vault/images/key-features/union.png') }}" alt="shape">
        </div>
        <div class="shape-five">
            <img src="{{ asset('front/digi_vault/images/key-features/union.png') }}" alt="shape">
        </div>
    </div>
</section>
<!-- Key features section end -->
