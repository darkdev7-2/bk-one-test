@php
    $landingContent =\App\Models\LandingContent::where('type','workstepsection')->where('locale',app()->getLocale())->get();
@endphp

<section class="work-process-section include-bg section-space" data-background="{{ asset('front/theme-2/images/bg/work-process-bg.png') }}" style="background-image: url(&quot;{{ asset('front/theme-2/images/bg/work-process-bg.png') }}&quot;);">
    <div class="container">
       <div class="row justify-content-center">
          <div class="col-xxl-6 col-xl-7 col-lg-6">
             <div class="section-title-wrapper text-center section-title-space">
                <h2 class="section-title text-white mb-15">{{ __('Our Work Process') }}</h2>
                <p class="description text-white">{{ __('Depositing money securely involves ensuring that the process of transferring funds into a bank account is protected from unauthorized access or potential risks') }}</p>
             </div>
          </div>
          <div class="work-process-main">
             <div class="process-intro-logo">
                <span><img src="{{ asset('front/theme-2/images/logo/intro-logo.svg') }}" alt="intro-logo"></span>
             </div>
             <div class="row gy-30">
                @foreach ($landingContent as $content)
                    <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6">
                        <div class="work-process-item">
                            <div class="icon">
                                <span>
                                    <img src="{{ asset($content->icon) }}" alt="step icon not found">
                                </span>
                            </div>
                            <div class="content">
                                <h3 class="title">
                                    {{ $content->title }}
                                </h3>
                            </div>
                        </div>
                    </div>
                @endforeach
             </div>
          </div>
       </div>
    </div>
</section>
