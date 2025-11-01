@php
    $testimonials = App\Models\Testimonial::get()->chunk(2);
@endphp
<!-- Testimonial section start -->
<section class="td-testimonial-section include-bg section_space"
    data-background="{{ asset('front/digi_Vault/images/bg/testimonial-bg.png') }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-6 col-xl-6">
                <div class="section-title-wrapper text-center section_title_space">
                    <span class="section-subtitle has_fade_anim">{{ $data['title_small'] }}</span>
                    <h2 class="section-title has_fade_anim">{{ $data['title_big'] }}</h2>
                </div>
            </div>
        </div>
        <div class="testimonial-grid">
            @foreach ($testimonials as $testimonial)
                <div class="single-column has_fade_anim">
                    @foreach ($testimonial as $content)
                        <div class="testimonial-item">
                            <div class="admin-item">
                                <div class="admin-thumbnail">
                                    <img src="{{ asset($content->picture) }}" alt="{{ $content->name }}">
                                </div>
                                <div class="admin-info">
                                    <h3 class="admin-name">{{ $content->name }}</h3>
                                    <span class="admin-designation">{{ $content->designation }}</span>
                                </div>
                            </div>
                            <div class="contents">
                                <p>{{ $content->message }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- Testimonial section end -->
