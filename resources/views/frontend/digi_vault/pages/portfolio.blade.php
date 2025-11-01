@extends('frontend::pages.index')
@section('title')
    {{ $data['title'] }}
@endsection
@section('meta_keywords')
    {{ $data['meta_keywords'] }}
@endsection
@section('meta_description')
    {{ $data['meta_description'] }}
@endsection
@section('page-content')
    @php
        $portfolios = App\Models\Portfolio::where('status', true)->get();
    @endphp
    <!-- Reward redeem section start -->
    <div class="pages-glow include-bg" data-background="{{ asset('front/digi_vaultimages/bg/redeem-bg.png') }}">
        <section class="td-reward-redeem-section section_space">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-7 col-xl-7 col-lg-8">
                        <div class="section-title-wrapper section_title_space">
                            <h2 class="section-title has_fade_anim">{{ $data['title_one'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row gy-30">
                    @foreach ($portfolios as $portfolio)
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="reward-redeem-item has_fade_anim">
                                <div class="reward-badge">
                                    <img src="{{ asset($portfolio->icon) }}" alt="Reward Badge">
                                </div>
                                <div class="reward-contents">
                                    <h3 class="title">{{ $portfolio->portfolio_name }}</h3>
                                    <h3 class="title">{{ __('Description :') }}{{ $portfolio->description }}</h3>
                                </div>
                                <a class="td-btn btn-primary-outline btn-sm radius-8"
                                    href="{{ route('user.rewards.index') }}">{{ __('Redeem Now') }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
    <!-- Reward redeem section end -->
@endsection
