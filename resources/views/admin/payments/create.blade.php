@extends('layouts.admin')
@section('content')
    
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger">
                <h2 class="text-center">{{ trans('global.pending_amount').' '.$invoice->rest . ' EGP' }}</h2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>{{ trans('global.create') }} {{ trans('cruds.payment.title_singular') }}</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.invoice.storePayment',$invoice->id) }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group row">

                    <div class="col-md-3">
                        <label class="required"  for="amount_pending">{{ trans('cruds.payment.fields.amount_pending') }}</label>
                        <input class="form-control {{ $errors->has('amount_pending') ? 'is-invalid' : '' }}" type="number"
                            name="amount_pending" id="amount_pending" value="{{ old('amount_pending') ?? $invoice->rest }}" required readonly>
                        @if ($errors->has('amount_pending'))
                            <div class="invalid-feedback">
                                {{ $errors->first('amount_pending') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.payment.fields.amount_pending_helper') }}</span>
                    </div>

                    <div class="col-md-3">
                        <label class="required" for="received_amount">{{ trans('cruds.invoice.fields.received_amount') }}</label>
                        <input type="text" class="form-control" name="received_amount" id="received_amount" value="0" readonly>
                        @if ($errors->has('received_amount'))
                            <div class="invalid-feedback">
                                {{ $errors->first('received_amount') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.invoice.fields.received_amount_helper') }}</span>
                    </div>
                    
                    <input type="hidden" name="sales_by_id" id="sales_by_id" value="{{$invoice->membership->member->sales_by_id}}"/>

                    <div class="col-md-3">
                        <label>{{ trans('cruds.invoice.fields.account') }}</label>
                        <select class="form-control select2 {{ $errors->has('account_id') ? 'is-invalid' : '' }}"
                            id="account_id" multiple="multiple" onchange="getAccounts()">
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
                   
                    <div class="col-md-3">
                        <label>Payment Date</label>
                        <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{date('Y-m-d')}}" 
                        @cannot('edit_payment_date')
                            readonly
                        @endcannot >
                        @if ($errors->has('payment_date'))
                            <div class="invalid-feedback">
                                {{ $errors->first('payment_date') }}
                            </div>
                        @endif
                        {{-- <span class="help-block">{{ trans('cruds.invoice.fields.account_helper') }}</span> --}}
                    </div>
                </div>

                <div class="row form-group">
                    @foreach ($accounts as $id => $name)
                        <input type="hidden" name="account_ids[]" value="{{ $id }}">
                        <div class="col-md-3">
                            <label class="required"
                                for="{{ str_replace(' ', '', $name) }}">{{ $name }}</label>
                            <input class="form-control accounts {{ $errors->has('cash_amount') ? 'is-invalid' : '' }}"
                                type="number" name="account_amount[]" id="{{ str_replace(' ', '', $name) }}"
                                value="{{ old($name) ?? 0 }}" required readonly>
                            <span class="help-block">{{ trans('cruds.payment.fields.cash_amount_helper') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    <label for="">{{ trans('global.notes') }}</label>
                    <textarea name="notes" id="notes" rows="7" class="form-control"></textarea>
                </div>

                <div class="form-group reminderCard" style="display: none">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="due_date">{{ trans('global.due_date') }}</label>
                            <input type="date" class="form-control" name="due_date" id="due_date">
                        </div>
                    </div>
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
          function getAccounts() {
            var accounts = $('#account_id').val();

            @foreach ($accounts as $id => $name)
                if(accounts.includes("{{ str_replace(' ', '', $name) }}"))
                {
                    $('#{{ str_replace(' ', '', $name) }}').removeAttr('readonly');
                }else{
                    $('#{{ str_replace(' ', '', $name) }}').attr('readonly',true);
                    $('#{{ str_replace(' ', '', $name) }}').val(0);
                }

                $('#{{ str_replace(' ', '', $name) }}').on('keyup',function(){
                    var sum = 0;
                    $('.accounts').each(function(){
                        sum += parseFloat($(this).val());
                    });

                    $('#received_amount').val(parseFloat(sum));

                    if(parseFloat($('#received_amount').val()) > parseFloat({{$invoice->rest}}) ){
                        $('.accounts').removeClass('is-valid').addClass('is-invalid');
                        $('.accounts').val(0)
                        $('.reminderCard').slideUp();
                        
                    }else if(parseFloat($('#received_amount').val()) < parseFloat({{$invoice->rest}})) {
                        $('.accounts').removeClass('is-invalid').addClass('is-valid');
                        $('#amount_pending').val( parseFloat({{$invoice->rest}}) - parseFloat($('#received_amount').val()) );
                        $('.reminderCard').slideDown();

                    }else{
                        $('.accounts').removeClass('is-invalid').addClass('is-valid');
                        $('#amount_pending').val( parseFloat({{$invoice->rest}}) - parseFloat($('#received_amount').val()) );
                        $('.reminderCard').slideUp();                        
                    }
                })
            @endforeach
        }  

        function getStatus()
        {
            var status_id = $('#member_status_id').val();
            var url = "{{ route('admin.getMemberStatus',[':id', ':date']) }}",
            url = url.replace(':id', status_id);
            url = url.replace(':date', "{{date('Y-m-d')}}");

            $.ajax({
                method : 'GET',
                url : url,
                success:function(response)
                {
                    $('#due_date').val(response.due_date);
                }
            });
        }
    </script>
@endsection