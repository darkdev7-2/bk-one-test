@extends('backend.layouts.app')

@section('title')
    {{ __('Subscribe DPS') }}
@endsection

@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xl-8">
                        <div class="title-content">
                            <h2 class="title">{{ __('Subscribe DPS') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form action="{{ route('admin.dps.request.subscribe') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="site-card">
                                    <div class="site-card-body">
                                        <div class="row justify-content-center">
                                            <div class="col-xl-8">
                                                <div class="site-input-groups">
                                                    <label class="box-input-label">
                                                        {{ __('Select FDR Plan :') }}
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <select name="dps_plan_id" class="form-select"
                                                        onchange="getfdrPlanInfo(this)">
                                                        <option selected disabled>
                                                            {{ __('Select DPS Plan') }}
                                                        </option>
                                                        @foreach ($dpsPlans as $dpsPlan)
                                                            <option value="{{ $dpsPlan->id }}"
                                                                @if (request('dps_plan_id') == $dpsPlan->id) selected @endif>
                                                                {{ $dpsPlan->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (isset($selectDpsPlan) && $selectDpsPlan)
                                <div class="col-xl-12">
                                    <div class="site-card">
                                        <div class="site-card-header">
                                            <h3 class="title">{{ __('DPS Plan Information') }}</h3>
                                        </div>
                                        <div class="site-card-body">
                                            <div class="profile-text-data">
                                                <div class="attribute">
                                                    {{ __('DPS Plan Name:') }}
                                                </div>
                                                <div class="value">
                                                    {{ $selectDpsPlan->name }}
                                                </div>
                                            </div>
                                            <div class="profile-text-data">
                                                <div class="attribute">
                                                    {{ __('Interest Rate:') }}
                                                </div>
                                                <div class="value">
                                                    {{ $selectDpsPlan->interest_rate }}%
                                                </div>
                                            </div>
                                            <div class="profile-text-data">
                                                <div class="attribute">
                                                    {{ __('Number of Installments:') }}
                                                </div>
                                                <div class="value">
                                                    {{ $selectDpsPlan->total_installment }}
                                                </div>
                                            </div>
                                            <div class="profile-text-data">
                                                <div class="attribute">
                                                    {{ __('Per Installment:') }}
                                                </div>
                                                <div class="value">
                                                    {{ $selectDpsPlan->per_installment }} {{ $currency }}
                                                </div>
                                            </div>
                                            <div class="profile-text-data">
                                                <div class="attribute">
                                                    {{ __('Installment Slice:') }}
                                                </div>
                                                <div class="value">
                                                    {{ $selectDpsPlan->interval }} {{ __('Days') }}
                                                </div>
                                            </div>
                                            <div class="profile-text-data">
                                                <div class="attribute">
                                                    {{ __('All Deposits:') }}
                                                </div>
                                                <div class="value">
                                                    {{ $selectDpsPlan->total_deposit }} {{ $currency }}
                                                </div>
                                            </div>
                                            <div class="profile-text-data">
                                                <div class="attribute">
                                                    {{ __('Final Maturity:') }}
                                                </div>
                                                <div class="value">
                                                    {{ $selectDpsPlan->total_mature_amount }} {{ $currency }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <button type="submit" class="site-btn-sm primary-btn w-100">
                                        {{ __('Subscribe') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const userId = "{{ request('user_id') }}";

        function getfdrPlanInfo(select) {

            const dpsPlanId = select.value;

            if (dpsPlanId) {
                const newUrl =
                    `${window.location.origin}${window.location.pathname}?dps_plan_id=${dpsPlanId}&user_id=${userId}`;
                window.location.href = newUrl;
            }
        }
    </script>
@endsection
