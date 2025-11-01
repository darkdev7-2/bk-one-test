@php
    $testimonials = App\Models\Testimonial::all();
@endphp
<section class="feedback-section bg-sugar-milk section-space">
    <div class="container">
       <div class="row justify-content-center">
          <div class="col-xxl-7 col-xl-7 col-lg-7 col-md-10">
             <div class="section-title-wrapper text-center section-title-space">
                <h2 class="section-title mb-15">{{ $data['title_big'] }}</h2>
             </div>
          </div>
       </div>
       <div class="feedback-slider-wrapper">
          <div class="swiper feedback-active">
             <div class="swiper-wrapper">
                @foreach ($testimonials as $content)
                    <div class="swiper-slide">
                    <div class="feedback-item">
                        <div class="admin-thumb-content">
                            @if(content_exists($content->picture))
                                <div class="thumb">
                                    <img src="{{ asset($content->picture) }}" alt="admin">
                                </div>
                            @endif
                            <div class="icon">
                                <span>
                                <svg width="21" height="18" viewBox="0 0 21 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.5 3.54412C9.68293 -1.30955 0 -1.67978 0 5.60071C0 12.0524 10.5 18 10.5 18C10.5 18 21 11.9735 21 5.60071C21 -1.67978 11.3171 -1.30955 10.5 3.54412Z" fill="white"/>
                                </svg>
                                </span>
                            </div>
                        </div>
                        <div class="feedback-content">
                            <p class="description">
                                {{ $content->message }}
                            </p>
                            <div class="admin-info">
                                <h4 class="title">{{ $content->name }}</h4>
                                <span class="info">{{ $content->designation }}</span>
                            </div>
                        </div>
                    </div>
                    </div>
                @endforeach
             </div>
          </div>
          <!-- If we need pagination -->
          <div class="slider-pagination-wrapper">
             <div class="td-swiper-dot text-center"></div>
          </div>
       </div>
    </div>
  </section>
