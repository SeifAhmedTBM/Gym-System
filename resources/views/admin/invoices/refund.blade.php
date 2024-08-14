@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.create') }} {{ trans('cruds.refund.title_singular') }} | <span class="badge badge-info">{{ $invoice->invoicePrefix().$invoice->id }}</span></h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="alert alert-warning text-center">
                <h4><i class="fa fa-exclamation-circle"></i> <strong>{{ trans('global.hint') }} </strong></h4>
            </div>
        </div>
        <div class="form-group">
            <div class="alert alert-danger text-center">
                <h4>{{ trans('cruds.invoice.fields.max_refund') }} <strong>{{ $invoice->payments_sum_amount }} EGP</strong></h4>
            </div>
        </div>
        <form method="POST" action="{{ route("admin.invoice.storeRefund",$invoice->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <h5> {{ trans('cruds.membership.title') }} : {{ $invoice->membership->service_pricelist->service->name .' @ '.$invoice->membership->service_pricelist->amount .' - '.$invoice->membership->service_pricelist->service->service_type->name }}</h5>
            </div>

            <div class="form-group row">
                <div class="col-md-4">
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

                <div class="col-md-4">
                    <label class="required" for="amount">{{ trans('cruds.refund.fields.amount') }}</label>
                    <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', '0') }}" step="0.01"  required >
                    @if($errors->has('amount'))
                        <div class="invalid-feedback">
                            {{ $errors->first('amount') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.refund.fields.amount_helper') }}</span>
                </div>

                <div class="col-md-4">
                    <label class="required" for="account_id">{{ trans('cruds.member.fields.payment_method') }}</label>
                    <select class="form-control select2 {{ $errors->has('refund_reason') ? 'is-invalid' : '' }}" name="account_id" id="account_id" required>
                    </select>
                    @if($errors->has('account_id'))
                        <div class="invalid-feedback">
                            {{ $errors->first('account_id') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.refund.fields.refund_reason_helper') }}</span>
                </div>
                
            </div>
            <div class="form-group">
                
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
        $('#amount').on('keyup',function(){
            if ( parseFloat( $('#amount').val() ) <={{$invoice->payments_sum_amount}} ) {
                $('#amount').removeClass('is-invalid').addClass('is-valid');
            }else{
                $('#amount').removeClass('is-valid').addClass('is-invalid');
                $('#amount').val(' ');
            }
        });

        $('#amount').on('keyup',function(){
            var amount = $('#amount').val();
            var url = "{{ route('admin.getAccountsByAmount') }}";
            $.ajax({
                method : 'POST',
                url : url,
                _token: $('meta[name="csrf-token"]').attr('content'),
                data : {
                    amount:amount,
                    _token: _token
                },
                success:function(data){
                    $('#account_id').empty();
                    $("#account_id").append(`<option selected disabled hidden>Select Account </option>`);
                    data.accounts.forEach(resp => {
                        $("#account_id").append(`<option value='${resp.id}'>${resp.name}</option>`)
                    })
                },
            })
        })
    </script>
@endsection