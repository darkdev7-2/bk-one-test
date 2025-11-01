@php use App\Enums\TxnStatus; @endphp
@extends('frontend::layouts.user')
@section('title')
{{ __('Virtual Card') }}
@endsection
@push('style')
<link rel="stylesheet" href="{{ asset('front/css/daterangepicker.css') }}">
@endpush
@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-12">
        <div class="site-card">
            <div class="site-card-header d-flex justify-content-between">
                <div class="title-small">{{ __('Cards') }}</div>

                @if(setting('card_creation','permission'))
                <a href="javascript:void(0)" class="site-btn-sm primary-btn" data-bs-toggle="modal"
                    data-bs-target="#createWalletModal">
                    <i data-lucide="plus-circle"></i>
                    {{ __('Create a new card') }}
                </a>
                @endif
            </div>
            <div class="site-card-body p-0 overflow-x-auto">
                <div class="site-custom-table">
                    <div class="contents">
                        <div class="site-table-list site-table-head">
                            <div class="site-table-col">{{ __('Cardholder Name') }}</div>
                            <div class="site-table-col">{{ __('Card Last 4') }}</div>
                            <div class="site-table-col">{{ __('Balance') }}</div>
                            <div class="site-table-col">{{ __('Status') }}</div>
                            <div class="site-table-col">{{ __('Created At') }}</div>
                            <div class="site-table-col">{{ __('Action') }}</div>
                        </div>
                        @foreach ($cards as $card)
                            <div class="site-table-list">
                                <div class="site-table-col">
                                    <div class="trx fw-bold">{{ $card?->cardHolder?->name }}</div>
                                </div>
                                <div class="site-table-col">
                                    <div class="trx fw-bold">{{ $card?->last_four_digits }}</div>
                                </div>
                                <div class="site-table-col">
                                    <div class="trx fw-bold">
                                        {{ setting('currency_symbol','global') . $card?->amount }}
                                    </div>
                                </div>
                                <div class="site-table-col">
                                    @if($card->status == 'active')
                                        <div class="type site-badge badge-primary">{{ $card->status }}</div>
                                    @else
                                        <div class="type site-badge badge-failed">{{ $card->status }}</div>
                                    @endif
                                </div>
                                <div class="site-table-col">
                                    <div class="trx fw-bold">{{ Carbon\Carbon::parse($card->created_at)->format('M d, Y') }}</div>
                                </div>
                                <div class="site-table-col">
                                    <div class="action">
                                        <a href="{{ route('user.card.details', $card->card_id) }}"
                                        class="icon-btn details-btn">
                                            <i data-lucide="eye"></i>
                                            {{ __('Details') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if(count($cards) == 0)
                        <div class="no-data-found">{{ __('No Data Found') }}</div>
                    @endif
                </div>

                <div class="modal fade" id="createWalletModal" aria-labelledby="openTicketModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content site-table-modal">
                            <div class="modal-body popup-body"> <button type="button" class="modal-btn-close" data-bs-dismiss="modal" aria-label="Close"> <i data-lucide="x"></i> </button>
                                <div class="popup-body-text">
                                    <div class="title">{{ __('Create a new card') }}</div>

                                    <form action="{{ route('user.card.store') }}" method="post">
                                        @csrf
                                        {{-- all card_providers --}}
                                        <div class="step-details-form">
                                            <div class="row">
                                                <div class="col-xl-12 col-lg-12 col-md-12">
                                                    <div class="inputs">
                                                        <label for="" class="input-label">{{ __('Choose Card Provider') }}<span class="required">*</span></label>
                                                        <select class="add-priority box-input page-count" id="card_provider_name" name="card_provider_name">
                                                            <option selected disabled value="">{{ __('Select Card Provider') }}</option>
                                                            @foreach ($card_providers as $card_provider)
                                                                <option value="{{ $card_provider->name }}">
                                                                    {{ $card_provider->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-holder-block">
                                            <div class="step-details-form">
                                                <div class="row">
                                                    <div class="col-xl-12 col-md-12 inputs">
                                                        <label for="" class="input-label">
                                                            {{ __('Cardholder') }}
                                                            <span class="required">*</span></label>
                                                        <br>
                                                        <div class="form-check form-check-inline">
                                                            <input onclick="changeCardholderType('existing_one')" class="form-check-input" type="radio" name="type" id="existing_one" value="existing_one" checked>
                                                            <label class="form-check-label" for="existing_one">
                                                                {{ __('Existing Cardholders') }}
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input onclick="changeCardholderType('new_one')" class="form-check-input" type="radio" name="type" id="new_one" value="new_one">
                                                            <label class="form-check-label" for="new_one">
                                                                {{ __('Create New Cardholder') }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
    
                                            <div class="step-details-form" id="existing_cardholder_part">
                                                <div class="row">
                                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                                        <div class="inputs">
                                                            <label for="" class="input-label">{{ __('Choose Cardholder') }}<span class="required">*</span></label>
                                                            <select class="add-priority box-input page-count" name="cardholder_id">
                                                                <option selected disabled value="">{{ __('Select Cardholder') }}</option>
                                                                @foreach ($card_holders as $card_holder)
                                                                    <option value="{{ $card_holder->id }}">
                                                                        {{ $card_holder->name }} - {{ $card_holder->email }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="step-details-form" id="new_cardholder_part">
                                                <div class="row">
                                                    <div class="col-xl-12 col-md-12 inputs">
                                                        <label class="form-label">{{ __('Name') }} <span class="required">*</span></label>
                                                        <div class="input-group">
                                                            <input type="text" name="name" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-12 col-md-12 inputs">
                                                        <label class="form-label">{{ __('Email') }} <span class="required">*</span></label>
                                                        <div class="input-group">
                                                            <input type="text" name="email" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-12 col-md-12 inputs">
                                                        <label class="form-label">{{ __('Phone Number') }} <span class="required">*</span></label>
                                                        <div class="input-group">
                                                            <input type="text" name="phone_number" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-12 col-md-12 inputs">
                                                        <label class="form-label">{{ __('Address') }} <span class="required">*</span></label>
                                                        <div class="input-group">
                                                            <input type="text" name="address" class="form-control">
                                                        </div>
                                                    </div>
                                                   <div class="row">
                                                        <div class="col-xl-4 col-md-4 inputs">
                                                            <label class="form-label">{{ __('City') }} <span class="required">*</span></label>
                                                            <div class="input-group">
                                                                <input type="text" name="city" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-4 col-md-4 inputs">
                                                            <label class="form-label">{{ __('State') }} <span class="required">*</span></label>
                                                            <div class="input-group">
                                                                <input type="text" name="state" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-4 col-md-4 inputs">
                                                            <label class="form-label">{{ __('Postal Code') }} <span class="required">*</span></label>
                                                            <div class="input-group">
                                                                <input type="text" name="postal_code" class="form-control">
                                                            </div>
                                                        </div>
                                                   </div>
                                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                                        <div class="inputs">
                                                            <label for="" class="input-label">{{ __('Country') }}<span class="required">*</span></label>
                                                            <select class="country-select-input" name="country">
                                                                <option selected value="">{{ __('Select Country') }}</option>
                                                                @foreach ($countries_list as $country)
                                                                    <option value="{{ $country['code'] }}">{{ $country['name'] }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="action-btns">
                                            <button type="submit" class="site-btn-sm primary-btn me-2">
                                                <i data-lucide="check"></i> {{ __('Create New') }}
                                            </button>

                                            <button type="button" class="site-btn-sm red-btn" data-bs-dismiss="modal" aria-label="Close">
                                                <i data-lucide="x"></i> {{ __('Close') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
        $('#existing_one').is(':checked') ? $('#new_cardholder_part').addClass('d-none') : $('#existing_cardholder_part').addClass('d-none');

        function changeCardholderType(type){
            if(type == 'existing_one'){
                $('#existing_cardholder_part').removeClass('d-none');
                $('#new_cardholder_part').addClass('d-none');
            }else{
                $('#existing_cardholder_part').addClass('d-none');
                $('#new_cardholder_part').removeClass('d-none');
            }
        }

        
        // on createWalletModal modal open
        $('#createWalletModal').on('show.bs.modal', function (e) {
            if(!$('.country-select-input').hasClass('select2-hidden-accessible')){
                $('.country-select-input').select2({
                    placeholder: "{{ __(key: 'Select Country') }}",
                    dropdownParent: $('#createWalletModal')
                });
            }
        });

        $(document).on('change','#card_provider_name',function(){
            var card_provider_name = $(this).val();
            if(['Stripe Virtual Card','Ufitpay Virtual Card'].includes(card_provider_name)){
                $('.card-holder-block').removeClass('d-none');
            }else{
                $('.card-holder-block').addClass('d-none');
            }
        });
    </script>
@endpush
