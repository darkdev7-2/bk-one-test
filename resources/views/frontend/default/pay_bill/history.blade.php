@extends('frontend::layouts.user')
@section('title')
    {{ __('My Bill Payments') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="site-card">
                <div class="site-card-header">
                    <div class="title">{{ __('My Bill Payments') }}</div>
                    <div class="card-header-links">
                        <a href="{{ route('user.pay.bill.airtime') }}" class="card-header-link"><i
                                data-lucide="credit-card"></i>{{ __('Pay Bill') }}</a>
                    </div>
                </div>
                <div class="site-card-body p-0">
                    <div class="site-custom-table">
                        <div class="contents">
                            <div class="site-table-list site-table-head">
                                <div class="site-table-col">{{ __('Service') }}</div>
                                <div class="site-table-col">{{ __('Amount') }}</div>
                                <div class="site-table-col">{{ __('Charge') }}</div>
                                <div class="site-table-col">{{ __('Status') }}</div>
                                <div class="site-table-col">{{ __('Action') }}</div>
                            </div>
                            @foreach ($bills as $bill)
                                <div class="site-table-list">
                                    <div class="site-table-col">
                                        <div class="description">
                                            <div class="content">
                                                <div class="title">{{ $bill->service->name }}</div>
                                                <div class="date">{{ date('d M Y h:i A', strtotime($bill->created_at)) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="site-table-col">
                                        <div class="trx fw-bold">{{ $bill->amount . ' ' . $currency }}</div>
                                    </div>
                                    <div class="site-table-col">
                                        <div class="trx fw-bold">{{ $bill->charge . ' ' . $currency }}</div>
                                    </div>
                                    <div class="site-table-col">
                                        @if ($bill->status->value == 'pending')
                                            <div class="type site-badge badge-pending">{{ $bill->status->value }}</div>
                                        @elseif($bill->status->value == 'return')
                                            <div class="type site-badge badge-failed">{{ $bill->status->value }}</div>
                                        @elseif($bill->status->value == 'completed')
                                            <div class="type site-badge badge-success">{{ $bill->status->value }}</div>
                                        @endif
                                    </div>

                                    <div class="site-table-col">
                                        <div class="action">
                                            <a href="javascript:void(0)" class="icon-btn details-btn" data-bs-toggle="modal"
                                                data-bs-target="#trxViewDetailsBox" data-title="{{ $bill->description }}"
                                                data-type="{{ $bill->service->type }}" data-time="{{ $bill->created_at }}"
                                                data-transaction-id="{{ $bill->tnx }}"
                                                data-transaction="{!! json_encode($bill->action_data) !!}"
                                                data-message="{{ $bill->action_message }}"
                                                data-amount="{{ $bill->amount . ' ' . $currency }}"
                                                data-charge="{{ $bill->charge . ' ' . $currency }}"
                                                data-method="{{ $bill->service->method !== '' ? ucfirst(str_replace('-', ' ', $bill->service->method)) : __('System') }}"
                                                class="icon-btn me-2"><i data-lucide="eye"></i>{{ __('Details') }}</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            {{ $bills->links() }}
                        </div>
                        @if (count($bills) == 0)
                            <div class="no-data-found">{{ __('No Data Found!') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Transaction View Details -->
    <div class="modal fade" id="trxViewDetailsBox" tabindex="-1" aria-labelledby="trxViewDetailsBoxModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content site-table-modal">
                <div class="modal-body popup-body">
                    <button type="button" class="modal-btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            data-lucide="x"></i></button>
                    <div class="popup-body-text">
                        <div class="title title-value"></div>
                        <div class="modal-beneficiary-details">
                            <div class="profile-text-data">
                                <div class="attribute">{{ __('Time') }}</div>
                                <div class="value time-value"></div>
                            </div>
                            <div class="profile-text-data">
                                <div class="attribute">{{ __('Amount') }}</div>
                                <div class="value green-color amount-value"></div>
                            </div>
                            <div class="profile-text-data">
                                <div class="attribute">{{ __('Charge') }}</div>
                                <div class="value red-color charge-value"></div>
                            </div>
                            <div class="profile-text-data">
                                <div class="attribute">{{ __('Method') }}</div>
                                <div class="value method-value"></div>
                            </div>
                            <div class="custom-fields"></div>

                            <div class="profile-text-data">
                                <div class="attribute message-value"></div>
                            </div>
                        </div>
                        <div class="action-btns mt-3">
                            <a href="javascript:void(0)" class="site-btn-sm polis-btn" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i data-lucide="check"></i>
                                {{ __('Close it') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for Transaction View Details end-->
@endsection

@push('js')
    <script>
        // Set data for modal

        // on trxViewDetailsBox


        $(document).on('click', '.details-btn', function(e) {

            e.preventDefault();

            var id = $(this).data('id');
            var title = $(this).data('title');
            var type = $(this).data('type');
            var time = $(this).data('time');
            var trx = $(this).data('transaction-id');
            var amount = $(this).data('amount');
            var charge = $(this).data('charge');
            var method = $(this).data('method');
            var transaction = $(this).data('transaction');

            console.log(transaction, status);

            var statusElement = '';
            var additionalData = '';

            $.each(transaction, function(key, value) {
                additionalData += '<div class="profile-text-data"><div class="attribute">' +
                    capitalizeFirstLetter(key.replaceAll('_', ' ')) + '</div><div class="value">' + value +
                    '</div></div>';
            });

            $('.title-value').text("{{ __('Bill Details') }}");
            $('.trx-value').text(trx);
            $('.time-value').text(time);
            $('.amount-value').text(amount);
            $('.charge-value').text(charge);
            $('.method-value').text(method);
            $('.status-value').html(statusElement);

            $('.custom-fields').html(additionalData);

        });
    </script>
@endpush
