@php
    $landingContent =\App\Models\LandingContent::where('type','card')->where('locale',app()->getLocale())->get();
@endphp
<!-- Virtual card section start -->
<section class="virtual-card-section fix include-bg position-relative z-index-11 section-space">
    <div class="virtual-card-pattern">
        <img src="{{ asset('front/theme-2/images/bg/virtual-card-bg.png') }}" alt="virtual card">
    </div>
    <div class="virtual-card-shapes">
        <div class="glow-one">
            <img src="{{ asset('front/theme-2/images/virtual-card/glow-one.png') }}" alt="glow-one">
        </div>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-6 col-xl-6 col-lg-8">
                <div class="virtual-card-box text-center">
                    <div class="virtual-card-content mb-30">
                        <div class="section-title-wrapper">
                            <h2 class="section-title mb-15">{{ $data['title'] }}</h2>
                            <P class="description">{{ $data['sub_title'] }}</P>
                        </div>
                    </div>
                    <div class="virtual-card-thumb">
                        <img src="{{ asset($data['card_image']) }}" alt="virtual-card-thumb">
                    </div>
                    <div class="bottom-contents">
                        <div class="btn-inner">
                            <a class="site-btn gdt-btn" href="{{ url($data['button_url']) }}" target="{{ $data['button_target'] }}">
                           <span>
                              <i class="{{ $data['button_icon'] }}"></i>
                           </span>{{ $data['button_label'] }}
                            </a>
                        </div>
                        <div class="payment-method">
                            @foreach($landingContent as $item)
                            <img src="{{ asset($item->icon) }}" alt="visa">
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Virtual card section end -->