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

    <section class="rewards-badge-section position-relative section-space">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-6 col-lg-8">
                    <div class="section-title-wrapper text-center section-title-space">
                        <h2 class="section-title">{{ $data['title_one'] }}</h2>
                    </div>
                </div>
            </div>
            <div class="rewards-grid">
                @forelse ($redeems as $redeem)
                    <div class="rewards-item">
                        <div class="icon">
                            <img src="{{ asset($redeem->portfolio?->icon) }}" alt="rewards">
                        </div>
                        <div class="contents">
                            <h3 class="title">{{ $redeem->portfolio?->portfolio_name }}</h3>
                            <div class="rewards-point">
                                <span>{{ $redeem->point }} {{ __('Points') }} = {{ $redeem->amount }} {{ $currency }}</span>
                            </div>
                            <div class="btn-inner">
                            <a class="site-btn gdt-btn" href="{{ route('user.rewards.index') }}">{{ __('Redeem Now') }}</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div>
                        <p>{{ __('No data found') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="rewards-point-area bg-sugar-milk position-relative fix section-space">
        <div class="container">
            <div class="row justify-content-center">
              <div class="col-xxl-6 col-xl-6 col-lg-8">
                 <div class="section-title-wrapper text-center section-title-space">
                    <h2 class="section-title">
                        {{ $data['title_two'] }}
                    </h2>
                 </div>
              </div>
            </div>
            <div class="row gy-30" data-aos="fade-up" data-aos-duration="2000">
                <div class="col-xxl-12">
                   <div class="rewards-point-item">
                      <div class="reward-table table-responsive">
                         <table class="table">
                            <thead>
                               <tr>
                                    <th>{{ __('Portfolio List') }}</th>
                                    <th>{{ __('Per Transactions') }}</th>
                                    <th>{{ __('Points') }}</th>
                               </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $item)
                                    <tr>
                                        <td>{{ $item->portfolio->portfolio_name }}</td>
                                        <td>{{ $currencySymbol.$item->amount_of_transactions }}</td>
                                        <td>{{ $item->point }} {{ __('Points') }}</td>
                                    </tr>
                                @empty
                                    <tr class="text-center">
                                        <td colspan="3">{{ __('No data found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                         </table>
                      </div>
                   </div>
                </div>
            </div>
        </div>
    </section>
@endsection
