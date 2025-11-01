@php
$landingContent =\App\Models\LandingContent::where('type','howitworks')->where('locale',app()->getLocale())->get();
@endphp

<section class="how-it-work-area position-relative section-space">
    <div class="work-timeline-grid">
        @foreach ($landingContent as $content)
            <div class="work-timeline-item">
                <div class="timeline-thumb">
                    <img src="{{ asset($content->icon) }}" alt="img not found">
                </div>
                <div class="content">
                    <h3 class="title">{{ $content->title }}</h3>
                    <p class="description">{{ $content->description }}</p>
                </div>
                <div class="timeline-count">
                    <span class="number">{{ $loop->iteration }}</span>
                </div>
            </div>
        @endforeach
    </div>
</section>
