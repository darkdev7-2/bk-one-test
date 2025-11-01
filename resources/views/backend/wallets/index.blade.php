@extends('backend.layouts.app')
@section('title')
    {{ __('Wallets') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('All Wallets') }}</h2>
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
                                @include('backend.filter.th',['label' => 'Currency','field' => 'name'])
                                @include('backend.filter.th',['label' => 'Currency Symbol','field' => 'symbol'])
                                @include('backend.filter.th',['label' => 'Balance','field' => 'balance'])
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($wallets as $wallet)
                                <tr>
                                    <td>
                                        {{ $wallet->created_at }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.user.edit',$wallet->user_id) }}" class="link">{{ Str::limit($wallet->user?->username,15) }}</a>
                                    </td>
                                    <td>
                                        {{ $wallet->currency?->name }}
                                    </td>
                                    <td>
                                        {{ $wallet->currency?->symbol }}
                                    </td>
                                    <td>
                                        {{ $wallet->currency?->symbol.$wallet->balance }}
                                    </td>
                                </tr>
                            @empty
                            <td colspan="7" class="text-center">{{ __('No Wallets Found!') }}</td>
                            @endforelse
                            </tbody>
                        </table>

                        {{ $wallets->links('backend.include.__pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

