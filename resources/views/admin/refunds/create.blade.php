@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.create') }} {{ trans('cruds.refund.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.refunds.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="refund_reason_id">{{ trans('cruds.refund.fields.refund_reason') }}</label>
                <select class="form-control select2 {{ $errors->has('refund_reason') ? 'is-invalid' : '' }}" name="refund_reason_id" id="refund_reason_id" required>
                    @foreach($refund_reasons as $id => $entry)
                        <option value="{{ $id }}" {{ old('refund_reason_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('refund_reason'))
                    <div class="invalid-feedback">
                        {{ $errors->first('refund_reason') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.refund.fields.refund_reason_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="invoice_id">{{ trans('cruds.refund.fields.invoice') }}</label>
                <select class="form-control select2 {{ $errors->has('invoice') ? 'is-invalid' : '' }}" name="invoice_id" id="invoice_id" required>
                    @foreach($invoices as $id => $entry)
                        <option value="{{ $id }}" {{ old('invoice_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('invoice'))
                    <div class="invalid-feedback">
                        {{ $errors->first('invoice') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.refund.fields.invoice_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="amount">{{ trans('cruds.refund.fields.amount') }}</label>
                <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', '0') }}" step="0.01" required>
                @if($errors->has('amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('amount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.refund.fields.amount_helper') }}</span>
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