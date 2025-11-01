@php
    $landingContent = \App\Models\LandingContent::where('type', 'faq')
        ->where('locale', app()->getLocale())
        ->get();
@endphp

<!-- FAQ section start -->
<section class="td-faq-section section_space include-bg p-relative z-index-11"
    data-background="{{ asset('front/digi_vault/images/bg/faq-bg.png') }}">
    <div class="container">
        <div class="row">
            <div class="col-xxl-6 col-xl-6 col-lg-6">
                <div class="faq-contents">
                    <div class="section-title-wrapper section_title_space">
                        <span class="section-subtitle has_fade_anim">{{ $data['title_small'] }}</span>
                        <h2 class="section-title has_fade_anim">{{ $data['title_big'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-xxl-12">
                <div class="faq-style-one">
                    <div class="accordion" id="accordionExample">
                        @foreach ($landingContent as $content)
                            <div class="accordion-item accordion-active has_fade_anim">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        aria-expanded="true" aria-controls="collapseOne">
                                        <span>
                                            <img src="{{ asset('front/digi_vault/images/faq/icon.png') }}"
                                                alt="Facebook Icons">
                                        </span>
                                        {{ $content->title }}
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <p class="description">{!! nl2br(e($content->description)) !!}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- FAQ section end -->
