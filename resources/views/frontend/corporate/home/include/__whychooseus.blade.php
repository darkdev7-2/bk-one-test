@php
    $landingContent =\App\Models\LandingContent::where('type','whychooseus')->where('locale',app()->getLocale())->get();
@endphp

<section class="why-choose-section section-space-top">
    <div class="container large-container">
       <div class="row justify-content-center">
          <div class="col-xxl-8 col-xl-8 col-lg-7 col-md-10 col-sm-10">
             <div class="section-title-wrapper text-center section-title-space">
                <h2 class="section-title mb-15">{{ $data['title_small'] }}</span></h2>
                <p class="description">{{ $data['title_big'] }}</p>
             </div>
          </div>
       </div>
       <div class="why-choose-grid">
          <div class="why-choose-column">
            @foreach($landingContent as $content)
                @if ($loop->odd)
                    <div class="why-choose-item has-left">
                        <div class="why-choose-content">
                            <h4 class="title">{{ $content->title }}</h4>
                            <p class="description">
                                {{ $content->description }}
                            </p>
                        </div>
                        <div class="why-choose-icon">
                            <span><i class="{{ $content->icon }} fa-2x"></i></span>
                        </div>
                    </div>
                @endif
            @endforeach
          </div>
          @if(content_exists($data['right_img']))
            <div class="why-choose-thumb">
                <img src="{{ asset($data['right_img']) }}" alt="why-choose">
            </div>
          @endif
          <div class="why-choose-column">
                @foreach($landingContent as $content)
                    @if ($loop->even)
                        <div class="why-choose-item has-right">
                            <div class="why-choose-content">
                                <h4 class="title">{{ $content->title }}</h4>
                                <p class="description">
                                    {{ $content->description }}
                                </p>
                            </div>
                            <div class="why-choose-icon">
                                <span><i class="{{ $content->icon }} fa-2x"></i></span>
                            </div>
                        </div>
                    @endif
                @endforeach
          </div>
       </div>
    </div>
</section>
