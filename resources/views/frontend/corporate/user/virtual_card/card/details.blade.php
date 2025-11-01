@php use App\Enums\TxnStatus; @endphp
@extends('frontend::layouts.user')
@section('title')
    {{ __('Card Details') }}
@endsection
@push('style')
    <link rel="stylesheet" href="{{ asset('front/css/daterangepicker.css') }}">
    <style>
        .banking-card-grid {
            display: grid;
            grid-template-columns: 1.25fr 1fr 1fr;
            gap: 30px;
        }

        @media only screen and (min-width: 992px) and (max-width: 1199px) {
            .banking-card-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 575px),
        only screen and (min-width: 576px) and (max-width: 767px),
        only screen and (min-width: 768px) and (max-width: 991px) {
            .banking-card-grid {
                grid-template-columns: 1fr;
            }
        }

        .banking-card-box {
            border-radius: 16px;
            box-shadow: rgba(0, 0, 0, 0.08) 0px 5px 22px;
            background-color: rgb(33, 33, 33);
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
            padding: 48px 32px;
            max-width: 500px;
        }

        .banking-card-box .card-logo-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .banking-card-box .card-number {
            margin-bottom: 25px;
        }

        .banking-card-box .card-number span {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: #fff;
        }

        @media (max-width:450px) {
            .banking-card-box .card-number span {
                font-size: 20px;
            }
        }

        .banking-card-box .card-more-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px 12px;
        }

        .banking-card-box .card-owner .title {
            font-size: 14px;
            font-weight: 400;
            line-height: 1.57;
            color: #fff;
            display: block;
            margin-bottom: 5px;
        }

        .banking-card-box .card-owner .name {
            font-size: 20px;
            font-weight: 600;
            color: #fff;
        }

        .banking-card-box .card-expiry-info .title {
            font-size: 14px;
            font-weight: 400;
            line-height: 1.57;
            color: #fff;
            display: block;
            margin-bottom: 5px;
        }

        .banking-card-box .card-expiry-info .date {
            font-size: 18px;
            font-weight: 600;
            color: #fff;
        }

        .banking-dollar-card {
            background-color: rgb(255, 255, 255);
            color: rgb(17, 25, 39);
            height: 100%;
            transition: box-shadow 300ms cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            border-radius: 20px;
            padding: 30px 25px;
            box-shadow: 0px 20px 30px rgba(3, 4, 28, 0.06);
            border: 1px solid #ddd;
        }

        .banking-dollar-card .inner {
            display: flex;
            justify-content: space-between;
        }

        .banking-dollar-card .inner .dollar-icon span {
            width: 60px;
            height: 60px;
            display: block;
        }

        .banking-dollar-card .inner .dollar-content .dollar-info span {
            margin-bottom: 5px;
            display: block;
        }

        .banking-dollar-card .inner .dollar-content .dollar-info:not(:last-child) {
            margin-bottom: 20px;
        }

        .banking-dollar-card .inner .dollar-content .dollar-info h5 {
            font-size: 24px;
        }

        .banking-contents-card {
            background-color: rgb(255, 255, 255);
            color: rgb(17, 25, 39);
            height: 100%;
            transition: box-shadow 300ms cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            border-radius: 20px;
            padding: 30px 25px;
            box-shadow: 0px 20px 30px rgba(3, 4, 28, 0.06);
            border: 1px solid #ddd;
        }

        .banking-contents-card .card-type {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px 5px;
        }

        .banking-contents-card .card-type .card-type-content .title {
            font-size: 16px;
            font-weight: 700;
            display: block;
            margin-bottom: 5px;
        }

        .banking-contents-card .card-currency {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .banking-contents-card .card-currency .status-badge {
            border-radius: 96px;
            color: rgb(181, 71, 8);
            background-color: rgba(247, 144, 9, 0.12);
            display: block;
            padding: 6px 15px;
            line-height: 1;
        }

        .banking-contents-card .card-currency-content .title {
            font-size: 16px;
            font-weight: 700;
            display: block;
            margin-bottom: 5px;
        }

        .banking-contents-card .card-currency-content .description {
            margin-bottom: 3px;
        }

        .banking-contents-card .card-currency-content .description:last-child {
            margin-bottom: 0;
        }

        .banking-card-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #ff7e42;
            height: 40px;
            padding: 0 20px;
            border-radius: 8px;
            color: #fff;
            font-weight: 500;
        }

        .banking-card-btn:hover {
            color: #fff;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-12">
            <div class="site-card">
                <div class="site-card-header d-flex justify-content-between">
                    <div class="title-small">{{ __('Card Details') }}</div>
                    @if (setting('card_topup', 'permission'))
                        <a href="javascript:void(0)" class="site-btn-sm polis-btn" data-bs-toggle="modal"
                            data-bs-target="#cardTopupModal">
                            <i data-lucide="plus-circle"></i>
                            {{ __('Add Card Balance') }}
                        </a>
                    @endif
                </div>
                <div class="site-card-body p-0 overflow-x-auto">
                    <div class="container">
                        <div class="row">
                            <div class="banking-card-area section-space mt-100">
                                <div class="banking-card-grid">
                                    <div class="banking-card-box"
                                        data-background="{{ asset('front/images') }}/bank-card-bg.png">
                                        <div class="card-logo-inner">
                                            <div class="card-logo">
                                                <img src="{{ asset('front/images') }}/contactless.svg" alt="">
                                            </div>
                                            <div class="network-logo">
                                                <img src="{{ asset('front/images') }}/logo-visa.svg" alt="logo-visa">
                                            </div>
                                        </div>
                                        <div class="card-number">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="hidden">
                                                    <span>****</span>
                                                    <span>****</span>
                                                    <span>****</span>
                                                    <span>
                                                        {{ $card->last_four_digits }}
                                                    </span>
                                                </div>
                                                <div class="show">
                                                    @foreach (str_split($card->card_number, 4) as $number)
                                                        <span>{{ $number }}</span>
                                                    @endforeach
                                                </div>

                                                <a href="javascript:void(0)">
                                                    <i data-lucide="eye" class="text-white" id="showCardNumber"></i>
                                                    <i data-lucide="eye-off" class="text-white" id="hideCardNumber"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-more-info">
                                            <div class="card-owner">
                                                <span class="title">{{ __('Cardholder name') }}</span>
                                                <h5 class="name">
                                                    {{ $card?->cardHolder?->name }}
                                                </h5>
                                            </div>
                                            <div class="card-expiry-info">
                                                <span class="title">{{ __('Expiry date') }}</span>
                                                <h5 class="date">
                                                    <div class="hidden">
                                                        **/****
                                                    </div>
                                                    <div class="show">
                                                        {{ $card->expiration_month }} / {{ $card->expiration_year }}
                                                    </div>
                                                </h5>
                                            </div>
                                            <div class="card-expiry-info">
                                                <span class="title">{{ __('CVC') }}</span>
                                                <h5 class="date">
                                                    <div class="hidden">
                                                        ***
                                                    </div>
                                                    <div class="show">
                                                        {{ $card->cvc }}
                                                    </div>
                                                </h5>
                                            </div>
                                            <div class="card-chip">
                                                <img src="{{ asset('front/images') }}/chip.svg" alt="chip">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="banking-dollar-card">
                                        <div class="inner">
                                            <div class="dollar-content">
                                                <div class="dollar-info">
                                                    <span>{{ __('Spending limit') }}:</span>
                                                    <h4>{{ setting('currency_symbol', 'global') . $card->amount }}</h4>
                                                </div>
                                            </div>
                                            <div class="dollar-icon">
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                        fill="currentColor" aria-hidden="true" data-slot="icon">
                                                        <path
                                                            d="M10.464 8.746c.227-.18.497-.311.786-.394v2.795a2.252 2.252 0 0 1-.786-.393c-.394-.313-.546-.681-.546-1.004 0-.323.152-.691.546-1.004ZM12.75 15.662v-2.824c.347.085.664.228.921.421.427.32.579.686.579.991 0 .305-.152.671-.579.991a2.534 2.534 0 0 1-.921.42Z">
                                                        </path>
                                                        <path fill-rule="evenodd"
                                                            d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v.816a3.836 3.836 0 0 0-1.72.756c-.712.566-1.112 1.35-1.112 2.178 0 .829.4 1.612 1.113 2.178.502.4 1.102.647 1.719.756v2.978a2.536 2.536 0 0 1-.921-.421l-.879-.66a.75.75 0 0 0-.9 1.2l.879.66c.533.4 1.169.645 1.821.75V18a.75.75 0 0 0 1.5 0v-.81a4.124 4.124 0 0 0 1.821-.749c.745-.559 1.179-1.344 1.179-2.191 0-.847-.434-1.632-1.179-2.191a4.122 4.122 0 0 0-1.821-.75V8.354c.29.082.559.213.786.393l.415.33a.75.75 0 0 0 .933-1.175l-.415-.33a3.836 3.836 0 0 0-1.719-.755V6Z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="banking-contents-card">
                                        <div class="card-type">
                                            <div class="card-type-content">
                                                <h5 class="title">{{ __('Card Type') }}</h5>
                                                <span>{{ __('Virtual') }}</span>
                                            </div>
                                            <div class="card-type-content">
                                                <h5 class="title">{{ __('Card Created') }}</h5>
                                                <span>
                                                    {{ Carbon\Carbon::parse($card->created_at)->format('M d, Y') }}
                                                </span>
                                            </div>
                                            <div class="card-type-link">
                                                <a class="site-btn-sm polis-btn"
                                                    href="{{ route('user.card.status', $card->card_id) }}">
                                                    {!! $card->status == 'active'
                                                        ? '<i
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            data-lucide="shield-off"></i>' .
                                                            __('Deactivate')
                                                        : '<i
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            data-lucide="shield-check"></i>' .
                                                            __('Activate') !!}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-currency">
                                            <div class="card-currency-content">
                                                <h5 class="title">{{ __('Card Currency') }}</h5>
                                                <span>
                                                    {{ strtoupper($card?->currency) }}
                                                </span>
                                            </div>
                                            <div class="card-currency-status">
                                                <span class="status-badge">
                                                    {{ ucfirst($card->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-address">
                                            <div class="card-currency-content">
                                                <h5 class="title">{{ __('Billing Address') }}</h5>
                                                <p class="description">
                                                    {{ $card->cardHolder->address }}
                                                </p>
                                                <p class="description">{{ $card->cardHolder->city }} ,
                                                    {{ $card->cardHolder->state }} - {{ $card->cardHolder->country }}
                                                    {{ $card->cardHolder->postal_code }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-md-12 col-12">
            <div class="site-card">
                <div class="site-card-header d-flex justify-content-between">
                    <div class="title-small">{{ __('Transactions') }}</div>

                    <a href="{{ route('user.card.transaction.sync', $card->id) }}" class="site-btn-sm polis-btn">
                        <i data-lucide="refresh-ccw"></i> {{ __('Sync') }}
                    </a>
                </div>
                <div class="site-card-body p-0 overflow-x-auto">
                    <div class="site-custom-table">
                        <div class="contents">
                            <div class="site-table-list site-table-head">
                                <div class="site-table-col">{{ __('Transaction') }}</div>
                                <div class="site-table-col">{{ __('Date') }}</div>
                                <div class="site-table-col">{{ __('Amount') }}</div>
                                <div class="site-table-col">{{ __('Status') }}</div>
                            </div>
                            @foreach ($transactions as $transaction)
                                <div class="site-table-list">
                                    <div class="site-table-col">
                                        <div class="trx fw-bold">
                                            {{ data_get($transaction, 'merchant_data.name') }}
                                        </div>
                                    </div>
                                    <div class="site-table-col">
                                        {{ $transaction?->created }}
                                    </div>
                                    <div class="site-table-col">
                                        <div class="fw-bold">
                                            {{ $transaction->amount . ' ' . ucwords($transaction->currency) }}
                                        </div>
                                    </div>
                                    <div class="site-table-col">
                                        <div class="type site-badge badge-primary">{{ __('Success') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if (count($transactions) == 0)
                            <div class="no-data-found">{{ __('No Data Found') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="cardTopupModal" tabindex="-1" aria-labelledby="openTicketModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content site-table-modal">
                <div class="modal-body popup-body"> <button type="button" class="modal-btn-close"
                        data-bs-dismiss="modal" aria-label="Close"> <i data-lucide="x"></i> </button>
                    <div class="popup-body-text">
                        <div class="title">{{ __('Card Balance Top Up') }}</div>

                        <form action="{{ route('user.card.balance.update', $card->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="card_id" value="{{ $card->id }}">
                            <div class="step-details-form">
                                <div class="row">
                                    <div class="col-xl-12 mb-5">
                                        <div class="form-check form-check-inline">
                                            <input onclick="changePaymentType('my_wallet')" class="form-check-input"
                                                type="radio" name="type" id="my_wallet" value="my_wallet" checked>
                                            <label class="form-check-label" for="my_wallet">
                                                {{ __('My Balance') }}
                                            </label>
                                        </div>
                                        {{-- <div class="form-check form-check-inline">
                                        <input onclick="changePaymentType('auto_payment')" class="form-check-input" type="radio" name="type" id="auto_payment" value="auto_payment">
                                        <label class="form-check-label" for="auto_payment">
                                            {{ __('Payment Gateways') }}
                                    </label>
                                </div> --}}
                                    </div>
                                    <div class="col-xl-12 col-lg-12 col-md-12" id="my_wallet_part">
                                        <p>
                                            {{ __('Default Account') }}:
                                            {{ setting('currency_symbol', 'global') . $user->balance }}
                                        </p>
                                    </div>
                                    {{-- <div class="col-xl-12 col-lg-12 col-md-12" id="auto_payment_part">
                                    <div class="inputs">
                                        <label for="" class="input-label">{{ __('Select Gateway') }}<span
                                class="required">*</span></label>
                            <select name="gateway_code" class="box-input deposit-methods" id="gatewaySelect">
                                <option value="" disabled selected>--{{ __('Select Gateway') }}--</option>
                                @foreach ($gateways as $gateway)
                                <option data-logo="{{ asset($gateway->logo) }}" value="{{ $gateway->gateway_code }}">
                                    {{ $gateway->name }}</option>
                                @endforeach
                            </select>
                            <div class="input-info-text charge"></div>
                        </div>
                </div> --}}
                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                        <div class="inputs">
                                            <label for="" class="input-label">{{ __('Amount') }}<span
                                                    class="required">*</span></label>
                                            <input type="text" class="box-input" name="amount">
                                            <div class="input-info-text min-max my-2">
                                                {{ __('Minimum :min_amount and Maximum :max_amount', ['min_amount' => setting('min_card_topup', 'virtual_card') . ' ' . $currency, 'max_amount' => setting('max_card_topup', 'virtual_card') . ' ' . $currency]) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="action-btns">
                                <button type="submit" class="site-btn-sm primary-btn me-2">
                                    <i data-lucide="check"></i> {{ __('Topup Now') }}
                                </button>

                                <button type="button" class="site-btn-sm red-btn" data-bs-dismiss="modal"
                                    aria-label="Close">
                                    <i data-lucide="x"></i> {{ __('Close') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $('#my_wallet').is(':checked') ? $('#auto_payment_part').addClass('d-none') : $('#my_wallet_part').addClass(
            'd-none');

        function changePaymentType(type) {
            if (type == 'my_wallet') {
                $('#my_wallet_part').removeClass('d-none');
                $('#auto_payment_part').addClass('d-none');
            } else {
                $('#my_wallet_part').addClass('d-none');
                $('#auto_payment_part').removeClass('d-none');
            }
        }

        $('#showCardNumber').click(function() {
            $('.hidden').addClass('d-none');
            $('.show').removeClass('d-none');
            $('#showCardNumber').addClass('d-none');
            $('#hideCardNumber').removeClass('d-none');
        });

        $('#hideCardNumber').click(function() {
            $('.hidden').removeClass('d-none');
            $('.show').addClass('d-none');
            $('#showCardNumber').removeClass('d-none');
            $('#hideCardNumber').addClass('d-none');
        });

        $(document).ready(function() {
            $('#hideCardNumber').click();
        });
    </script>
@endpush