@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.create') }} {{ trans('cruds.invoice.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.invoices.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="discount">{{ trans('cruds.invoice.fields.discount') }}</label>
                <input class="form-control {{ $errors->has('discount') ? 'is-invalid' : '' }}" type="number" name="discount" id="discount" value="{{ old('discount', '0') }}" step="0.01" required>
                @if($errors->has('discount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('discount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.discount_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="discount_notes">{{ trans('cruds.invoice.fields.discount_notes') }}</label>
                <textarea class="form-control {{ $errors->has('discount_notes') ? 'is-invalid' : '' }}" name="discount_notes" id="discount_notes">{{ old('discount_notes') }}</textarea>
                @if($errors->has('discount_notes'))
                    <div class="invalid-feedback">
                        {{ $errors->first('discount_notes') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.discount_notes_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="service_fee">{{ trans('cruds.invoice.fields.service_fee') }}</label>
                <input class="form-control {{ $errors->has('service_fee') ? 'is-invalid' : '' }}" type="number" name="service_fee" id="service_fee" value="{{ old('service_fee', '0') }}" step="0.01" required>
                @if($errors->has('service_fee'))
                    <div class="invalid-feedback">
                        {{ $errors->first('service_fee') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.service_fee_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.invoice.fields.payment_method') }}</label>
                <select class="form-control {{ $errors->has('payment_method') ? 'is-invalid' : '' }}" name="payment_method" id="payment_method">
                    <option value disabled {{ old('payment_method', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Invoice::PAYMENT_METHOD_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('payment_method', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('payment_method'))
                    <div class="invalid-feedback">
                        {{ $errors->first('payment_method') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.payment_method_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="net_amount">{{ trans('cruds.invoice.fields.net_amount') }}</label>
                <input class="form-control {{ $errors->has('net_amount') ? 'is-invalid' : '' }}" type="number" name="net_amount" id="net_amount" value="{{ old('net_amount', '0') }}" step="0.01" required>
                @if($errors->has('net_amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('net_amount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.net_amount_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="membership_id">{{ trans('cruds.invoice.fields.membership') }}</label>
                <select class="form-control select2 {{ $errors->has('membership') ? 'is-invalid' : '' }}" name="membership_id" id="membership_id" required>
                    @foreach($memberships as $id => $entry)
                        <option value="{{ $id }}" {{ old('membership_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('membership'))
                    <div class="invalid-feedback">
                        {{ $errors->first('membership') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.membership_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="sales_by_id">{{ trans('cruds.invoice.fields.sales_by') }}</label>
                <select class="form-control select2 {{ $errors->has('sales_by') ? 'is-invalid' : '' }}" name="sales_by_id" id="sales_by_id" required>
                    @foreach($sales_bies as $id => $entry)
                        <option value="{{ $id }}" {{ old('sales_by_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('sales_by'))
                    <div class="invalid-feedback">
                        {{ $errors->first('sales_by') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.invoice.fields.sales_by_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection