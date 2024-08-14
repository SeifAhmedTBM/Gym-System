@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.create') }} {{ trans('cruds.withdrawal.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.withdrawals.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group row">

                <div class="col-md-6">
                    <label class="required" for="amount">{{ trans('cruds.withdrawal.fields.amount') }}</label>
                    <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', '0') }}" step="0.01" required>
                    @if($errors->has('amount'))
                        <div class="invalid-feedback">
                            {{ $errors->first('amount') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.withdrawal.fields.amount_helper') }}</span>
                </div>
                <div class="col-md-6">
                    <label class="required" for="account_id">{{ trans('cruds.withdrawal.fields.account') }}</label>
                    <select class="form-control select2 {{ $errors->has('account') ? 'is-invalid' : '' }}" name="account_id" id="account_id" required>
                    
                    </select>
                    @if($errors->has('account'))
                        <div class="invalid-feedback">
                            {{ $errors->first('account') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.withdrawal.fields.account_helper') }}</span>
                </div>
            </div>
           
            <div class="form-group">
                <label class="required" for="notes">{{ trans('cruds.withdrawal.fields.notes') }}</label>
                    <textarea class="form-control {{ $errors->has('notes') ? 'is-invalid' : '' }}" name="notes" id="notes" required>{{ old('notes') }}</textarea>
                    @if($errors->has('notes'))
                        <div class="invalid-feedback">
                            {{ $errors->first('notes') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.withdrawal.fields.notes_helper') }}</span>
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