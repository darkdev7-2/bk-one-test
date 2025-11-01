<section class="video-section-start section-space">
    <div class="container">
        <div class="video-wrapper p-relative">
            <div class="video-content">
                <h3 class="title">{{ $data['big_title'] }}</h3>
            </div>
            <div class="video-thumb-inner">
                @if (content_exists($data['thumbnail_img']))
                    <div class="video-thumb" data-background="{{ asset($data['thumbnail_img']) }}"
                        style="background-image: url(&quot;{{ asset($data['thumbnail_img']) }}&quot;);">
                    </div>
                @endif
                <a class="play-btn popup-video animate-play" href="{{ $data['video_link'] }}">
                    <span><i class="fa-solid fa-play"></i></span>
                </a>
            </div>
        </div>
    </div>
</section>
@push('js')
    <script>
        (function($) {
            'use strict';
            // MagnificPopup image 
            $(".popup-image").magnificPopup({
                type: "image",
                gallery: {
                    enabled: true,
                },
            });

            // MagnificPopup video 
            $(".popup-video").magnificPopup({
                type: "iframe",
            });
        })(jQuery);
    </script>
@endpush
