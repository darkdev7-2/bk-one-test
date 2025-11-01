@php
    $landingContent =\App\Models\LandingContent::where('type','bankingsolution')->where('locale',app()->getLocale())->get();
@endphp

<section class="professional-features-section section-space">
    <div class="container">
       <div class="row justify-content-center">
          <div class="col-xxl-7 col-xl-7 col-lg-7">
             <div class="section-title-wrapper text-center section-title-space">
                <h2 class="section-title mb-15"> {{ $data['title_big'] }}</h2>
                <p class="description">
                    {{ $data['title_small'] }}
                </p>
             </div>
          </div>
       </div>
       <div class="row gy-30">
            @foreach($landingContent as $content)
                <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                    <div class="professional-features-item">
                        <div class="icon">
                            @if(content_exists($content->icon))
                                <span>
                                    <img src="{{ asset($content->icon) }}" alt="features thumb not found">
                                </span>
                            @else
                                <span><i data-lucide="{{ $content->icon }}"></i></span>
                                <i class="{{ $content->icon }}"></i>
                            @endif
                        </div>
                        <div class="content">
                            <h3 class="title"><a href="#">{{ $content->title }}</a></h3>
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

{{--
<section class="our-solutions-section section-space">
    <div class="container">
        <div class="row justify-content-center">
        <div class="col-xxl-6 col-xl-6 col-lg-8">
            <div class="section-title-wrapper text-center section-title-space">
                <h2 class="section-title mb-15">
                    {{ $data['title_big'] }}
                </h2>
                <P class="description">
                    {{ $data['description'] }}
                </P>
            </div>
        </div>
        </div>
        <div class="row gy-50">
            @foreach($landingContent as $content)
                <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6">
                    <div class="our-solutions-item">
                        @if(content_exists($content->icon))
                            <div class="our-solutions-icon">
                                <img src="{{ asset($content->icon) }}" alt="features thumb not found">
                            </div>
                        @else
                            <div class="our-solutions-icon">
                                <span><i data-lucide="{{ $content->icon }}"></i></span>
                                <i class="{{ $content->icon }}"></i>
                            </div>
                        @endif

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
</section> --}}
