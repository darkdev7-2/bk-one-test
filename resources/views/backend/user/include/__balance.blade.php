<div
    class="modal fade"
    id="addSubBal"
    tabindex="-1"
    aria-labelledby="addSubBalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content site-table-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="addSubBalLabel">
                    {{ __('Balance Add or Subtract') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.user.balance-update',$user->id) }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="switch-field">
                                <input
                                    type="radio"
                                    id="addMon"
                                    name="type"
                                    value="add"
                                    checked
                                />
                                <label for="addMon">{{ __('Add') }}</label>
                                <input
                                    type="radio"
                                    id="addMon2"
                                    name="type"
                                    value="subtract"
                                />
                                <label for="addMon2">{{ __('Subtract') }}</label>
                            </div>
                        </div>
                        @if (setting('multiple_currency', 'permission'))
                            <div class="col-xl-12">
                                <div class="site-input-groups mb-0">
                                    <label for="wallet" class="input-label mb-1">
                                        {{ __('Select Wallet') }}
                                        <span class="required">*</span>
                                    </label>
                                    <select class="form-select" name="wallet_type" id="wallet" required>
                                        <option selected disabled value="">
                                            {{ __('Select Wallet') }}
                                        </option>
                                        @foreach ($wallets as $wallet)
                                            <option data-curr-name="{{ $wallet['name'] }}" value="{{ $wallet['id'] }}">
                                                {{ $wallet['name'] }} ({{ $wallet['code'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="site-input-groups">
                                    <label for="wallet" class="input-label mb-1">
                                        {{ __('Amount') }}
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-group joint-input">
                                        <span class="input-group-text balance-add-sub-currency">{{ setting('site_currency','global') }}</span>
                                        <input type="text" name="amount" oninput="this.value = validateDouble(this.value)" class="form-control">
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-xl-12">
                                <div class="site-input-groups">
                                    <div class="input-group joint-input">
                                        <span class="input-group-text">{{ setting('site_currency','global') }}</span>
                                        <input type="text" name="amount" oninput="this.value = validateDouble(this.value)"
                                            class="box-input">
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-xl-12">
                            <button type="submit" class="site-btn primary-btn w-100">
                                {{ __('Apply Now') }}
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
