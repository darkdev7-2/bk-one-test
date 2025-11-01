@php
$landingContent =\App\Models\LandingContent::where('type','faq')->where('locale',app()->getLocale())->get();
@endphp

<section class="faq-section section-space">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-6 col-xl-6 col-lg-7">
                <div class="section-title-wrapper text-center section-title-space">
                    <h2 class="section-title mb-15">{{ $data['title_small'] }}</h2>
                    <p class="description">{{ $data['title_big'] }}</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-xxl-10 col-xl-10 col-lg-10">
                <div class="accordion-wrapper site-faq">
                    <div class="accordion" id="accordionExample">
                        @foreach($landingContent as $content)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $loop->iteration != 1 ? 'active' : '' }}"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse_{{ $content->id }}" aria-expanded="true"
                                    aria-controls="collapse_{{ $content->id }}">
                                    <span class="count">{{ $loop->iteration }}</span>
                                    {{ $content->title }}
                                </button>
                            </h2>
                            <div id="collapse_{{ $content->id }}"
                                class="accordion-collapse collapse {{ $loop->iteration == 1 ? 'show':'' }}"
                                data-bs-parent="#accordionExample" style="">
                                <div class="accordion-body">
                                    <p>{!! nl2br(e($content->description)) !!}</p>
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
