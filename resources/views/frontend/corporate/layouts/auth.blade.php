<!DOCTYPE html>
 @php
     $isRtl = isRtl(app()->getLocale());
 @endphp
 <html lang="{{ app()->getLocale() }}" @if($isRtl) dir="rtl" @endif>

@include('frontend::include.__head')


<body class="auth-bg" @class([
    'dark-theme' => session()->get('site-color-mode',setting('default_mode')) == 'dark',
    'rtl_mode' => $isRtl,
    'auth-bg'
])>

    <!--Notification-->
    @include('global._notify')

  <!-- Pre loader start -->
  <div id="preloader" class="d-none">
   <div class="sk-three-bounce">
      <div class="sk-child sk-bounce1"></div>
      <div class="sk-child sk-bounce2"></div>
      <div class="sk-child sk-bounce3"></div>
    </div>
  </div>
  <!-- Pre loader end -->

   <!-- Back to top start -->
   @if(setting('back_to_top','permission'))
   <div class="back-to-top-wrap coinefy cursor-pointer">
      <svg class="backtotop-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
         <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
      </svg>
   </div>
    @endif
   <!-- Back to top end -->

   <!-- Header area start -->
   <header>
      <div class="header-area header-style-two auth-header">
         <div class="header-inner">
            <div class="header-left">
               <div class="header-logo">
                @php
                    $height = setting('site_logo_height','global') == 'auto' ? 'auto' : setting('site_logo_height','global').'px';
                    $width = setting('site_logo_width','global') == 'auto' ? 'auto' : setting('site_logo_width','global').'px';
                @endphp

                  <a href="/">
                     <img src="{{asset(setting('site_logo','global'))}}" alt="logo not found" style="height:{{ $height }};width:{{ $width }};">
                  </a>
               </div>
            </div>
            <div class="header-right">
                <div class="header-switcher">
                    <div class="switcher-box">
                       <label class="color-switcher">
                          <input type="checkbox" id="themeSwitcher">
                          <span class="slider round"></span>
                       </label>
                    </div>
                 </div>
               @if(setting('language_switcher'))
                    @php
                        $languages = \App\Models\Language::where('status',true)->get();
                        $current_lang = app()->getLocale();
                    @endphp
                    <div class="header-quick-action">
                        <div class="language-box">
                            <div class="header-lang-item header-lang">
                                <span class="header-lang-toggle" id="header-lang-toggle">
                                    <i class="fa-regular fa-globe"></i>
                                    <span class="lang-text">
                                        {{ $languages->where('locale',$current_lang)->value('name') }}
                                    </span>
                                </span>
                                <ul id="language-list" class="hidden">
                                    @foreach(\App\Models\Language::where('status',true)->get() as $lang)
                                        @if ($current_lang != $lang->locale)
                                            <li>
                                                <a href="{{ route('language-update',['name'=> $lang->locale]) }}" data-lang="{{$lang->name}}" class="change_lang">
                                                    {{$lang->name}} <span class="icon"></span>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
         </div>
      </div>
   </header>
   <!-- Header area end -->

   <!-- Body main wrapper start -->
   <main>
     @yield('content')
   </main>
   <!-- Body main wrapper end -->

   @include('frontend::cookie.gdpr_cookie')
   @include('frontend::include.__script')
   <script>
        $('.change_lang').on('click', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            $.get(url, function(data) {
                location.reload();
            });
        });
    </script>
</body>
</html>
