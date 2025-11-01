@extends('frontend::pages.index')

@section('title')
    {{ $blog->title }}
@endsection

@section('page-content')
    <!-- Postbox details section start -->
    <section class="coinefy-postbox-details-area position-relative inner-pages_space-top section_space-bottom">
        <div class="container">
            <div class="row gy-40">
                <div class="col-xxl-4 col-xl-5 col-lg-5">
                    <div class="sidebar-main-wrapper">
                        <div class="sidebar-wrapper">
                            <div class="sidebar-widget">
                                <h3 class="sidebar-widget-title">{{ __('Recent Posts') }}</h3>
                                <div class="sidebar-widget-content">
                                    <div class="sidebar-post">
                                        @php
                                            $blogs = \App\Models\Blog::where('locale', app()->getLocale())
                                                ->take(3)
                                                ->get();
                                        @endphp
                                        @foreach ($blogs as $blog)
                                            <div class="rc-post-item">
                                                <div class="rc-post-thumb">
                                                    <a href="{{ route('blog-details', $blog->id) }}">
                                                        <img src="{{ asset($blog->cover) }}" alt="Recent Post">
                                                    </a>
                                                </div>
                                                <div class="rc-post-content">
                                                    <div class="rc-post-meta">
                                                        <div class="meta-info">
                                                            <span>
                                                                <svg width="11" height="11" viewBox="0 0 11 11"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <g clip-path="url(#clip0_268_1574xzx3)">
                                                                        <path
                                                                            d="M8.69108 1.77779H8.27441V1.36112C8.27441 1.25062 8.23052 1.14464 8.15238 1.0665C8.07424 0.988357 7.96825 0.944458 7.85775 0.944458C7.74724 0.944458 7.64126 0.988357 7.56312 1.0665C7.48498 1.14464 7.44108 1.25062 7.44108 1.36112V1.77779H4.10775V1.36112C4.10775 1.25062 4.06385 1.14464 3.98571 1.0665C3.90757 0.988357 3.80159 0.944458 3.69108 0.944458C3.58057 0.944458 3.47459 0.988357 3.39645 1.0665C3.31831 1.14464 3.27441 1.25062 3.27441 1.36112V1.77779H2.85775C2.30542 1.77845 1.7759 1.99816 1.38534 2.38872C0.994782 2.77927 0.775076 3.30879 0.774414 3.86112L0.774414 8.86112C0.775076 9.41346 0.994782 9.94298 1.38534 10.3335C1.7759 10.7241 2.30542 10.9438 2.85775 10.9445H8.69108C9.24341 10.9438 9.77293 10.7241 10.1635 10.3335C10.554 9.94298 10.7738 9.41346 10.7744 8.86112V3.86112C10.7738 3.30879 10.554 2.77927 10.1635 2.38872C9.77293 1.99816 9.24341 1.77845 8.69108 1.77779ZM1.60775 3.86112C1.60775 3.5296 1.73944 3.21166 1.97386 2.97724C2.20828 2.74282 2.52623 2.61112 2.85775 2.61112H8.69108C9.0226 2.61112 9.34054 2.74282 9.57496 2.97724C9.80938 3.21166 9.94108 3.5296 9.94108 3.86112V4.27779H1.60775V3.86112ZM8.69108 10.1111H2.85775C2.52623 10.1111 2.20828 9.97943 1.97386 9.74501C1.73944 9.51059 1.60775 9.19265 1.60775 8.86112V5.11112H9.94108V8.86112C9.94108 9.19265 9.80938 9.51059 9.57496 9.74501C9.34054 9.97943 9.0226 10.1111 8.69108 10.1111Z"
                                                                            fill="#222222" />
                                                                        <path
                                                                            d="M5.77441 7.81946C6.11959 7.81946 6.39941 7.53964 6.39941 7.19446C6.39941 6.84928 6.11959 6.56946 5.77441 6.56946C5.42924 6.56946 5.14941 6.84928 5.14941 7.19446C5.14941 7.53964 5.42924 7.81946 5.77441 7.81946Z"
                                                                            fill="#222222" />
                                                                        <path
                                                                            d="M3.69043 7.81946C4.03561 7.81946 4.31543 7.53964 4.31543 7.19446C4.31543 6.84928 4.03561 6.56946 3.69043 6.56946C3.34525 6.56946 3.06543 6.84928 3.06543 7.19446C3.06543 7.53964 3.34525 7.81946 3.69043 7.81946Z"
                                                                            fill="#222222" />
                                                                        <path
                                                                            d="M7.8584 7.81946C8.20358 7.81946 8.4834 7.53964 8.4834 7.19446C8.4834 6.84928 8.20358 6.56946 7.8584 6.56946C7.51322 6.56946 7.2334 6.84928 7.2334 7.19446C7.2334 7.53964 7.51322 7.81946 7.8584 7.81946Z"
                                                                            fill="#222222" />
                                                                    </g>
                                                                    <defs>
                                                                        <clipPath id="clip0_268_1dsd5743">
                                                                            <rect width="10" height="10"
                                                                                fill="white"
                                                                                transform="translate(0.774414 0.944458)" />
                                                                        </clipPath>
                                                                    </defs>
                                                                </svg>
                                                            </span>
                                                            <span>{{ date('d M Y', strtotime($blog->created_at)) }}</span>
                                                        </div>
                                                    </div>
                                                    <h5 class="rc-post-title"><a
                                                            href="{{ route('blog-details', $blog->id) }}">{{ $blog->title }}</a>
                                                    </h5>
                                                    <a class="td-btn-text"
                                                        href="{{ route('blog-details', $blog->id) }}">{{ __('Read More') }}</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-8 col-xl-7 col-lg-7">
                    <div class="postbox-contents">
                        <img src="{{ asset($blog->cover) }}" alt="Postbox Large Thumb">
                        <h2>{{ $blog->title }}</h2>
                        {!! $blog->details !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Postbox details section end -->
@endsection
