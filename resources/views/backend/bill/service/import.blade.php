@extends('backend.layouts.app')
@section('title')
    {{ __('Import Services') }}
@endsection
@section('content')
    <div class="main-content">
        <div class="page-title">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="title-content">
                            <h2 class="title">{{ __('Import Services') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div class="site-card">
                        <div class="site-card-body">
                            <form action="" method="get">
                                <div class="row">
                                    <div class="col-md-3 col-xl-3">
                                        <div class="site-input-groups">
                                            <label class="box-input-label" for="">{{ __('Method:') }}</label>
                                            <select name="method" class="form-select">
                                                <option value="" selected disabled>{{ __('Select Method') }}</option>
                                                @foreach ($methods as $method)
                                                    <option value="{{ strtolower($method->name) }}"
                                                        @selected(request('method') == strtolower($method->name))>
                                                        {{ $method->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xl-3">
                                        <div class="site-input-groups">
                                            <label class="box-input-label" for="">{{ __('Categories:') }}</label>
                                            <select name="category" class="form-select">
                                                <option value="" selected disabled>{{ __('Select Category') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xl-3" id="operator-area">
                                        <div class="site-input-groups">
                                            <label class="box-input-label" for="">{{ __('Operators:') }}</label>
                                            <select name="operator" class="form-select">
                                                <option value="" selected disabled>{{ __('Select Operator') }}
                                                </option>
                                                @isset($operators)
                                                    @foreach ($operators as $operator)
                                                        <option value="{{ $operator['id'] }}" @selected(request('operator') == $operator['id'])>
                                                            {{ $operator['name'] }}
                                                        </option>
                                                    @endforeach
                                                @endisset
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xl-3">
                                        <button type="submit" name="type" value="get_service"
                                            class="site-btn-sm primary-btn mt-4">
                                            <i data-lucide="search"></i>
                                            {{ __('Search Service') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @isset($services)
                    <div class="col-xl-12">
                        <form action="{{ route('admin.bill.service.bulk.store') }}" method="post">
                            @csrf
                            <input type="hidden" name="method" value="{{ request('method') }}">
                            <input type="hidden" name="api_id" value="{{ $method->id }}">
                            <input type="hidden" name="category" value="{{ request('category') }}">
                            <input type="hidden" name="operator" value="{{ request('operator') }}">
                            <div class="site-card">
                                <div class="site-card-body">
                                    <button type="submit" class="site-btn-sm primary-btn mb-2">
                                        <i data-lucide="plus-circle"></i> {{ __('Bulk Insert') }}
                                    </button>
                                    <div class="site-table table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">
                                                        <input type="checkbox" id="all-checked" class="form-check-input">
                                                    </th>
                                                    <th scope="col">{{ __('Name') }}</th>
                                                    <th scope="col">{{ __('Code') }}</th>
                                                    <th scope="col">{{ __('Country') }}</th>
                                                    <th scope="col">{{ __('Amount') }}</th>
                                                    <th scope="col">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($services as $service)
                                                    @include('backend.bill.service.include.' . request('method'))
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endisset
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        "use strict";

        $('body').on('click', '#edit', function(event) {

            event.preventDefault();
            $('#edit-staff-body').empty();
            var id = $(this).data('id');

            $.get('edit/' + id, function(data) {

                $('#editModal').modal('show');
                $('#edit-staff-body').append(data);

            })
        });

        $('#all-checked').on('click', function() {
            $('input[id=check-row]').prop('checked', this.checked);
        });

        function operatorToggle(method) {
            if (method == 'bloc' || method == 'flutterwave') {
                $('#operator-area').show();
            } else {
                $('#operator-area').hide();
            }
        }

        operatorToggle('{{ request('method') }}');

        $('select[name=method]').on('change', function() {

            var method = $(this).val();

            operatorToggle(method);

            getCategories(method);

        });
        $('select[name=category]').on('change', function() {

            categoryChanged();

        });

        function categoryChanged() {
            var category = $('select[name=category]').val();
            var method = $('select[name=method]').val();
            getOperators(category, method);
        }


        function getCategories(method) {

            $.get('{{ url('admin/bill/get-categories') }}/' + method, function(data) {

                $('select[name=category]').html(data);

                $('select[name=category]').val('{{ request('category') }}')

            });
        }


        function getOperators(category, method) {
            var data_attr = $('select[name=category] option:selected').data();

            $.get('{{ route('admin.bill.get.operators', ['method', 'category', 'data' => 'data_attr']) }}'
                .replaceAll('category', category)
                .replaceAll('method', method).replaceAll('data_attr', JSON.stringify(data_attr)),
                function(data) {
                    $('select[name=operator]').html(data);

                    $('[name="operator"]').val('{{ request('operator') }}')
                });
        }

        @if (request('type') == 'get_service')
            $('select[name=method]').trigger('change');
            @if (request('method') == 'flutterwave')

                var intervalTimer = setInterval(function() {
                    if ($('select[name=category] option').length > 0) {
                        setTimeout(function() {
                            categoryChanged();
                            clearInterval(intervalTimer);

                        }, 1500)
                        clearInterval(intervalTimer);
                    }
                }, 1000)
            @else
                getCategories('{{ request('method') }}');
            @endif
        @endif

        $(document).on('click', '.addServiceBtn', function() {
            let method = "{{ request('method') }}";
            let category = "{{ request('category') }}";
            let provider_code = "{{ $method->id }}";
            let data = $(this).data('info');
            let element = $(this);
            let loader =
                '<div class="text-center"><img src="{{ asset('front/images/loader.gif') }}" width="100"><h5>{{ __('Please wait') }}...</h5></div>';

            element.removeAttr('id');
            element.html(loader);

            $.ajax({
                url: "{{ route('admin.bill.service.store') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    method: method,
                    category: category,
                    data: data,
                    provider_code: provider_code
                },
                method: "POST",
                success: function(response) {
                    if (response.success) {
                        element.parent().find('#addedService').removeClass('d-none');

                        element.remove();

                        tNotify('success', response.message, 'Success');
                    } else {
                        element.attr('id', 'addService');
                        element.html('<i data-lucide="plus-circle"></i>');
                        lucide.createIcons();
                        tNotify('warning', response.message, 'Error');
                    }
                },
                error: function(error) {
                    element.attr('id', 'addService');
                    element.html('<i data-lucide="plus-circle"></i>');
                    lucide.createIcons();
                    tNotify('error', "{{ __('Sorry, Something went wrong!') }}", 'Error');
                }
            });

        });
    </script>
@endsection
