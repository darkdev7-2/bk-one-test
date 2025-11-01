@if(request('tab') == 'card')
<div @class([ 'tab-pane fade' , 'show active'=> request('tab') == 'card'
    ])
    id="pills-loan"
    role="tabpanel"
    aria-labelledby="pills-loan-tab"
    >
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="site-card">
                <div class="site-card-header">
                    <h4 class="title">{{ __('Virtual Card') }}</h4>
                </div>
                <div class="site-card-body">
                    <div class="row">
                        @foreach ($cards as $card)
                            <div class="col-xl-6">
                                <div class="site-card">
                                    <div class="site-card-body">
                                        <div class="profile-text-data">
                                            <div class="attribute">{{ __('Cardholder Name') }}</div>
                                            <div class="value">
                                                {{ $card?->cardHolder?->name }}
                                            </div>
                                        </div>
                                        <div class="profile-text-data">
                                            <div class="attribute">{{ __('Card Number') }}</div>
                                            <div class="value">
                                                **** **** **** {{ $card?->last_four_digits }}
                                            </div>
                                        </div>
                                        <div class="profile-text-data">
                                            <div class="attribute">{{ __('Card Expiry') }}</div>
                                            <div class="value">
                                                {{ $card?->expiration_month }} / {{ $card?->expiration_year }}
                                            </div>
                                        </div>
                                        <div class="profile-text-data">
                                            <div class="attribute">{{ __('Card Balance') }}</div>
                                            <div class="value">
                                                {{ setting('currency_symbol','global') . $card->amount }}
                                            </div>
                                        </div>
                                        <div class="profile-text-data">
                                            <div class="attribute">{{ __('Card Status') }}</div>
                                            <div class="value">
                                                <div
                                                    class="site-badge {{ $card?->status == 'active' ? 'success':'danger' }}">
                                                    {{ ucfirst($card?->status) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            @can('virtual-card-status-change')
                                            <a href="{{ route('admin.user.card.status.update', $card->card_id) }}"
                                                class="site-btn-sm {{ $card?->status == 'active' ? 'red':'green' }}-btn">
                                                {!! $card->status == 'active' ? '<i data-lucide="shield-off"></i>'.__('Deactivate') : '<i data-lucide="shield-check"></i>'.__('Activate') !!}
                                            </a>
                                            &nbsp;
                                            @endcan
                                            @can('virtual-card-topup')
                                            <button type="button" class="site-btn-sm primary-btn" data-bs-toggle="modal" data-bs-target="#topUpCard_{{ $card->id }}">
                                                <i data-lucide="plus-circle"></i> {{ __('Top Up Card') }}
                                            </button>
                                            @endcan
                                        </div>
                                    </div>

                                </div>
                            </div>
                            @can('virtual-card-topup')
                            <div class="modal fade" id="topUpCard_{{ $card->id }}" tabindex="-1" aria-labelledby="addSubBalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-md modal-dialog-centered">
                                    <div class="modal-content site-table-modal">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addSubBalLabel">
                                                {{ __('Card Top Up') }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('admin.user.card.balance.update', $card->id) }}" method="post">
                                                @csrf
                                                @method('PUT')
                                                <div class="row">
                                                    <div class="col-xl-12">
                                                        <div class="site-input-groups">
                                                            <label for="wallet" class="input-label mb-1">
                                                                {{ __('Amount') }}
                                                                <span class="required">*</span>
                                                            </label>
                                                            <div class="input-group joint-input">
                                                                <span class="input-group-text">{{ setting('site_currency','global') }}</span>
                                                                <input type="text" name="amount" oninput="this.value = validateDouble(this.value)"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-12">
                                                        <button type="submit" class="site-btn-sm primary-btn w-100">
                                                            {{ __('Apply Now') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endcan
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
