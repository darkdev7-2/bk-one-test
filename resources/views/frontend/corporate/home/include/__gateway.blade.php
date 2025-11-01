@php
    $gateways_list = \App\Models\Gateway::where('status',true)->pluck('logo','name')->chunk(6);
@endphp

<section class="sponsor-section">
    <div class="container">
        <div class="sponsor-main-wrapper">
            <div class="sponsor-item-grid">
                <div class="swiper sponsor_active">
                    <div class="swiper-wrapper">
                        @foreach ($gateways_list as $gateways)
                            @foreach($gateways as $name => $logo)
                                <div class="swiper-slide">
                                    <div class="sponsor-item">
                                        <div class="sponsor-thumb">
                                            <img src="{{ asset($logo) }}" alt="sponsor">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
