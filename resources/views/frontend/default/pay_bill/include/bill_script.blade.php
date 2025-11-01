@push('js')
    <script>
        "use strict";

        $('select[name=country]').select2();

        $('select[name=country]').on('change', function() {

            let country = $(this).val();

            let url = "{{ url('user/pay-bill/get-services') }}/" + country + "/{{ request()->segment(3) }}";

            $.get(url, function(response) {
                $('#services').html(response.html);
                $('#services').select2();
            });

        });

        $('#currency').hide();

        $('#services').on('change', function() {
            let minAmount = $('#services option:selected').data('min-amount');
            let maxAmount = $('#services option:selected').data('max-amount');
            let currency = $('#services option:selected').data('currency');
            let amount = $('#services option:selected').data('amount');
            let labels = $('#services option:selected').data('label');

            labels = JSON.parse(JSON.parse(labels));

            $('#currency').show();
            $('#currency').text(currency);

            if (amount !== 0) {
                $('#amount').val(amount);
                $('#amount').attr('readonly', true);
            } else {
                $('#amount').val('');
                $('#amount').attr('readonly', false);
            }

            $('.custom-input-box').html('');

            for (let i = 0; i < labels.length; i++) {
                const label = labels[i];
                let label_text = label.replace(/_/g, ' ');
                label_text = label_text.replace(/\b\w/g, l => l.toUpperCase());
                var inputs = `<div class="inputs custom-label-box">
                                <label for="" class="input-label"><span class="label-name">${label_text}</span> <span class="required">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="data[${label}]" class="form-control" id="label-input" placeholder="${label}">
                                </div>
                            </div>`;

                $('.custom-input-box').append(inputs).removeClass('d-none');
            }

            getPaymentDetails();

        });

        $('input[name=amount]').on('keyup', function() {
            getPaymentDetails();
        })

        function getPaymentDetails() {
            let service_id = $('#services option:selected').val();
            let amount = $('input[name=amount]').val();
            let button = $('button[type=submit]');
            let amountField = $('input[name=amount]');

            $.get("{{ route('user.pay.bill.get.payment.details') }}", {
                amount: amount,
                service_id
            }, function(response) {
                $('.pay-amount').text(response.payable_amount);
                $('.charge').text(response.charge);
                $('.amount').text(response.amount);
                $('.conversion-rate').text(response.rate);
            });
        }
    </script>
@endpush
