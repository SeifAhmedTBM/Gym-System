@extends('layouts.admin')
@section('content')
    <h5 class="rounded text-white font-weight-bold p-3 mb-3">
        {{ trans('cruds.membership.title_singular') }} - {{ $membership->service_pricelist->service->name }}
        ({{ $membership->service_pricelist->amount }})
        ( <span class="text-primary">{{ $membership->member->name }}</span> )
    </h5>
    <form method="POST" action="{{ route('admin.membership.storeUpgrade', $membership->id) }}" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        <div class="card">
            <div class="card-header">
                <h5>{{ trans('cruds.membership.fields.upgrade') }} {{ trans('cruds.membership.title_singular') }}</h5>
            </div>
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-6">
                        <label class="required" for="member_id">{{ trans('cruds.membership.fields.member') }}</label>
                        <select class="form-control {{ $errors->has('member') ? 'is-invalid' : '' }}" name="member_id"
                            id="member_id" required readonly>
                            <option value="{{ $membership->member_id }}"
                                {{ old('member_id') == $membership->member_id ? 'selected' : '' }}>
                                {{ $membership->member->memberPrefix() . $membership->member->member_code . ' - ' . $membership->member->name }}
                            </option>
                        </select>
                        @if ($errors->has('member'))
                            <div class="invalid-feedback">
                                {{ $errors->first('member') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.membership.fields.member_helper') }}</span>
                    </div>

                    @if (config('domains')[config('app.url')]['sales_by'] == true ? 'required' : '')
                        <div class="col-md-6">
                            <label class="required" for="sales_by_id">{{ trans('cruds.member.fields.sales_by') }}</label>
                            <select class="form-control select2 {{ $errors->has('sales_by') ? 'is-invalid' : '' }}"
                                name="sales_by_id" id="sales_by_id" required>
                                <option disabled selected hidden>{{ trans('global.pleaseSelect') }}</option>
                                @foreach ($sales_bies as $id => $entry)
                                    <option value="{{ $id }}"
                                        {{ $membership->sales_by_id == $id ? 'selected' : '' }}>
                                        {{ $entry }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('sales_by'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('sales_by') }}
                                </div>
                            @endif
                            <span class="help-block">{{ trans('cruds.member.fields.sales_by_helper') }}</span>
                        </div>
                    @else
                        <input type="hidden" name="sales_by_id" value="{{ $membership->sales_by_id }}">
                    @endif
                </div>
                {{-- <div class="row form-group">
                    <div class="col-md-12">
                        <label for="notes">{{ trans('cruds.member.fields.notes') }}</label>
                        <textarea class="form-control {{ $errors->has('notes') ? 'is-invalid' : '' }}" name="notes"
                            id="notes">{{ old('notes') }}</textarea>
                        @if ($errors->has('notes'))
                            <div class="invalid-feedback">
                                {{ $errors->first('notes') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.notes_helper') }}</span>
                    </div>
                </div> --}}
            </div>
        </div>


        <div class="card">
            <div class="card-header">
                ENTER DETAILS OF THE SUBSCRIPTION
            </div>
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-3">
                        <label for="service_type" class="required">{{ trans('cruds.service.fields.service_type') }}</label>
                        <select name="service_type_id" id="service_type_id" class="form-control select2"
                            onchange="getPricelists()">
                            <option value="{{ null }}" selected disabled>{{ trans('global.pleaseSelect') }}
                            </option>
                            @foreach (\App\Models\ServiceType::pluck('name', 'id') as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="required" for="service_pricelist_id">Price list</label>
                        <select onchange="getExpiry()"
                            class="form-control select2 {{ $errors->has('service_pricelist_id') ? 'is-invalid' : '' }}"
                            name="service_pricelist_id" id="service_pricelist_id" required>
                            <option disabled selected hidden>Select Service</option>
                            {{-- @foreach ($pricelists as $pricelist)
                        <option value="{{ $pricelist->id }}" {{ old('service_pricelist_id') == $pricelist->id ? 'selected' : '' }}>
                            {{ $pricelist->name }}  </option>
                    @endforeach --}}
                        </select>
                        @if ($errors->has('service_pricelist_id'))
                            <div class="invalid-feedback">
                                {{ $errors->first('service_pricelist_id') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.service_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required" for="start_date">{{ trans('cruds.membership.fields.start_date') }}</label>
                        <input class="form-control date {{ $errors->has('start_date') ? 'is-invalid' : '' }}"
                            type="text" name="start_date" id="start_date" onblur="getExpiry()"
                            value="{{ $membership->start_date }}" readonly required>
                        @if ($errors->has('start_date'))
                            <div class="invalid-feedback">
                                {{ $errors->first('start_date') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.membership.fields.start_date_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required" for="end_date">{{ trans('cruds.membership.fields.end_date') }}</label>
                        <input class="form-control date {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="text"
                            name="end_date" id="end_date" value="" required
                            @cannot('editable_end_date') 
                     readonly
                    @endcannot>
                        @if ($errors->has('end_date'))
                            <div class="invalid-feedback">
                                {{ $errors->first('end_date') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.membership.fields.end_date_helper') }}</span>
                    </div>
                </div>

                <div class="row form-group">
                    <div class="col-md-4 >
                <label class="required" for="trainer_id">
                        {{ trans('cruds.member.fields.trainer') }}</label>
                        <select class="form-control  {{ $errors->has('trainer_id') ? 'is-invalid' : '' }}"
                            name="trainer_id" id="trainer_id" required>
                            <option disabled selected hidden>{{ trans('global.pleaseSelect') }}</option>
                            @foreach ($trainers as $id => $entry)
                                <option value="{{ $id }}" {{ old('trainer_id') == $id ? 'selected' : '' }}>
                                    {{ $entry }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('trainer_id'))
                            <div class="invalid-feedback">
                                {{ $errors->first('trainer_id') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.trainer_helper') }}</span>
                    </div>

                    @if (config('domains')[config('app.url')]['sports_option'] == true)
                        <div class="col-md-4 form-group">
                            <label for="sport_id" class="required">{{ trans('global.sport') }}</label>
                            <select name="sport_id" id="sport_id" class="select2">
                                <option disabled hidden selected>{{ trans('global.pleaseSelect') }}</option>
                                @foreach ($sports as $sport_id => $sport_name)
                                    <option value="{{ $sport_id }}">{{ $sport_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if (config('domains')[config('app.url')]['add_to_class_in_invoice'] == true)
                        <div class="col-md-4 form-group">
                            <label for="main_schedule_id">Session</label>
                            <select name="main_schedule_id[]" id="main_schedule_id" class="select2 multiple"
                                multiple="multiple">
                                {{-- <option disabled hidden selected value="{{ NULL }}">{{ trans('global.pleaseSelect') }}</option> --}}
                                @foreach ($main_schedules as $main_schedule)
                                    <option value="{{ $main_schedule->id }}">
                                        {{ ($main_schedule->session->name ?? '') . ' - ' . ($main_schedule->trainer->name ?? '') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <label class="" for="subscription_notes">Subscription Notes</label>
                        <input class="form-control" id="subscription_notes" name="subscription_notes"
                            value="{{ $membership->notes }}" />
                        @if ($errors->has('subscription_notes'))
                            <div class="invalid-feedback">
                                {{ $errors->first('subscription_notes') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.member.fields.trainer_helper') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                ENTER DETAILS OF THE INVOICE
            </div>
            <div class="card-body">
                @cannot('free_discount')
                    @if ($setting->max_discount != 100)
                        <div class="alert alert-warning text-center">
                            <strong><i class="fa fa-exclamation-circle"></i> {{ trans('global.max_discount') }} :
                                {{ $setting->max_discount }} %</strong>
                        </div>
                    @endif
                @endcannot
                <div class="row form-group">
                    <div class="col-md-3">
                        <label class="required" for="invoice_id">{{ trans('cruds.payment.fields.invoice') }}</label>
                        <input type="text" class="form-control" name="invoice_id"
                            value="{{ $membership->invoice->id }}" readonly>
                        @if ($errors->has('invoice_id'))
                            <div class="invalid-feedback">
                                {{ $errors->first('invoice_id') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.payment.fields.invoice_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required"
                            for="membership_fee">{{ trans('cruds.membership.fields.membership_fee') }}</label>
                        <input class="form-control {{ $errors->has('membership_fee') ? 'is-invalid' : '' }}"
                            type="text" name="membership_fee" id="membership_fee"
                            value="{{ old('membership_fee') }}" required readonly>
                        @if ($errors->has('membership_fee'))
                            <div class="invalid-feedback">
                                {{ $errors->first('membership_fee') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.membership.fields.membership_fee_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required" for="discount">{{ trans('cruds.invoice.fields.discount') }}</label>
                        <div class="input-group">
                            <input class="form-control {{ $errors->has('discount') ? 'is-invalid' : '' }}" type="number"
                                name="discount" id="discount" value="{{ old('discount') ?? 0 }}" required
                                step="0.001">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        @if ($errors->has('discount'))
                            <div class="invalid-feedback">
                                {{ $errors->first('discount') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.discount_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required" for="discount_amount">{{ trans('global.discount_amount') }}</label>
                        <div class="input-group">
                            <input class="form-control {{ $errors->has('discount_amount') ? 'is-invalid' : '' }}"
                                type="number" name="discount_amount" id="discount_amount"
                                value="{{ old('discount_amount') ?? 0 }}" required step="0.001">
                            <div class="input-group-append">
                                <span class="input-group-text">EGP</span>
                            </div>
                        </div>
                        @if ($errors->has('discount_amount'))
                            <div class="invalid-feedback">
                                {{ $errors->first('discount_amount') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="discount_notes">{{ trans('cruds.invoice.fields.discount_notes') }}</label>
                        <input type="text" name="discount_notes" id="discount_notes" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="created_at">{{ trans('global.date') }}</label>
                        <input type="date" class="form-control" name="created_at" value="{{ date('Y-m-d') }}"
                            @cannot('invoice_change_date') readonly  @endcannot>
                    </div>
                </div>
            </div>
        </div>
        {{-- payments details --}}
        <div class="card">
            <div class="card-header">
                ENTER DETAILS OF THE PAYMENT
            </div>
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-3">
                        <label class="required" for="net_amount">{{ trans('cruds.invoice.fields.net_amount') }}</label>
                        <input type="text" class="form-control" name="net_amount" id="net_amount"
                            value="{{ $invoice->net_amount }}" readonly>
                        @if ($errors->has('net_amount'))
                            <div class="invalid-feedback">
                                {{ $errors->first('net_amount') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.net_amount_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required"
                            for="received_amount">{{ trans('cruds.invoice.fields.received_amount') }}</label>
                        <input type="text" class="form-control" name="received_amount" id="received_amount"
                            value="{{ $invoice->payments_sum_amount }}" readonly>
                        @if ($errors->has('received_amount'))
                            <div class="invalid-feedback">
                                {{ $errors->first('received_amount') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.received_amount_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required"
                            for="amount_pending">{{ trans('cruds.payment.fields.amount_pending') }}</label>
                        <input class="form-control {{ $errors->has('amount_pending') ? 'is-invalid' : '' }}"
                            type="number" name="amount_pending" id="amount_pending"
                            value="{{ old('amount_pending') ?? 0 }}" value="" required readonly>
                        @if ($errors->has('amount_pending'))
                            <div class="invalid-feedback">
                                {{ $errors->first('amount_pending') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.payment.fields.amount_pending_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required">{{ trans('cruds.member.fields.payment_method') }}</label>
                        <select class="form-control select2 {{ $errors->has('account_id') ? 'is-invalid' : '' }}"
                            id="account_id" multiple="multiple" onchange="getAccounts()" required>
                            @foreach ($accounts as $id => $name)
                                <option value="{{ str_replace(' ', '', $name) }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('account_id'))
                            <div class="invalid-feedback">
                                {{ $errors->first('account_id') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.account_helper') }}</span>
                    </div>
                </div>

                <div class="row form-group">
                    @foreach ($accounts as $id => $name)
                        <input type="hidden" name="account_ids[]" value="{{ $id }}">
                        <div class="col-md-3">
                            <label class="required" for="{{ $name }}">{{ $name }}</label>
                            <input class="form-control accounts {{ $errors->has('cash_amount') ? 'is-invalid' : '' }}"
                                type="number" name="account_amount[]" id="{{ str_replace(' ', '', $name) }}"
                                value="{{ old($name) ?? 0 }}" required readonly>
                            <span class="help-block">{{ trans('cruds.payment.fields.cash_amount_helper') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- reminders --}}
        @include('partials.invoice_reminder')

        <div class="card-footer">
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </div>
    </form>



@endsection

@section('scripts')
    <script>
        function getPricelists() {
            var service_type_id = $('#service_type_id').val();
            var pricelist_amount = '{{ $membership->service_pricelist->amount }}';

            var url = "{{ route('admin.getPricelistsByServiceType', ':id') }}",

                url = url.replace(':id', service_type_id);
            $.ajax({
                method: 'GET',
                url: url,
                success: function(response) {
                    $('#service_pricelist_id').empty();
                    $('#service_pricelist_id').append(`<option>{{ trans('global.pleaseSelect') }}</option>`);
                    response.pricelists.forEach(resp => {
                        if (parseFloat(pricelist_amount) < parseFloat(resp.amount)) {
                            $("#service_pricelist_id").append(
                                `<option value='${resp.id}'>${resp.name} (${resp.amount})</option>`);
                        }
                    })
                }
            });
        }

        function getExpiry() {
            var max_discount = "{{ $setting->max_discount }}";
            var service_pricelist_id = $("#service_pricelist_id").val();
            var url = "{{ route('admin.getServiceByPricelist', [':id', ':date']) }}",
                url = url.replace(':id', service_pricelist_id);
            url = url.replace(':date', $('#start_date').val());
            $.ajax({
                method: 'GET',
                url: url,
                success: function(response) {
                    if (response.pricelist.service.trainer == 0) {
                        $('#trainer_id').attr('disabled', 'disabled');
                        $('#trainer_id').val('');
                    } else {
                        $('#trainer_id').removeAttr('disabled');
                        $('#trainer_id').select2();
                    }
                    $("#end_date").val(response.expiry)
                    $('#membership_fee').val(response.pricelist.amount);
                    $('#net_amount').val($('#membership_fee').val());

                    $('#discount').on('keyup', function() {
                        if (parseFloat($('#discount').val()) <= max_discount) {
                            $('#discount').removeClass('is-invalid').addClass('is-valid');

                            $('#discount_amount').val($('#membership_fee').val() * $('#discount')
                            .val() / 100);

                            $('#net_amount').val($('#membership_fee').val() - $('#discount_amount')
                            .val());

                            $('#amount_pending').val($('#membership_fee').val() - $('#discount_amount')
                                .val());

                            $('#amount_pending').val($('#net_amount').val() - $('#received_amount')
                            .val());

                        } else {
                            $('#discount').removeClass('is-valid').addClass('is-invalid');
                            $('#discount').val(0);
                            $('#discount_amount').val(0);
                        }
                    })

                    $('#discount_amount').on('keyup', function() {
                        if (parseFloat($('#discount_amount').val()) <= parseFloat($('#membership_fee')
                                .val())) {
                            $('#discount_amount').removeClass('is-invalid').addClass('is-valid');

                            $('#discount').val(Math.round($('#discount_amount').val() / $(
                                '#membership_fee').val() * 100));

                            $('#net_amount').val($('#membership_fee').val() - $('#discount_amount')
                            .val());

                            $('#amount_pending').val($('#membership_fee').val() - $('#discount_amount')
                                .val());

                            $('#amount_pending').val($('#net_amount').val() - $('#received_amount')
                            .val());

                            if (parseFloat($('#discount').val()) <= max_discount) {
                                $('#discount').removeClass('is-invalid').addClass('is-valid');
                            } else {
                                $('#discount').removeClass('is-valid').addClass('is-invalid');
                                $('#discount').val(0);
                                $('#discount_amount').val(0);
                            }
                        } else {
                            $('#discount_amount').removeClass('is-valid').addClass('is-invalid');
                            $('#amount_pending').val($('#net_amount').val() - $('#received_amount')
                            .val());
                            $('#discount').val(0);
                            $('#discount_amount').val(0);
                        }
                    })


                    $('#amount_pending').val($('#net_amount').val() - $('#received_amount').val());

                }
            })
        }
        

        function getAccounts() {
            var accounts = $('#account_id').val();

            @foreach ($accounts as $id => $name)
                if (accounts.includes("{{ $name }}")) {
                    $('#{{ $name }}').removeAttr('readonly');
                } else {
                    $('#{{ $name }}').attr('readonly', true);
                    $('#{{ $name }}').val(0);
                }

                $('#{{ $name }}').on('keyup', function() {
                    var sum = 0;
                    $('.accounts').each(function() {
                        sum += parseFloat($(this).val());
                    });
                    $('#received_amount').val(parseFloat(sum) + {{ $invoice->payments_sum_amount }});
                    if (parseFloat($('#received_amount').val()) > parseFloat($("#net_amount").val())) {
                        $('.accounts').removeClass('is-valid').addClass('is-invalid');
                        $('.accounts').val(0)
                        $('.reminderCard').slideUp();
                        $('#received_amount').val("{{ $invoice->payments_sum_amount }}");
                    } else if (parseFloat($('#received_amount').val()) < parseFloat($("#net_amount").val())) {
                        $('.accounts').removeClass('is-invalid').addClass('is-valid');
                        $('#amount_pending').val($('#net_amount').val() - $('#received_amount').val());
                        $('.reminderCard').slideDown();

                    } else {
                        $('.accounts').removeClass('is-invalid').addClass('is-valid');
                        $('#amount_pending').val($('#net_amount').val() - $('#received_amount').val());
                        $('.reminderCard').slideUp();
                    }
                })
            @endforeach
        }

        function getStatus() {
            var status_id = $('#member_status_id').val();
            var url = "{{ route('admin.getMemberStatus', [':id', ':date']) }}",
                url = url.replace(':id', status_id);
            url = url.replace(':date', "{{ date('Y-m-d') }}");

            $.ajax({
                method: 'GET',
                url: url,
                success: function(response) {
                    $('#due_date').val(response.due_date);
                }
            });
        }
    </script>
@endsection
