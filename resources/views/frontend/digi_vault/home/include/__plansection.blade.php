<!-- Pricing section start -->
<section class="td-pricing-section include-bg p-relative z-index-11 section_space"
    data-background="{{ asset('front/digi_vault/images/bg/pricing-bg.png') }}">
    <div class="container">
        <div class="row gy-30">
            <div class="col-xxl-12">
                <div class="row align-items-center justify-content-between">
                    <div class="col-xxl-6 col-xl-6 col-lg-6">
                        <div class="section-title-wrapper section_title_space">
                            <span class="section-subtitle has_fade_anim">{{ $data['title_small'] }}</span>
                            <h2 class="section-title has_fade_anim">{{ $data['title_big'] }}</h2>
                        </div>
                    </div>
                    <div class="col-xxl-6 col-xl-6 col-lg-6">
                        <div class="pricing-tab d-flex justify-content-md-end has_fade_anim">
                            <nav class="td-tab">
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-dps-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-dps" type="button" role="tab" aria-controls="nav-dps"
                                        aria-selected="true">{{ __('DPS') }}</button>
                                    <button class="nav-link" id="nav-fdr-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-fdr" type="button" role="tab" aria-controls="nav-fdr"
                                        aria-selected="false">{{ __('FDR') }}</button>
                                    <button class="nav-link" id="nav-loan-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-loan" type="button" role="tab"
                                        aria-controls="nav-loan" aria-selected="false">{{ __('Loan') }}</button>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            @php
                $dps_plans = App\Models\DpsPlan::active()->get();
                $fdr_plans = App\Models\FdrPlan::active()->get();
                $loan_plans = App\Models\LoanPlan::active()->get();
            @endphp

            <div class="col-xxl-12">
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-dps" role="tabpanel" aria-labelledby="nav-dps-tab">
                        <div class="row gy-30">
                            @foreach ($dps_plans as $plan)
                                <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6">
                                    <div class="has_fade_anim" data-delay="0.15">
                                        <div class="pricing-item">
                                            <div class="inner">
                                                <span class="info-badge">{{ $plan->badge }}</span>
                                                <div class="plan-heading">
                                                    <strong>{{ setting('currency_symbol', 'global') }}{{ $plan->per_installment }}</strong>
                                                    <sub> /{{ $plan->interval }} {{ __('Days') }}</sub>
                                                    <h5 class="title">{{ $plan->name }}</h5>
                                                </div>
                                                <div class="plan-lists">
                                                    <ul>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('Interest Rate') }} :
                                                                    {{ $plan->per_installment }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('Number of Installments') }} :
                                                                    {{ $plan->total_installment }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('Per Installment') }} :
                                                                    {{ $currencySymbol . $plan->per_installment }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('Installment Slice') }} :
                                                                    {{ $plan->interval }} {{ __('Days') }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('All Deposits') }} :
                                                                    {{ $currencySymbol . $plan->total_deposit }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="plan-content-list">
                                                                <div class="list-info">
                                                                    <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                        alt="check">
                                                                    <span>{{ __('Final Maturity') }} :
                                                                        {{ $currencySymbol . $plan->total_mature_amount }}</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="btn-inner">
                                                <a class="td-btn gradient-btn radius-8 w-100"
                                                    href="{{ route('user.dps.subscribe', $plan->id) }}">
                                                    <span class="btn-text">{{ __('Subscribe') }}</span>
                                                    <span class="btn-icon">
                                                        <svg width="19" height="19" viewBox="0 0 19 19"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M2.9017 9.6748C2.9017 9.36414 3.15355 9.1123 3.4642 9.1123H15.8392C16.1499 9.1123 16.4017 9.36414 16.4017 9.6748C16.4017 9.98547 16.1499 10.2373 15.8392 10.2373H3.4642C3.15355 10.2373 2.9017 9.98547 2.9017 9.6748Z"
                                                                fill="white" />
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M10.3789 4.21456C10.5986 3.99489 10.9548 3.99489 11.1744 4.21456L16.2369 9.27706C16.4566 9.49672 16.4566 9.85289 16.2369 10.0725L11.1744 15.135C10.9548 15.3547 10.5986 15.3547 10.3789 15.135C10.1593 14.9154 10.1593 14.5592 10.3789 14.3396L15.0437 9.67481L10.3789 5.01005C10.1593 4.79038 10.1593 4.43423 10.3789 4.21456Z"
                                                                fill="white" />
                                                        </svg>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-fdr" role="tabpanel" aria-labelledby="nav-fdr-tab">
                        <div class="row gy-30">
                            @foreach ($fdr_plans as $plan)
                                <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6">
                                    <div class="has_fade_anim" data-delay="0.15">
                                        <div class="pricing-item">
                                            <div class="inner">
                                                @if (!empty($plan->badge))
                                                    <span class="info-badge">{{ $plan->badge }}</span>
                                                @endif
                                                <div class="plan-heading">
                                                    <strong>{{ $currencySymbol }}{{ $plan->minimum_amount }}</strong>
                                                    <sub>/ {{ __('Min') }}</sub>
                                                    <h5 class="title">{{ $plan->name }}</h5>
                                                </div>
                                                <div class="plan-lists">
                                                    <ul>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('Lock In Period') }} : {{ $plan->locked }}
                                                                    {{ __('Days') }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('Get Profit Every') }} :
                                                                    {{ $plan->intervel }} {{ __('Days') }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('Profit Rate') }} :
                                                                    {{ $plan->interest_rate }}%</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('Minimum FDR') }} :
                                                                    {{ $currencySymbol . $plan->minimum_amount }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('Maximum FDR') }} :
                                                                    {{ $currencySymbol . $plan->maximum_amount }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('Compounding') }} :
                                                                    {{ $plan->is_compounding ? __('Yes') : __('No') }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('Cancel In') }} :
                                                                    @if ($plan->can_cancel)
                                                                        {{ $plan->cancel_type == 'anytime' ? __('Anytime') : $plan->cancel_days . ' ' . __('Days') }}
                                                                    @else
                                                                        {{ __('No') }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="btn-inner">
                                                <a class="td-btn gradient-btn radius-8 w-100"
                                                    href="{{ route('user.fdr.subscribe', $plan->id) }}">
                                                    <span class="btn-text">{{ __('Subscribe') }}</span>
                                                    <span class="btn-icon">
                                                        <svg width="19" height="19" viewBox="0 0 19 19"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M2.9017 9.6748C2.9017 9.36414 3.15355 9.1123 3.4642 9.1123H15.8392C16.1499 9.1123 16.4017 9.36414 16.4017 9.6748C16.4017 9.98547 16.1499 10.2373 15.8392 10.2373H3.4642C3.15355 10.2373 2.9017 9.98547 2.9017 9.6748Z"
                                                                fill="white" />
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M10.3789 4.21456C10.5986 3.99489 10.9548 3.99489 11.1744 4.21456L16.2369 9.27706C16.4566 9.49672 16.4566 9.85289 16.2369 10.0725L11.1744 15.135C10.9548 15.3547 10.5986 15.3547 10.3789 15.135C10.1593 14.9154 10.1593 14.5592 10.3789 14.3396L15.0437 9.67481L10.3789 5.01005C10.1593 4.79038 10.1593 4.43423 10.3789 4.21456Z"
                                                                fill="white" />
                                                        </svg>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-loan" role="tabpanel" aria-labelledby="nav-loan-tab">
                        <div class="row gy-30">
                            @foreach ($loan_plans as $plan)
                                <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6">
                                    <div class="has_fade_anim" data-delay="0.15">
                                        <div class="pricing-item">
                                            <div class="inner">
                                                <span class="info-badge">{{ $plan->badge }}</span>
                                                <div class="plan-heading">
                                                    <strong>{{ setting('currency_symbol', 'global') }}{{ $plan->per_installment }}</strong>
                                                    <sub> /{{ $plan->interval }} {{ __('Days') }}</sub>
                                                    <h5 class="title">{{ $plan->name }}</h5>
                                                </div>
                                                <div class="plan-lists">
                                                    <ul>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('Interest Rate') }} :
                                                                    {{ $plan->per_installment }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('Number of Installments') }} :
                                                                    {{ $plan->total_installment }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('Per Installment') }} :
                                                                    {{ $currencySymbol . $plan->per_installment }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('Installment Slice') }} :
                                                                    {{ $plan->interval }} {{ __('Days') }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="list-info">
                                                                <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                    alt="check">
                                                                <span>{{ __('All Deposits') }} :
                                                                    {{ $currencySymbol . $plan->total_deposit }}</span>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <div class="plan-content-list">
                                                                <div class="list-info">
                                                                    <img src="{{ asset('front/digi_vault/images/icons/check.svg') }}"
                                                                        alt="check">
                                                                    <span>{{ __('Final Maturity') }} :
                                                                        {{ $currencySymbol . $plan->total_mature_amount }}</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="btn-inner">
                                                <a class="td-btn gradient-btn radius-8 w-100"
                                                    href="{{ route('user.dps.subscribe', $plan->id) }}">
                                                    <span class="btn-text">{{ __('Subscribe') }}</span>
                                                    <span class="btn-icon">
                                                        <svg width="19" height="19" viewBox="0 0 19 19"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M2.9017 9.6748C2.9017 9.36414 3.15355 9.1123 3.4642 9.1123H15.8392C16.1499 9.1123 16.4017 9.36414 16.4017 9.6748C16.4017 9.98547 16.1499 10.2373 15.8392 10.2373H3.4642C3.15355 10.2373 2.9017 9.98547 2.9017 9.6748Z"
                                                                fill="white" />
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M10.3789 4.21456C10.5986 3.99489 10.9548 3.99489 11.1744 4.21456L16.2369 9.27706C16.4566 9.49672 16.4566 9.85289 16.2369 10.0725L11.1744 15.135C10.9548 15.3547 10.5986 15.3547 10.3789 15.135C10.1593 14.9154 10.1593 14.5592 10.3789 14.3396L15.0437 9.67481L10.3789 5.01005C10.1593 4.79038 10.1593 4.43423 10.3789 4.21456Z"
                                                                fill="white" />
                                                        </svg>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Pricing section end -->
