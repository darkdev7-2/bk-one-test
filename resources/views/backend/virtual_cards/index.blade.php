@extends('backend.layouts.app')
@section('title')
    {{ __('Virtual Cards') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('Virtual Cards') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div class="site-table table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    @include('backend.filter.th',['label' => 'Date','field' => 'created_at'])
                                    @include('backend.filter.th',['label' => 'User','field' => 'user'])
                                    @include('backend.filter.th',['label' => 'Card No.','field' => 'card_number'])
                                    @include('backend.filter.th',['label' => 'Expiry','field' => 'expiration_year'])
                                    @include('backend.filter.th',['label' => 'Balance','field' => 'balance'])
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($cards as $card)
                                <tr>
                                    <td>
                                        {{ $card->created_at }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.user.edit',$card->user_id) }}" class="link">{{ Str::limit($card->user?->username,15) }}</a>
                                    </td>
                                    <td>
                                        {{ $card->card_number }}
                                    </td>
                                    <td>
                                        {{ $card->expiration_month.'/'.$card->expiration_year }}
                                    </td>
                                    <td>
                                        {{ $currencySymbol.$card->amount }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @can('virtual-card-topup')
                                            <a href="#" class="round-icon-btn primary-btn" data-bs-toggle="modal" data-bs-target="#topUpCard_{{ $card->id }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Top Up Card') }}">
                                                <i data-lucide="plus-circle"></i>
                                            </a>
                                            @endcan

                                            @can('virtual-card-status-change')
                                            <a href="{{ route('admin.user.card.status.update', $card->card_id) }}" class="round-icon-btn {{ $card?->status == 'active' ? 'red':'green' }}-btn" data-bs-toggle="tooltip" data-bs-original-title="{!! $card->status == 'active' ? __('Deactivate') : __('Activate') !!}">
                                                {!! $card->status == 'active' ? '<i data-lucide="shield-off"></i>'.__('Deactivate') : '<i data-lucide="shield-check"></i>'.__('Activate') !!}
                                            </a>
                                            @endcan
                                        </div>
                                        @can('virtual-card-topup')
                                        <div class="modal fade" id="topUpCard_{{ $card->id }}" tabindex="-1" aria-labelledby="addSubBalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-md modal-dialog-centered">
                                                <div class="modal-content site-table-modal">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="addSubBalLabel">
                                                            {{ __('Card Balance Add or Subtract') }}
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
                                                                        <div class="input-group joint-input">
                                                                            <span class="input-group-text">{{ setting('site_currency','global') }}</span>
                                                                            <input type="text" name="amount" oninput="this.value = validateDouble(this.value)"
                                                                                class="form-control">
                                                                        </div>
                                                                    </div>
                                                                </div>
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
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                            <td colspan="7" class="text-center">{{ __('No Cards Found!') }}</td>
                            @endforelse
                            </tbody>
                        </table>

                        {{ $cards->links('backend.include.__pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

