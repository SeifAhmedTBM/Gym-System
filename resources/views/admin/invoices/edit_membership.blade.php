<div class="card">
    <div class="card-header">
        ENTER DETAILS OF THE SUBSCRIPTION
    </div>
    <div class="card-body">
        <div class="row form-group">
            <div class="col-md-3">
                <label for="service_type" class="required">{{ trans('cruds.service.fields.service_type') }}</label>
                <select name="service_type_id" id="service_type_id" class="form-control select2" onchange="getPricelists()">
                    <option value="{{ NULL }}" selected disabled>{{ trans('global.pleaseSelect') }}</option>
                    @foreach (\App\Models\ServiceType::pluck('name','id') as $id => $name)
                        <option value="{{ $id }}" {{ $invoice->membership->service_pricelist->service->service_type_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="required" for="service_pricelist_id">{{ trans('cruds.member.fields.service') }}</label>
                <select onchange="getExpiry()" class="form-control select2 {{ $errors->has('service_pricelist_id') ? 'is-invalid' : '' }}"
                    name="service_pricelist_id" id="service_pricelist_id" required>
                    <option disabled selected hidden>Select Service</option>
                    @foreach ($invoice->membership->service_pricelist->service->service_type->service_pricelists as $pricelist)
                        <option value="{{ $pricelist->id }}" {{ $invoice->membership->service_pricelist_id == $pricelist->id ? 'selected' : '' }}>
                            {{ $pricelist->name }}  </option>
                    @endforeach
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
                <input class="form-control {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="date"
                    name="start_date" id="start_date" onblur="getExpiry()" value="{{ $invoice->membership->start_date ?? date('Y-m-d') }}" required>
                @if ($errors->has('start_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('start_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membership.fields.start_date_helper') }}</span>
            </div>

            <div class="col-md-3">
                <label class="required" for="end_date">{{ trans('cruds.membership.fields.end_date') }}</label>
                <input class="form-control {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="date"
                    name="end_date" id="end_date" value="{{  $invoice->membership->end_date ?? date('Y-m-d') }}" required 
                    @cannot('editable_end_date') 
                     readonly
                    @endcannot
                    >
                @if ($errors->has('end_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('end_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membership.fields.end_date_helper') }}</span>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-3">
                <label class="required" for="trainer_id">{{ trans('cruds.member.fields.trainer') }}</label>
                <select class="form-control  {{ $errors->has('trainer_id') ? 'is-invalid' : '' }}" name="trainer_id" id="trainer_id" required>
                    <option disabled selected hidden>{{ trans('global.pleaseSelect') }}</option>
                    @foreach ($trainers as $id => $entry)
                        <option value="{{ $id }}" {{ $invoice->membership->trainer_id == $id ? 'selected' : '' }}>
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

            <div class="col-md-6">
                <label class="" for="subscription_notes">Subscription Notes</label>
                <input class="form-control" id="subscription_notes" name="subscription_notes" value="{{$invoice->membership->notes}}" />
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

   {{-- invoice details --}}
   <div class="card">
    <div class="card-header">
        ENTER DETAILS OF THE INVOICE
    </div>
    <div class="card-body">
        @cannot('free_discount') 
            @if ($setting->max_discount != 100)
                <div class="alert alert-warning text-center">
                    <strong><i class="fa fa-exclamation-circle"></i> {{ trans('global.max_discount') }} : {{ $setting->max_discount }} %</strong>
                </div>
            @endif
        @endcannot
        <div class="row form-group">
            <div class="col-md-3">
                <label class="required" for="invoice_id">{{ trans('cruds.payment.fields.invoice') }}</label>
                <input type="text" class="form-control" name="invoice_id" value="{{ $invoice->id  }}" readonly>
                @if ($errors->has('invoice_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('invoice_id') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.payment.fields.invoice_helper') }}</span>
            </div>

            <div class="col-md-3">
                <label class="required" for="membership_fee">{{ trans('cruds.membership.fields.membership_fee') }}</label>
                <input class="form-control {{ $errors->has('membership_fee') ? 'is-invalid' : '' }}" type="text"
                    name="membership_fee" id="membership_fee" value="{{ $invoice->service_fee }}" required readonly>
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
                        name="discount" id="discount" value="{{ old('discount') ?? round(($invoice->discount / $invoice->service_fee) * 100) }}" required step="0.001">
                    <div class="input-group-append">
                        <span class="input-group-text" >%</span>
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
                    <input class="form-control {{ $errors->has('discount_amount') ? 'is-invalid' : '' }}" type="text" name="discount_amount" id="discount_amount" value="{{ $invoice->discount }}" required>
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
                <input name="discount_notes" id="discount_notes" value="{{ $invoice->discount_notes }}" class="form-control">
            </div>
            {{-- <div class="col-md-6">
                <label for="created_at">Invoice Date</label>
                <input type="date" class="form-control" name="created_at" value="{{ $invoice->created_at->format('Y-m-d') ?? date('Y-m-d') }}" @cannot('invoice_change_date') readonly  @endcannot>
            </div> --}}
           
        </div>
    </div>
</div>


<div class="card subscription">
    <div class="card-header">
        ENTER DETAILS OF THE PAYMENT
    </div>

    {{-- {{ dd() }} --}}
    <div class="card-body">
        <div class="row form-group">
            <div class="col-md-3">
                <label class="required" for="net_amount">{{ trans('cruds.invoice.fields.net_amount') }}</label>
                <input type="text" class="form-control" value="{{$invoice->service_fee - $invoice->discount}}" name="net_amount" id="net_amount" readonly>
                @if ($errors->has('net_amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('net_amount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.net_amount_helper') }}</span>
            </div>

            <div class="col-md-3">
                <label class="required" for="received_amount">{{ trans('cruds.invoice.fields.received_amount') }}</label>
                <input type="text" class="form-control" value="{{ $invoice->payments_sum_amount ?? old('received_amount') }}" name="received_amount" id="received_amount" readonly>
                @if ($errors->has('received_amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('received_amount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.received_amount_helper') }}</span>
            </div>

            <div class="col-md-3">
                <label class="required" for="amount_pending">{{ trans('cruds.payment.fields.amount_pending') }}</label>
                <input class="form-control {{ $errors->has('amount_pending') ? 'is-invalid' : '' }}" type="number"
                    name="amount_pending" id="amount_pending" value="{{ ($invoice->service_fee - $invoice->discount) - $invoice->payments_sum_amount }}" required readonly>
                @if ($errors->has('amount_pending'))
                    <div class="invalid-feedback">
                        {{ $errors->first('amount_pending') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.payment.fields.amount_pending_helper') }}</span>
            </div>

            <div class="col-md-3">
                <label class="required">{{ trans('cruds.member.fields.payment_method') }}</label>
                <select class="form-control select2 {{ $errors->has('account_id') ? 'is-invalid' : '' }}" id="account_id" multiple="multiple" onchange="getAccounts()" required>
                    @foreach ($accounts as $id => $name)
                        <option value="{{ str_replace(' ','',$name) }}" {{ $invoice->payments()->whereAccountId($id)->first() ? 'selected' : old('account_id', $name)  }}>{{ $name }}</option>
                    @endforeach
                </select>
                @if($errors->has('account_id'))
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
                    <input class="form-control accounts {{ $errors->has('cash_amount') ? 'is-invalid' : '' }}" type="number"
                        name="account_amount[]" id="{{ str_replace(' ','',$name) }}"
                        @if ($acc = $invoice->payments()->where('account_id', $id)->first())
                        value="{{ $acc->amount }}"
                        @else
                        value="{{ old('account_amount', 0) }}" readonly
                        @endif
                        required>
                    <span class="help-block">{{ trans('cruds.payment.fields.cash_amount_helper') }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>