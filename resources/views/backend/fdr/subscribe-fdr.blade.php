@extends('backend.layouts.app')

@section('title')
    {{ __('Subscribe FDR') }}
@endsection

@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="title-content">
                            <h2 class="title">{{ __('Subscribe FDR') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <form action="{{ route('admin.fdr.request.subscribe') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" value="{{ request('user_id') }}">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="site-card">
                            <div class="site-card-body">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="site-input-groups">
                                            <label class="box-input-label">
                                                {{ __('Select FDR Plan :') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select name="fdr_plan_id" class="form-select" onchange="getfdrPlanInfo(this)">
                                                <option selected disabled>
                                                    {{ __('Select FDR Plan') }}
                                                </option>
                                                @foreach ($fdrPlans as $fdrPlan)
                                                    <option value="{{ $fdrPlan->id }}"
                                                        @if (request('fdr_plan_id') == $fdrPlan->id) selected @endif>
                                                        {{ $fdrPlan->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="col-xl-12">
                                            <div class="site-input-groups">
                                                <label class="box-input-label" for="">
                                                    {{ __('Amount:') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group joint-input">
                                                    <input type="text" name="fdr_amount" id="fdrAmount" step="0.01"
                                                        class="form-control">
                                                    <span class="input-group-text">
                                                        {{ setting('site_currency', 'global') }}
                                                    </span>
                                                </div>
                                                <div class="text-danger min-max"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (isset($selectFdrPlan) && $selectFdrPlan)
                        <div class="col-xl-6">
                            <div class="site-card">
                                <div class="site-card-header">
                                    <h3 class="title">{{ __('FDR Plan Information') }}</h3>
                                </div>
                                <div class="site-card-body">
                                    <div class="profile-text-data">
                                        <div class="attribute">
                                            {{ __('FDR Plan Name:') }}
                                        </div>
                                        <div class="value">
                                            {{ $selectFdrPlan->name }}
                                        </div>
                                    </div>
                                    <div class="profile-text-data">
                                        <div class="attribute">
                                            {{ __('Lock In Period:') }}
                                        </div>
                                        <div class="value">
                                            {{ $selectFdrPlan->locked }} {{ __('Days') }}
                                        </div>
                                    </div>
                                    <div class="profile-text-data">
                                        <div class="attribute">
                                            {{ __('Get Profit Every:') }}
                                        </div>
                                        <div class="value">
                                            {{ $selectFdrPlan->intervel }} {{ __('Days') }}
                                        </div>
                                    </div>
                                    <div class="profile-text-data">
                                        <div class="attribute">
                                            {{ __('Profit Rate:') }}
                                        </div>
                                        <div class="value">
                                            {{ $selectFdrPlan->interest_rate }}{{ __('%') }}
                                        </div>
                                    </div>
                                    <div class="profile-text-data">
                                        <div class="attribute">
                                            {{ __('Minimum FDR:') }}
                                        </div>
                                        <div class="value">
                                            {{ $selectFdrPlan->minimum_amount }} {{ $currency }}
                                        </div>
                                    </div>
                                    <div class="profile-text-data">
                                        <div class="attribute">
                                            {{ __('Maximum FDR:') }}
                                        </div>
                                        <div class="value">
                                            {{ $selectFdrPlan->maximum_amount }} {{ $currency }}
                                        </div>
                                    </div>
                                    <div class="profile-text-data">
                                        <div class="attribute">
                                            {{ __('Compounding:') }}
                                        </div>
                                        <div class="value">
                                            {{ $selectFdrPlan->is_compounding ? 'Yes' : 'No' }}
                                        </div>
                                    </div>
                                    <div class="profile-text-data">
                                        <div class="attribute">
                                            {{ __('Cancel In:') }}
                                        </div>
                                        <div class="value">
                                            {{ $selectFdrPlan->cancel_type == 'anytime' ? 'Anytime' : $selectFdrPlan->cancel_days . ' Days' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <button type="submit" class="site-btn-sm primary-btn w-100">
                               {{ __('Subscribe FDR') }}
                            </button>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const userId = "{{ request('user_id') }}";

        function getfdrPlanInfo(select) {

            const fdrPlanId = select.value;

            if (fdrPlanId) {
                const newUrl =
                    `${window.location.origin}${window.location.pathname}?fdr_plan_id=${fdrPlanId}&user_id=${userId}`;
                window.location.href = newUrl;
            }
        }
    </script>
    @if (isset($selectFdrPlan) && $selectFdrPlan)
        <script>
            const currency = @json($currency);

            const min = "{{ $selectFdrPlan->minimum_amount }}";
            const max = "{{ $selectFdrPlan->maximum_amount }}";

            var message = `Minimum ${min} ${currency} and Maximum ${max} ${currency}`;

            $('.min-max').text(message);

            $('#fdrAmount').on('change', function(event) {

                var fdrAmount = parseFloat($("#fdrAmount").val());

                if (fdrAmount < min) {
                    fdrAmount = min;
                    $("#fdrAmount").val(fdrAmount);
                } else if (fdrAmount > max) {
                    fdrAmount = max;
                    $("#fdrAmount").val(fdrAmount);
                }

            });
        </script>
    @endif
@endsection
