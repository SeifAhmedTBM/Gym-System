@extends('layouts.admin')
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger">
            <h2 class="text-center">{{ trans('global.pending_amount').' '.($payment->invoice->rest + $payment->amount) . ' EGP' }}</h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.edit') }} {{ trans('cruds.payment.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.payments.update", [$payment->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required">{{ trans('cruds.account.title_singular') }}</label>
                    <select class="form-control {{ $errors->has('account') ? 'is-invalid' : '' }}" name="account_id" id="account_id" required>
                        <option value disabled {{ old('account_id', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach($accounts as $id => $name)
                            <option value="{{ $id }}" {{ old('account_id', $payment->account_id) === $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('account_id'))
                        <div class="invalid-feedback">
                            {{ $errors->first('account_id') }}
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="amount">{{ trans('cruds.payment.fields.amount') }}</label>
                    <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', $payment->amount) }}" step="0.01">
                    @if($errors->has('amount'))
                        <div class="invalid-feedback">
                            {{ $errors->first('amount') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.payment.fields.amount_helper') }}</span>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label for="">{{ trans('global.notes') }}</label>
                    <input type="text" name="notes" id="notes" class="form-control" value="{{ $payment->notes }}">
                </div>

                <div class="col-md-6">
                    <label class="required" for="payment_date">Payment Date</label>
                    <input type="date" class="form-control" name="payment_date" id="payment_date" value="{{date('Y-m-d',strtotime($payment->created_at))}}" @cannot('edit_payment_date') readonly @endcannot>

                    @if($errors->has('payment_date'))
                        <div class="invalid-feedback">
                            {{ $errors->first('payment_date') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.payment.fields.sales_by_helper') }}</span>
                </div>
            </div>

            <div class="form-group row">
                {{-- <div class="col-md-6">
                    <label class="required" for="sales_by_id">{{ trans('cruds.payment.fields.sales_by') }}</label>
                    <select class="form-control select2 {{ $errors->has('sales_by') ? 'is-invalid' : '' }}" name="sales_by_id" id="sales_by_id" required>
                        @foreach($sales_bies as $id => $entry)
                            <option value="{{ $id }}" {{ (old('sales_by_id') ? old('sales_by_id') : $payment->sales_by->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('sales_by'))
                        <div class="invalid-feedback">
                            {{ $errors->first('sales_by') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.payment.fields.sales_by_helper') }}</span>
                </div> --}}
                
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
@section('scripts')
    <script>
        var rest    = "{{ $payment->invoice->rest }}";
        var payment = "{{ $payment->rest }}";
        var amount  = "{{ $payment->amount }}";
        $('#amount').keyup(function(){
            if (parseFloat($('#amount').val()) > (parseFloat(rest) + parseFloat(amount))) 
            {
                $('#amount').val(parseFloat(payment));
            }else{
                $('#amount').addClass('is-valid')
            }
        })
    </script>
@endsection