@php
    $landingContent = \App\Models\LandingContent::where('type', 'howitworks')
        ->where('locale', app()->getLocale())
        ->get();
@endphp

<!-- how it works section start -->
<section class="how-it-works-section include-bg section_space"
    data-background="{{ asset('front/digi_vault/images/bg/how-it-works-bg.png') }}">
    <div class="container">
        <div class="row gy-30">
            @foreach ($landingContent as $content)
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <div class="how-it-works-step has_fade_anim">
                        <h3 class="title"><span class="number">{{ $loop->index + 1 }}.</span>{{ $content->title }}</h3>
                        <p class="description">{{ $content->description }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- how it works section end -->
