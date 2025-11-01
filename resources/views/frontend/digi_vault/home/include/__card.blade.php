@php
    $landingContent = \App\Models\LandingContent::where('type', 'card')
        ->where('locale', app()->getLocale())
        ->get();
@endphp
<!-- Virtual card section start -->
<section class="td-virtual-card-section include-bg p-relative z-index-11 section_space"
    data-background="{{ asset('front/digi_vault/images/bg/virtual-card-bg.png') }}">
    <div class="container">
        <div class="row gy-20">
            <div class="col-xxl-7 col-xl-7 col-lg-6">
                <div class="virtual-card-contents">
                    <div class="section-title-wrapper is-white section_title_space">
                        <h2 class="section-title has_fade_anim mb-16">{{ $data['title'] }}</h2>
                        <P class="description has_fade_anim b4">{{ $data['sub_title'] }}</P>
                    </div>
                    <div class="bottom-contents has_fade_anim">
                        <div class="btn-inner">
                            <a class="td-btn gradient-btn radius-8" href="{{ url($data['button_url']) }}"
                                target="{{ $data['button_target'] }}">
                                <span class="btn-icon">
                                    <i class="{{ $data['button_icon'] }}"></i>
                                </span>
                                <span class="btn-text">{{ $data['button_label'] }}</span>
                            </a>
                        </div>
                        <div class="payment-method">
                            @foreach ($landingContent as $item)
                                <img src="{{ asset($item->icon) }}" alt="Icon">
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-5 col-xl-5 col-lg-6">
                <div class="virtual-card-thumb">
                    <img src="{{ asset($data['card_image']) }}" alt="Virtual Card">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Virtual card section end -->
