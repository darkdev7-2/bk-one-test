@if (setting('multiple_currency', 'permission'))
    @php
        $currencies = \App\Models\Currency::get();
    @endphp

    <div class="col-xl-6 col-lg-12 col-md-12 col-12">
        <div class="site-card">
            <div class="site-card-header">
                <h3 class="title">{{ $fields['title'] }}</h3>
            </div>
            <div class="col-xl-3 p-2">
                <a href="javascript:void(0)" id="generate"
                    class="site-btn-xs primary-btn mb-3">{{ __('Add Currency') }}</a>
            </div>
            <form action="{{ route('admin.currency.update') }}" method="post">
                @csrf
                @method('PUT')
                <div class="addOptions p-3" id="currency_elements">
                    @foreach ($currencies as $key => $currency)
                        <div>
                            <div class="option-remove-row row">
                                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="site-input-groups">
                                        <input name="field_options[{{ $key }}][name]" class="box-input"
                                            type="text" value="{{ $currency->name }}" required
                                            placeholder="{{ __('Currency Name') }}">
                                        <input name="field_options[{{ $key }}][current_name]" class="box-input"
                                            type="hidden" value="{{ $currency->name }}">
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="site-input-groups">
                                        <input name="field_options[{{ $key }}][code]" class="box-input"
                                            type="text" value="{{ $currency->code }}" required
                                            placeholder="{{ __('Currency Code') }}">
                                        <input name="field_options[{{ $key }}][current_code]"
                                            class="box-input" type="hidden" value="{{ $currency->code }}">
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="site-input-groups">
                                        <input name="field_options[{{ $key }}][rate]" class="box-input"
                                            type="text" value="{{ $currency->rate }}" required
                                            placeholder="{{ __('Rate') }}">
                                        <input name="field_options[{{ $key }}][current_rate]"
                                            class="box-input" type="hidden" value="{{ $currency->rate }}">
                                    </div>
                                </div>

                                <div class="col-xl-2 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <div class="site-input-groups">
                                        <input name="field_options[{{ $key }}][symbol]" class="box-input"
                                            type="text" value="{{ $currency->symbol }}" required
                                            placeholder="{{ __('Currency Symbol') }}">
                                        <input name="field_options[{{ $key }}][current_symbol]"
                                            class="box-input" type="hidden" value="{{ $currency->symbol }}">
                                    </div>
                                </div>

                                <div class="col-xl-1 col-lg-6 col-md-6 col-sm-6 col-12">
                                    <button class="delete-option-row delete_desc" type="button"
                                        data-id="{{ $currency->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-xl-12">
                    <button type="submit" class="site-btn primary-btn w-100">
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('single-script')
        <script>
            var i = '{{ $currencies->count() }}' - 1;

            $("#generate").on('click', function() {
                ++i;
                var form = `<div>
                    <div class="option-remove-row row">
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="site-input-groups">
                            <input name="field_options[` + i + `][name]" class="box-input" type="text" value="" required placeholder="{{ __('Currency Name') }}" required>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="site-input-groups">
                            <input name="field_options[` + i + `][code]" class="box-input" type="text" value="" required placeholder="{{ __('Currency Code') }}">
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="site-input-groups">
                            <input name="field_options[` + i + `][rate]" class="box-input" type="text" value="" required placeholder="{{ __('Rate') }}">
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="site-input-groups">
                            <input name="field_options[` + i + `][symbol]" class="box-input" type="text" value="" required placeholder="{{ __('Currency Symbol') }}">
                            </div>
                        </div>

                        <div class="col-xl-1 col-lg-6 col-md-6 col-sm-6 col-12">
                            <button class="delete-option-row delete_desc" type="button">
                            <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    </div>`;
                $('.addOptions').append(form)
            });

            $(document).on('click', '.delete_desc', function(e) {
                var value = $(this).attr('data-id');

                if (value && value != 'undefined') {
                    $("#currency_elements").append('<input type="hidden" name="delete_currencies[]" value="' + value +
                        '">');
                }

                $(this).closest('.option-remove-row').parent().remove();
            });
        </script>
    @endpush
@endif
