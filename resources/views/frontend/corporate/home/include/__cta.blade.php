@php
    $data = json_decode(getLandingData('cta')?->data, true);
@endphp

<section class="cta-section section-space-top bg-forsythia-bud">
    <div class="container">
        <div class="cta-main-wrapper">
            <div class="row gy-30 align-items-center">
                <div class="col-xxl-8 col-xl-7 col-lg-7">
                    <div class="cta-content">
                        <h2 class="cta-title">{{ $data['title'] }}</h2>
                        <p class="description">{{ $data['description'] }}</p>
                        <a class="site-btn gdt-btn" href="{{ $data['button_url'] }}"
                            target="{{ $data['button_target'] }}">{{ $data['button_label'] }}</a>
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-4">
                    <div class="cta-thumb-wrapper">
                        <div class="cta-thumb">
                            <img src="{{ isset($data['right_img']) ? asset($data['right_img']) : asset('front/theme-2/images/cta/cta-thumb.png') }}"
                                alt="cta-thumb">
                        </div>
                        <div class="cta-shape">
                            <img src="{{ asset('front/theme-2/images/cta/cta-bg.png') }}" alt="cta-bg">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
