@php
    $landingContent =\App\Models\LandingContent::where('type','card')->where('locale',app()->getLocale())->get();
@endphp

<!-- Virtual card section start -->
<section class="virtual-card-section include-bg position-relative z-index-11 section-space" data-background="{{ asset('front/images/bg/virtual-card-bg.png') }}">
    <div class="container">
        <div class="row gy-30">
            <div class="col-xxl-6 col-xl-6 col-lg-6">
                <div class="virtual-card-thumb">
                    <img data-aos="fade-left" data-aos-duration="1500" src="{{ asset('front/images/virtual-card/virtual-card-thumb.png') }}" alt="Virtual Card">
                </div>
            </div>
            <div class="col-xxl-6 col-xl-6 col-lg-6">
                <div class="virtual-card-contents">
                    <div class="section-title-wrapper mb-30">
                        <h2 data-aos="fade-up" data-aos-duration="1500" class="section-title text-white">
                            {{ $data['title'] }}
                        </h2>
                    </div>
                    <p data-aos="fade-up" data-aos-duration="2000" class="description">
                        {{ $data['sub_title'] }}
                    </p>
                    <div data-aos="fade-up" data-aos-duration="3000" class="bottom-contents has_fade_anim">
                        <div class="btn-inner">
                            <a class="td-btn gradient-btn radius-8" href="{{ url($data['button_url']) }}" target="{{ $data['button_target'] }}">
                                 <span class="btn-icon">
                                    <i class="{{ $data['button_icon'] }}"></i>
                                 </span>
                                <span class="btn-text">{{ $data['button_label'] }}</span>
                            </a>
                        </div>
                        <div class="payment-method">
                            <img src="{{ asset('front/images/virtual-card/visa-card.png') }}" alt="visa">
                            <img src="{{ asset('front/images/virtual-card/master-card.png') }}" alt="mastercard">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Virtual card section end -->