@php
    $landingContent =\App\Models\LandingContent::where('type','powerfulsection')->where('locale',app()->getLocale())->get();
@endphp

<section class="our-solutions-section section-space-bottom">
    <div class="container">
       <div class="row justify-content-center">
          <div class="col-xxl-6 col-xl-6 col-lg-8">
             <div class="section-title-wrapper text-center section-title-space">
                <h2 class="section-title mb-15">{{ $data['title_big'] }}</h2>
                <p class="description">
                    {{ $data['title_small'] }}
                </p>
             </div>
          </div>
       </div>
       <div class="row gy-50">
          @foreach ($landingContent as $content)
            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                <div class="our-solutions-item">
                    <div class="our-solutions-icon">
                        <img src="{{ asset($content->icon) }}" alt="Not found" width="80px" height="80px">
                    </div>
                    <div class="our-solutions-content">
                        <h3 class="title">{{ $content->title }}</h3>
                        <p class="description">
                            {{ $content->description }}
                        </p>
                    </div>
                </div>
            </div>
          @endforeach
       </div>
    </div>
</section>
