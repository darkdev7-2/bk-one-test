@php
    $footerContent = json_decode(\App\Models\LandingPage::where('locale',app()->getLocale())->where('code','footer')->first()?->data,true);
@endphp

@if(!Route::is('home'))
@include('frontend::home.include.__cta')
@endif

<footer>
    <div class="footer-section bg-forsythia-bud footer-style-two p-relative z-11">
       <div class="container">
          <div class="footer-main">
             <div class="footer-widget">
                <div class="footer-wg-title">
                   <h5>{{ __('Contact Us') }}</h5>
                </div>
                <div class="footer-contact-info">
                   <ul>
                      <li><a href="mailto:{{ $footerContent['contact_email_address'] }}"><span><i class="fa-solid fa-envelope"></i></span>{{ $footerContent['contact_email_address'] }}</a></li>
                      <li><a href="{{ $footerContent['contact_telegram_link'] }}" target="_blank"><span><i class="fa-solid fa-phone"></i></span>{{ $footerContent['contact_telegram_link'] }}</a></li>
                   </ul>
                </div>
                <div class="footer-social">
                    @foreach(\App\Models\Social::all() as $social)
                        <a href="{{ url($social->url) }}"><i class="{{ $social->class_name }}"></i></a>
                    @endforeach
                </div>
             </div>
            @foreach($navigations as $navigation)
             <div class="footer-widget">
                <div class="footer-wg-title">
                   <h5>{{ $footerContent['widget_title_'.$loop->iteration] ?? '' }}</h5>
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

            <div class="footer-widget">
                <div class="footer-wg-title">
                   <h5>{{ __('Subscribe Now') }}</h5>
                </div>
                <div class="footer-newsletter">
                   <p class="description">
                    {{ $footerContent['widget_left_description'] }}
                   </p>
                   <div class="footer-form">
                      <form action="{{ route('subscriber') }}" method="post">
                        @csrf
                        <input type="text" placeholder="Enter your email"  name="email" >
                        <button type="submit" class="input-btn"><i class="fa-light fa-arrow-right-long"></i></button>
                    </form>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
    <div class="footer-bottom">
       <div class="container">
          <div class="footer-copyright">
             <div class="row">
                <div class="col-xxl-4 col-xl-5 col-lg-5">
                   <div class="content">
                      <p class="description">{{ $footerContent['copyright_text'] }}</p>
                   </div>
                </div>
                <div class="col-xxl-4 col-xl-2 col-lg-2">
                    @php
                        $height = setting('site_logo_height','global') == 'auto' ? 'auto' : setting('site_logo_height','global').'px';
                        $width = setting('site_logo_width','global') == 'auto' ? 'auto' : setting('site_logo_width','global').'px';
                    @endphp

                   <div class="footer-logo">
                      <img src="{{ asset(setting('site_logo','global')) }}" alt="logo" style="height:{{ $height }};width:{{ $width }}">
                   </div>
                </div>
                <div class="col-xxl-4 col-xl-5 col-lg-5">
                   <div class="footer-bottom-links">
                      <ul>
                         <li><a href="/privacy-policy">{{ __('Privacy Policy') }}</a></li>
                         <li><a href="/terms-and-conditions">{{ __('Terms & Conditions') }}</a></li>
                      </ul>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
 </footer>
