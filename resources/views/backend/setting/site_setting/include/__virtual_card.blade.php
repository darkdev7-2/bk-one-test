<div class="col-xl-6 col-lg-12 col-md-12 col-12">
    <div class="site-card">
        <div class="site-card-header">
            <h3 class="title">{{$fields['title']}}</h3>
        </div>
        <div class="site-card-body">
            @include('backend.setting.site_setting.include.form.__open_action')

            <div class="site-input-groups row mb-0">
                <label for="" class="col-sm-4 col-label">{{ __('Card Creation Charge') }}</label>
                <div class="col-sm-8">
                    <div class="site-input-groups">
                        <div class="input-group joint-input">
                            <input type="text" name="card_creation_charge" value="{{ oldSetting('card_creation_charge','virtual_card') }}" class="form-control">
                            <span class="input-group-text">{{ setting('site_currency','global') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        
            <div class="site-input-groups row mb-0">
                <div class="col-sm-4 col-label">{{ __('Card Limit') }}</div>
                <div class="col-sm-8">
                    <div class="form-row">
                        <div class="col-xl-6 col-sm-12 col-12">
                            <div class="site-input-groups">
                                <label for="" class="box-input-label">{{ __('Min Card Topup:') }}</label>
                                <div class="input-group joint-input">
                                    <input type="text" class="form-control" name="min_card_topup"
                                           value="{{ oldSetting('min_card_topup','virtual_card') }}">
                                    <span class="input-group-text">{{ setting('site_currency','global') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-sm-12 col-12">
                            <div class="site-input-groups">
                                <label for="" class="box-input-label">{{ __('Max Card Topup:') }}</label>
                                <div class="input-group joint-input">
                                    <input type="text" class="form-control" name="max_card_topup"
                                           value="{{ oldSetting('max_card_topup','virtual_card') }}">
                                    <span class="input-group-text">{{ setting('site_currency','global') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="site-input-groups row">
                <label for="" class="col-sm-4 col-label">{{ __('Card Topup Charge') }}</label>
                <div class="col-sm-8">
                    <div class="site-input-groups position-relative">
                        <div class="position-relative">
                            <input type="text" class="box-input" value="{{ oldSetting('card_topup_charge','virtual_card') }}"
                                   name="card_topup_charge">
                            <div class="prcntcurr">
                                <select name="card_topup_charge_type" class="form-select" id="">
                                    @foreach(['fixed' => setting('currency_symbol','default'), 'percentage' => '%'] as $key => $value)
                                        <option @if( oldSetting('card_topup_charge_type','virtual_card') == $key) selected @endif
                                        value="{{ $key }}"> {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="site-input-groups row mb-0">
                <label for="" class="col-sm-4 col-label">{{ __('Card Creation Limit (Per User)') }}</label>
                <div class="col-sm-8">
                    <div class="site-input-groups">
                        <div class="input-group joint-input">
                            <input type="text" name="card_creation_limit" value="{{ oldSetting('card_creation_limit','virtual_card') }}" class="form-control">
                            <span class="input-group-text">{{ __('Cards') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @include('backend.setting.site_setting.include.form.__close_action')
        </div>
    </div>
</div>

