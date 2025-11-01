@php
    $footerContent = json_decode(\App\Models\LandingPage::where('locale',app()->getLocale())->where('code','footer')->first()?->data,true);
@endphp

<!-- Footer section start -->
<footer>
    <div class="td-footer-section footer-primary p-relative z-index-11">
        <div class="container">
            <div class="footer-main">
                <div class="row justify-content-center">
                    <div class="col-xxl-4 col-xl-4 col-lg-8 col-md-10">
                        <div class="footer-content text-center has_fade_anim">
                            <div class="footer-logo">
                                <a href="{{ route('home') }}">
                                    <img src="{{ asset(setting('site_logo','global')) }}" alt="Footer logo">
                                </a>
                            </div>

                            <p class="description">{{ $footerContent['widget_left_description'] }}</p>
                            <div class="footer-newsletter">
                                <form action="{{ route('subscriber') }}" method="POST">
                                    @csrf
                                    <input type="email" name="email" id="footerName" placeholder="Enter your email address">
                                    <button type="submit" class="td-btn gradient-btn">
                                       <span class="btn-icon">
                                          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                             <g clip-path="url(#clip0_268_14199)">
                                             <path d="M15.2386 0.761809C14.5126 0.0351427 13.4633 -0.190857 12.4813 0.179143L1.55194 4.70848C0.261271 5.19448 -0.137396 6.39914 0.0406043 7.37648C0.219271 8.35514 1.0166 9.34181 2.3966 9.34181H6.64927V13.6045C6.64927 14.9838 7.63594 15.7818 8.61394 15.9591C8.75994 15.9858 8.9106 15.9998 9.06327 15.9998C9.93594 15.9998 10.8686 15.5505 11.2759 14.4698L15.8293 3.49848C16.1919 2.53714 15.9659 1.48781 15.2386 0.761809ZM14.5893 3.00781L10.0359 13.9791C9.80527 14.5891 9.2726 14.7245 8.85327 14.6485C8.43194 14.5718 7.98194 14.2571 7.98194 13.6051V8.67581C7.98194 8.30714 7.68394 8.00914 7.31527 8.00914H2.3966C1.74394 8.00914 1.42927 7.55914 1.3526 7.13781C1.2766 6.71714 1.41194 6.18514 2.0426 5.94781L12.9719 1.41848C13.1226 1.36181 13.2753 1.33381 13.4253 1.33381C13.7459 1.33381 14.0533 1.46114 14.2966 1.70448C14.6533 2.06114 14.7593 2.55648 14.5893 3.00781Z" fill="white"/>
                                             </g>
                                             <defs>
                                             <clipPath id="clip0_268_14199">
                                             <rect width="16" height="16" fill="white"/>
                                             </clipPath>
                                             </defs>
                                          </svg>
                                       </span>
                                        <span class="btn-text">{{ __('Subscribe') }}</span>
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-main-wrapper has_fade_anim">
                    <div class="row gy-30">
                        @foreach($navigations as $navigation)
                        <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="footer-wg-head">
                                <h5 class="title">{{ $footerContent['widget_title_'.$loop->iteration] ?? '' }}</h5>
                            </div>
                            <div class="footer-links">
                                <ul>
                                    @foreach($navigation as $menu)
                                        @if($menu->page_id == null)
                                        <li><a href="{{ $menu->url }}">{{ $menu->tname }}</a></li>
                                        @else
                                        <li><a href="{{ url($menu->url) }}">{{ $menu->tname }}</a></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endforeach
                        <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-6">
                            <div class="footer-widget">
                                <div class="footer-wg-head">
                                    <h5 class="title">{{ $footerContent['widget_title_3'] ?? '' }}</h5>
                                </div>
                                <div class="footer-info">
                                    <div class="info-item">
                                        <h6 class="title">{{ $footerContent['contact_email_title'] }}</h6>
                                        <p class="link"><a href="{{ $footerContent['contact_email_address'] }}">{{ $footerContent['contact_email_address'] }}</a></p>
                                    </div>
                                    <div class="info-item">
                                        <h6 class="title">{{ $footerContent['contact_telegram_title'] }}</h6>
                                        <p class="link"><a target="_blank" href="{{ $footerContent['contact_telegram_link'] }}">{{ $footerContent['contact_telegram_link'] }}</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-xl-3 col-lg-3">
                            <div class="footer-widget">
                                <div class="footer-subscribe">
                                    <div class="footer-wg-head">
                                        <h5 class="title">{{ __('Social Link') }}</h5>
                                    </div>
                                </div>
                                <div class="footer-socials">
                                    @foreach(\App\Models\Social::all() as $social)
                                    <a href="{{ url($social->url) }}">
                                        <i class="{{ $social->class_name }}"></i>
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-copyright">
                    <div class="copyright-text text-center">
                        <p class="description">{{ $footerContent['copyright_text'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-glows d-none d-md-block">
            <div class="glow-one d-none d-sm-block">
                <img src="{{ asset('front/digi_vault/images/bg/foorer-glow-01.png') }}" alt="Footer Glow">
            </div>
        </div>
    </div>
</footer>
<!-- Footer section end -->