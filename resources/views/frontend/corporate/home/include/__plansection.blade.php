<section class="pricing-section section-space include-bg"
    data-background="{{ asset('front/theme-2') }}/images/bg/price-bg.png">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-6 col-xl-6 col-lg-6">
                <div class="section-title-wrapper text-center section-title-space">
                    <h2 class="section-title white-text mb-15">
                        {{ $data['title_big'] }}
                    </h2>
                    <P class="description white-text">
                        {{ $data['title_small'] }}
                    </P>
                </div>
            </div>
        </div>
        <div class="price-main-wrapper">
            <div class="price-tab-content">
                <h3 class="title">{{ __('Get Started Now') }}</h3>
                <div class="price-tab-list td-tab">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="dps-tab" data-bs-toggle="tab"
                                data-bs-target="#dps-tab-pane" type="button" role="tab"
                                aria-controls="dps-tab-pane" aria-selected="true">
                                <span class="icon"><svg width="44" height="44" viewBox="0 0 44 44"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="22" cy="22" r="20.75" fill="white" stroke="#FF7E42"
                                            stroke-width="2.5" />
                                        <path
                                            d="M29.6221 18.3445L28.2444 16.9668C28.1992 16.9214 28.1456 16.8855 28.0865 16.8609C28.0274 16.8364 27.964 16.8237 27.9 16.8237C27.836 16.8237 27.7727 16.8364 27.7136 16.8609C27.6545 16.8855 27.6008 16.9214 27.5557 16.9668L20.5114 24.403L16.4736 20.3637C16.381 20.2711 16.2555 20.2192 16.1246 20.2192C15.9938 20.2192 15.8682 20.2711 15.7757 20.3637L14.3799 21.7605C14.2873 21.8531 14.2354 21.9786 14.2354 22.1094C14.2354 22.2403 14.2873 22.3658 14.3799 22.4584L20.1225 28.334C20.1756 28.386 20.24 28.4249 20.3107 28.4476C20.3814 28.4704 20.4564 28.4763 20.5298 28.465C20.6053 28.4782 20.6829 28.4732 20.7562 28.4504C20.8294 28.4276 20.8962 28.3877 20.951 28.334L29.6221 19.0335C29.7135 18.9421 29.7649 18.8182 29.7649 18.689C29.7649 18.5597 29.7135 18.4358 29.6221 18.3444V18.3445Z"
                                            fill="#FF7E42" />
                                    </svg>
                                </span>
                                {{ __('DPS') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="fdr-tab" data-bs-toggle="tab" data-bs-target="#fdr-tab-pane"
                                type="button" role="tab" aria-controls="fdr-tab-pane" aria-selected="false">
                                <span class="icon"><svg width="44" height="44" viewBox="0 0 44 44"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="22" cy="22" r="20.75" fill="white" stroke="#FF7E42"
                                            stroke-width="2.5" />
                                        <path
                                            d="M29.6221 18.3445L28.2444 16.9668C28.1992 16.9214 28.1456 16.8855 28.0865 16.8609C28.0274 16.8364 27.964 16.8237 27.9 16.8237C27.836 16.8237 27.7727 16.8364 27.7136 16.8609C27.6545 16.8855 27.6008 16.9214 27.5557 16.9668L20.5114 24.403L16.4736 20.3637C16.381 20.2711 16.2555 20.2192 16.1246 20.2192C15.9938 20.2192 15.8682 20.2711 15.7757 20.3637L14.3799 21.7605C14.2873 21.8531 14.2354 21.9786 14.2354 22.1094C14.2354 22.2403 14.2873 22.3658 14.3799 22.4584L20.1225 28.334C20.1756 28.386 20.24 28.4249 20.3107 28.4476C20.3814 28.4704 20.4564 28.4763 20.5298 28.465C20.6053 28.4782 20.6829 28.4732 20.7562 28.4504C20.8294 28.4276 20.8962 28.3877 20.951 28.334L29.6221 19.0335C29.7135 18.9421 29.7649 18.8182 29.7649 18.689C29.7649 18.5597 29.7135 18.4358 29.6221 18.3444V18.3445Z"
                                            fill="#FF7E42" />
                                    </svg>
                                </span>
                                {{ __('FDR') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="loan-tab" data-bs-toggle="tab" data-bs-target="#loan-tab-pane"
                                type="button" role="tab" aria-controls="loan-tab-pane" aria-selected="false">
                                <span class="icon"><svg width="44" height="44" viewBox="0 0 44 44"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="22" cy="22" r="20.75" fill="white" stroke="#FF7E42"
                                            stroke-width="2.5" />
                                        <path
                                            d="M29.6221 18.3445L28.2444 16.9668C28.1992 16.9214 28.1456 16.8855 28.0865 16.8609C28.0274 16.8364 27.964 16.8237 27.9 16.8237C27.836 16.8237 27.7727 16.8364 27.7136 16.8609C27.6545 16.8855 27.6008 16.9214 27.5557 16.9668L20.5114 24.403L16.4736 20.3637C16.381 20.2711 16.2555 20.2192 16.1246 20.2192C15.9938 20.2192 15.8682 20.2711 15.7757 20.3637L14.3799 21.7605C14.2873 21.8531 14.2354 21.9786 14.2354 22.1094C14.2354 22.2403 14.2873 22.3658 14.3799 22.4584L20.1225 28.334C20.1756 28.386 20.24 28.4249 20.3107 28.4476C20.3814 28.4704 20.4564 28.4763 20.5298 28.465C20.6053 28.4782 20.6829 28.4732 20.7562 28.4504C20.8294 28.4276 20.8962 28.3877 20.951 28.334L29.6221 19.0335C29.7135 18.9421 29.7649 18.8182 29.7649 18.689C29.7649 18.5597 29.7135 18.4358 29.6221 18.3444V18.3445Z"
                                            fill="#FF7E42" />
                                    </svg>
                                </span>
                                {{ __('Loan') }}</button>
                        </li>
                    </ul>
                </div>
            </div>
            @php
                $dps_plans = App\Models\DpsPlan::active()->get();
                $fdr_plans = App\Models\FdrPlan::active()->get();
                $loan_plans = App\Models\LoanPlan::active()->get();
            @endphp
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="dps-tab-pane" role="tabpanel" aria-labelledby="dps-tab"
                    tabindex="0">
                    <div class="row gy-30">
                        @foreach ($dps_plans as $plan)
                            <div class="col-xxl-4 col-xl-4 col-lg-6">
                                <div class="price-item">
                                    <div class="price-heading">
                                        <h3 class="title">{{ $plan->name }}</h3>
                                    </div>
                                    <div class="price-value">
                                        <sup class="dollar">{{ setting('currency_symbol', 'global') }}</sup>
                                        <h4 class="title">{{ $plan->per_installment }}</h4>
                                        <sub>/{{ $plan->interval }} {{ __('Days') }}</sub>
                                    </div>
                                    <div class="price-list">
                                        <ul>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Interest Rate') }} :
                                                        {{ $plan->interest_rate }}%</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Number of Installments') }} :
                                                        {{ $plan->total_installment }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Per Installment') }} :
                                                        {{ $currencySymbol . $plan->per_installment }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Installment Slice') }} :
                                                        {{ $plan->interval }} {{ __('Days') }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('All Deposits') }} :
                                                        {{ $currencySymbol . $plan->total_deposit }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Final Maturity') }} :
                                                        {{ $currencySymbol . $plan->total_mature_amount }}</span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="price-link">
                                        <a href="{{ route('user.dps.subscribe', $plan->id) }}"
                                            class="site-btn gdt-btn w-100">{{ __('Subscribe') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="tab-pane fade" id="fdr-tab-pane" role="tabpanel" aria-labelledby="fdr-tab"
                    tabindex="0">
                    <div class="row">
                        @foreach ($fdr_plans as $plan)
                            <div class="col-xxl-4 col-xl-4">
                                <div class="price-item">
                                    <div class="price-heading">
                                        <h3 class="title">{{ $plan->name }}</h3>
                                    </div>
                                    <div class="price-value">
                                        <h4 class="title">{{ $plan->interest_rate }}</h4>
                                        <sup class="dollar">%</sup>
                                        <sub>/{{ $plan->intervel }} {{ __('Days') }}</sub>
                                    </div>
                                    <div class="price-list">
                                        <ul>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Lock In Period') }} :
                                                        {{ $plan->locked }} {{ __('Days') }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Get Profit Every') }} :
                                                        {{ $plan->intervel }} {{ __('Days') }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Profit Rate') }} :
                                                        {{ $plan->interest_rate }}%</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Minimum FDR') }} :
                                                        {{ $currencySymbol . $plan->minimum_amount }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Miximum FDR') }} :
                                                        {{ $currencySymbol . $plan->maximum_amount }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Compounding') }} :
                                                        {{ $plan->is_compounding ? 'Yes' : 'No' }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Cancel In') }} :
                                                        {{ $plan->can_cancel ? ($plan->cancel_type == 'anytime' ? 'Anytime' : $plan->cancel_days.' Days') : __('No') }}</span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="price-link">
                                        <a href="{{ route('user.fdr.subscribe', encrypt($plan->id)) }}"
                                            class="site-btn gdt-btn w-100">{{ __('Subscribe') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="tab-pane fade" id="loan-tab-pane" role="tabpanel" aria-labelledby="loan-tab"
                    tabindex="0">
                    <div class="row">
                        @foreach ($loan_plans as $plan)
                            <div class="col-xxl-4 col-xl-4">
                                <div class="price-item">
                                    <div class="price-heading">
                                        <h3 class="title">{{ $plan->name }}</h3>
                                    </div>
                                    <div class="price-value">
                                        <h4 class="title">{{ $plan->per_installment }}</h4>
                                        <sup class="dollar">%</sup>
                                        <sub>/{{ $plan->installment_intervel }} {{ __('Days') }}</sub>
                                    </div>
                                    <div class="price-list">
                                        <ul>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Minimum Loan') }} :
                                                        {{ $currencySymbol . $plan->minimum_amount }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Miximum Loan') }} :
                                                        {{ $currencySymbol . $plan->maximum_amount }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Installment Rate') }} :
                                                        {{ $plan->per_installment }}%</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Installment Slice') }} :
                                                        {{ $plan->installment_intervel }} {{ __('Days') }}</span>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="list-item">
                                                    <span class="icon">
                                                        <img src="{{ asset('front/theme-2') }}/images/icons/check-black.svg"
                                                            alt="check">
                                                    </span>
                                                    <span class="title">{{ __('Total Installment') }} :
                                                        {{ $plan->total_installment }}</span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="price-link">
                                        <a href="{{ route('user.loan.application', encrypt($plan->id)) }}"
                                            class="site-btn gdt-btn w-100">{{ __('Subscribe') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
