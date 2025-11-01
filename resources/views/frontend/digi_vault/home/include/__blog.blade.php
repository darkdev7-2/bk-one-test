<!-- Blog section start -->
<div class="td-blog-section position-relative z-index-11 section_space fix">
    <div class="container">
        <div class="row">
            <div class="col-xxl-5 col-xl-6 col-lg-8">
                <div class="section-title-wrapper section_title_space">
                    <span class="section-subtitle has_fade_anim">{{ $data['blog_title_small'] }}</span>
                    <h2 class="section-title has_fade_anim mb-15">{{ $data['blog_title_big'] }}</h2>
                </div>
            </div>
        </div>
        <div class="row gy-20">
            @foreach (\App\Models\Blog::where('locale', app()->getLocale())->latest()->take(4)->get() as $blog)
                <div class="col-xxl-6 col-xl-6 col-lg-6">
                    <div class="has_fade_anim" data-fade-from="bottom" data-delay="0.30">
                        <article class="single-blog-item">
                            <div class="blog-thumb">
                                <a href="{{ route('blog-details', $blog->id) }}">
                                    <img src="{{ asset($blog->cover) }}" alt="blog img not found">
                                </a>
                            </div>
                            <div class="blog-contents">
                                <span class="date"><svg width="14" height="14" viewBox="0 0 14 14"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g clip-path="url(#clip0_268xad_9548)">
                                            <path
                                                d="M11.0833 1.16667H10.5V0.583333C10.5 0.428624 10.4385 0.280251 10.3291 0.170854C10.2197 0.0614582 10.0714 0 9.91667 0C9.76196 0 9.61358 0.0614582 9.50419 0.170854C9.39479 0.280251 9.33333 0.428624 9.33333 0.583333V1.16667H4.66667V0.583333C4.66667 0.428624 4.60521 0.280251 4.49581 0.170854C4.38642 0.0614582 4.23804 0 4.08333 0C3.92862 0 3.78025 0.0614582 3.67085 0.170854C3.56146 0.280251 3.5 0.428624 3.5 0.583333V1.16667H2.91667C2.1434 1.16759 1.40208 1.47518 0.855295 2.02196C0.308514 2.56874 0.00092625 3.31007 0 4.08333L0 11.0833C0.00092625 11.8566 0.308514 12.5979 0.855295 13.1447C1.40208 13.6915 2.1434 13.9991 2.91667 14H11.0833C11.8566 13.9991 12.5979 13.6915 13.1447 13.1447C13.6915 12.5979 13.9991 11.8566 14 11.0833V4.08333C13.9991 3.31007 13.6915 2.56874 13.1447 2.02196C12.5979 1.47518 11.8566 1.16759 11.0833 1.16667ZM1.16667 4.08333C1.16667 3.6192 1.35104 3.17409 1.67923 2.8459C2.00742 2.51771 2.45254 2.33333 2.91667 2.33333H11.0833C11.5475 2.33333 11.9926 2.51771 12.3208 2.8459C12.649 3.17409 12.8333 3.6192 12.8333 4.08333V4.66667H1.16667V4.08333ZM11.0833 12.8333H2.91667C2.45254 12.8333 2.00742 12.649 1.67923 12.3208C1.35104 11.9926 1.16667 11.5475 1.16667 11.0833V5.83333H12.8333V11.0833C12.8333 11.5475 12.649 11.9926 12.3208 12.3208C11.9926 12.649 11.5475 12.8333 11.0833 12.8333Z"
                                                fill="#222222" />
                                            <path
                                                d="M7 9.625C7.48325 9.625 7.875 9.23325 7.875 8.75C7.875 8.26675 7.48325 7.875 7 7.875C6.51675 7.875 6.125 8.26675 6.125 8.75C6.125 9.23325 6.51675 9.625 7 9.625Z"
                                                fill="#222222" />
                                            <path
                                                d="M4.08331 9.625C4.56656 9.625 4.95831 9.23325 4.95831 8.75C4.95831 8.26675 4.56656 7.875 4.08331 7.875C3.60006 7.875 3.20831 8.26675 3.20831 8.75C3.20831 9.23325 3.60006 9.625 4.08331 9.625Z"
                                                fill="#222222" />
                                            <path
                                                d="M9.91669 9.625C10.3999 9.625 10.7917 9.23325 10.7917 8.75C10.7917 8.26675 10.3999 7.875 9.91669 7.875C9.43344 7.875 9.04169 8.26675 9.04169 8.75C9.04169 9.23325 9.43344 9.625 9.91669 9.625Z"
                                                fill="#222222" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_268_95xzx48">
                                                <rect width="14" height="14" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                    {{ $blog->created_at }}</span>
                                <h5 class="title">
                                    <a href="{{ route('blog-details', $blog->id) }}">{{ $blog->title }}</a>
                                </h5>
                                <div class="blog-link">
                                    <a class="btn-text" href="{{ route('blog-details', $blog->id) }}">
                                        {{ __('Read More') }}
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<!-- Blog section end -->
