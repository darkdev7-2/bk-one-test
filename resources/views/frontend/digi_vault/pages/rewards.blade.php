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
        $redeems = App\Models\RewardPointRedeem::with('portfolio')->get();
        $transactions = App\Models\RewardPointEarning::with('portfolio')->get();
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
                    @foreach ($redeems as $redeem)
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="reward-redeem-item has_fade_anim">
                                <div class="reward-badge">
                                    <img src="{{ asset($redeem->portfolio?->icon) }}" alt="Reward Badge">
                                </div>
                                <div class="reward-contents">
                                    <h3 class="title">{{ $redeem->portfolio?->portfolio_name }}</h3>
                                    <p class="point">{{ $redeem->point }} {{ __('Points') }} = {{ $redeem->amount }}
                                        {{ $currency }}</p>
                                </div>
                                <a class="td-btn btn-primary-outline btn-sm radius-8"
                                    href="{{ route('user.rewards.index') }}">{{ __('Redeem Now') }}</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <section class="reward-earnings-section section_space-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-xxl-7 col-xl-7 col-lg-8">
                        <div class="section-title-wrapper section_title_space">
                            <h2 class="section-title has_fade_anim">{{ $data['title_two'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row gy-30">
                    <div class="col-xxl-6">
                        <div class="reward-table table-responsive has_fade_anim">
                            <table class="td-table has-glow">
                                <thead>
                                    <tr>
                                        <th><span><svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M11.3333 12H2" stroke="#3D3D3D" stroke-width="1.33331"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M13.9999 9.33301H2" stroke="#3D3D3D" stroke-width="1.33331"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M11.3333 6.66699H2" stroke="#3D3D3D" stroke-width="1.33331"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M13.9999 4H2" stroke="#3D3D3D" stroke-width="1.33331"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </span> Portfolio List</th>
                                        <th> <span><svg width="17" height="16" viewBox="0 0 17 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M14.0007 13.9999V12.6665C14.0007 11.9593 13.7197 11.281 13.2196 10.7809C12.7195 10.2808 12.0412 9.99988 11.334 9.99988H6.00065C5.29341 9.99988 4.61513 10.2808 4.11503 10.7809C3.61494 11.281 3.33398 11.9593 3.33398 12.6665V13.9999"
                                                        stroke="#3D3D3D" stroke-width="1.33331" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M8.66698 7.33395C10.1399 7.33395 11.334 6.1399 11.334 4.66698C11.334 3.19405 10.1399 2 8.66698 2C7.19405 2 6 3.19405 6 4.66698C6 6.1399 7.19405 7.33395 8.66698 7.33395Z"
                                                        stroke="#3D3D3D" stroke-width="1.33331" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </span> Per Transactions</th>
                                        <th> <span><svg width="17" height="16" viewBox="0 0 17 16" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <g clip-path="url(#clip0_259_8119)">
                                                        <path d="M8.2002 12V14.667" stroke="#DBDBDB" stroke-width="1.33331"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M11.0264 10.8267L12.9133 12.7135" stroke="#DBDBDB"
                                                            stroke-width="1.33331" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M3.48633 12.7135L5.37321 10.8267" stroke="#DBDBDB"
                                                            stroke-width="1.33331" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M12.2002 8H14.8672" stroke="#DBDBDB" stroke-width="1.33331"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M1.5332 8H4.20018" stroke="#3D3D3D" stroke-width="1.33331"
                                                            stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M11.0264 5.17363L12.9133 3.28674" stroke="#3D3D3D"
                                                            stroke-width="1.33331" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M3.48633 3.28674L5.37321 5.17363" stroke="#3D3D3D"
                                                            stroke-width="1.33331" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path d="M8.2002 1.33337V4.00035" stroke="#3D3D3D"
                                                            stroke-width="1.33331" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </g>
                                                    <defs>
                                                        <clipPath id="clip0_259_8119">
                                                            <rect width="16" height="16" fill="white"
                                                                transform="translate(0.200195)" />
                                                        </clipPath>
                                                    </defs>
                                                </svg>
                                            </span> Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transactions as $item)
                                        <tr>
                                            <td>
                                                <span>{{ $item->portfolio->portfolio_name }}</span>
                                            </td>
                                            <td>
                                                <span>{{ $currencySymbol . $item->amount_of_transactions }}</span>
                                            </td>
                                            <td>
                                                <span>{{ $item->point }} {{ __('Points') }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="text-center">
                                            <td colspan="3">{{ __('No data found') }}</td>
                                        </tr>
                                    @endforelse


                                    <!-- Add more rows as necessary -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- Reward redeem section end -->
@endsection
