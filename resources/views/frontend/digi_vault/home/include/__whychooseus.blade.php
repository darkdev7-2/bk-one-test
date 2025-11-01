@php
    $landingContent = \App\Models\LandingContent::where('type', 'whychooseus')
        ->where('locale', app()->getLocale())
        ->get();
@endphp

<!-- Why choose section start -->
<section class="td-why-choose-section include-bg "
    data-background="{{ asset('front/digi_vault/images/bg/why-choose-bg.png') }}">
    <div class="container">
        <div class="why-choose-main p-relative section_space">
            <div class="row">
                <div class="col-xxl-6 col-xl-6 col-lg-6">
                    <div class="section-title-wrapper section_title_space">
                        <span class="section-subtitle has_fade_anim">{{ $data['title_small'] }}</span>
                        <h2 class="section-title has_fade_anim">{{ $data['title_big'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="why-choose-grid">
                <div class="row gy-30">
                    @foreach ($landingContent as $content)
                        <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                            <div class="why-choose-item has_fade_anim">
                                <div class="why-choose-icon">
                                    <img src="{{ asset($content['icon']) }}" alt="Why Choose Icon">
                                </div>
                                <div class="why-choose-contents">
                                    <h4 class="title">{{ $content['title'] }}</h4>
                                    <p class="description">{{ $content['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- line animation start -->
            <div class="why-choose-line-wrap d-none d-lg-block">
                <div class="line-item"></div>
                <div class="line-item"></div>
                <div class="line-item"></div>
                <div class="line-item"></div>
            </div>
        </div>
    </div>
</section>
<!-- Why choose section end -->
