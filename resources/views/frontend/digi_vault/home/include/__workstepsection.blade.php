@php
    $landingContent = \App\Models\LandingContent::where('type', 'workstepsection')
        ->where('locale', app()->getLocale())
        ->get();
@endphp

<!-- Work step section start -->
<section class="td-work-steps-section">
    <div class="container">
        <div class="work-items-grid">
            @foreach ($landingContent as $content)
                <div class="work-steps-item has_fade_anim" data-delay="0.15">
                    <div class="icon">
                        <span>
                            <img src="{{ asset($content->icon) }}" alt="steps Icon">
                        </span>
                    </div>
                    <div class="contents">
                        <h4 class="title">{{ $content->title }}</h4>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- Work step section end -->
