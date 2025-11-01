@extends('frontend::layouts.user')
@section('title')
    {{ __('All Wallets') }}
@endsection
@section('content')
    <div class="site-card-header d-flex justify-content-between">
        <div class="title-small"></div>

        <a href="javascript:void(0)" class="site-btn-sm primary-btn mb-4" data-bs-toggle="modal"
            data-bs-target="#createWalletModal">
            <i data-lucide="plus-circle"></i>
            {{ __('Create New Wallet') }}
        </a>
    </div>
    <div class="row">
        <div class="col-xl-4 col-lg-12 col-md-12 col-12">
            <div class="user-profile-card">
                <h4 class="title">{{ __('Default Wallet') }}</h4>

                <h3 class="acc-balance" id="passo">
                    {{ setting('currency_symbol','global').number_format($user->balance,2) }}
                </h3>

                <div class="buttons mt-120">
                    <a href="{{ route('user.deposit.amount') }}" class="send me-2"><i data-lucide="plus-circle"></i>{{ __('Add Money') }}</a>
                    <a href="{{ route('user.fund_transfer.index') }}" class="add"><i data-lucide="send"></i>{{ __('Send Money') }}</a>
                </div>
                <div class="o">O</div>
            </div>
        </div>
        @foreach ($user_wallets as $wallet)
            <div class="col-xl-4 col-lg-12 col-md-12 col-12">
                <div class="user-profile-card position-relative">
                    <h4 class="title">
                        {{ $wallet?->currency?->name }} ({{ $wallet?->currency?->code }})
                    </h4>

                    <h3 class="acc-balance" id="passo">
                        {{ $wallet?->currency?->symbol.number_format($wallet->balance,2) }}
                    </h3>

                    <div class="buttons mt-120">
                        <a href="{{ route('user.deposit.amount', $wallet?->currency?->code) }}" class="send me-2"><i data-lucide="plus-circle"></i>{{ __('Add Money') }}</a>
                        <a href="{{ route('user.fund_transfer.index', $wallet?->currency?->code) }}" class="add"><i data-lucide="send"></i>{{ __('Send Money') }}</a>
                    </div>
                    <div class="o">O</div>

                    <a href="javascriptvoid(0)" type="button" class="card-delete text-danger" data-bs-toggle="modal"
                    data-bs-target="#deleteWallet_{{ $wallet->id }}">
                        <i data-lucide="trash"></i>
                    </a>
                </div>
            </div>

            <div class="modal fade" id="deleteWallet_{{ $wallet->id }}" tabindex="-1" aria-labelledby="openTicketModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content site-table-modal">
                        <div class="modal-body popup-body"> <button type="button" class="modal-btn-close" data-bs-dismiss="modal" aria-label="Close"> <i data-lucide="x"></i> </button>
                            <div class="popup-body-text">
                                <div class="title">{{ __('Delete Wallet') }}</div>

                                <form action="{{ route('user.wallets.destroy', $wallet->id) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('DELETE')
                                    <div class="step-details-form">
                                        <div class="row">
                                            <div class="col-xl-12 col-lg-12 col-md-12">
                                                <h5>
                                                    {{ __('Are you sure you want to delete this wallet?') }}
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="action-btns mt-3">
                                        <button type="submit" class="site-btn-sm red-btn me-2">
                                            <i data-lucide="check"></i> {{ __('Yes, delete now') }}
                                        </button>

                                        <button type="button" class="site-btn-sm primary-btn" data-bs-dismiss="modal" aria-label="Close">
                                            <i data-lucide="x"></i> {{ __('Close') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="col-xl-4 col-lg-12 col-md-12 col-12">
            <div class="user-profile-card">
                <div class="buttons">
                    <a href="javascript:void(0)" class="send" data-bs-toggle="modal"
                        data-bs-target="#createWalletModal">
                        <i data-lucide="plus-circle"></i>
                        {{ __('Create New Wallet') }}
                    </a>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="createWalletModal" tabindex="-1" aria-labelledby="openTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content site-table-modal">
                <div class="modal-body popup-body"> <button type="button" class="modal-btn-close" data-bs-dismiss="modal" aria-label="Close"> <i data-lucide="x"></i> </button>
                    <div class="popup-body-text">
                        <div class="title">{{ __('Create New Wallet') }}</div>

                        <form action="{{ route('user.wallets.store') }}" method="post">
                            @csrf
                            <div class="step-details-form">
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                        <div class="inputs">
                                            <label for="" class="input-label">{{ __('Wallet') }}<span class="required">*</span></label>
                                            <select class="add-priority box-input page-count" name="currency" >
                                                <option selected disabled value="">{{ __('Select Wallet') }}</option>
                                                @foreach ($currencies as $currency)
                                                    <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                                @endforeach
                                            </select>
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


@endsection
@section('script')
    <script>

        $('#copy').on('click',function(){
            copyRef();
        });

        function copyRef() {
            /* Get the text field */
            var textToCopy = $('#refLink').val();
            // Create a temporary input element
            var tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(textToCopy).select();
            // Copy the text from the temporary input
            document.execCommand('copy');
            // Remove the temporary input element
            tempInput.remove();

            // Set tooltip as copied
            var tooltip = bootstrap.Tooltip.getInstance('#copy');
            tooltip.setContent({ '.tooltip-inner': 'Copied' });

            setTimeout(() => {
                tooltip.setContent({ '.tooltip-inner': 'Copy' });
            }, 4000);
        }

    </script>
@endsection

@push('style')
    <style>
        .user-profile-card .card-delete {
            position: absolute;
            top: 20px;
            right: 20px;
            cursor: pointer;
        }
    </style>
@endpush
