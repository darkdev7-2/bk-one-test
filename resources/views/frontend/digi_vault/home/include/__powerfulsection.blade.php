@php
    $landingContent = \App\Models\LandingContent::where('type', 'powerfulsection')
        ->where('locale', app()->getLocale())
        ->get();
@endphp

<!-- Our solutions section start -->
<section class="td-our-solutions-section p-relative z-index-11 section_space">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-7 col-xl-7 col-lg-8">
                <div class="section-title-wrapper text-center section_title_space">
                    <span class="section-subtitle has_fade_anim">{{ $data['title_small'] }}</span>
                    <h2 class="section-title has_fade_anim mb-16">{{ $data['title_big'] }}</h2>
                    <div class="has_fade_anim">
                        <p>{{ $data['description'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gy-30">
            @foreach ($landingContent as $content)
                <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                    <div class="our-solutions-item hover_active active has_fade_anim">
                        <div class="inner">
                            <div class="our-solutions-icon">
                                <span>
                                    <img src="{{ asset($content->icon) }}" alt="Fund Transfers Icon">
                                </span>
                            </div>
                            <div class="our-solutions-contents">
                                <h4 class="title">{{ $content->title }}</h4>
                                <p class="description">{{ $content->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="our-solutions-glows">
        <div class="glow-one d-none d-md-block">
            <img src="{{ asset('front/digi_vault/images/our-solutions/glow-01.png') }}" alt="Glow Shape">
        </div>
        <div class="glow-two">
            <img src="{{ asset('front/digi_vault/images/our-solutions/glow-02.png') }}" alt="Glow Shape">
        </div>
    </div>
</section>
<!-- Our solutions section end -->
