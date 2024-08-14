@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.create') }} {{ trans('cruds.expense.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.expenses.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="expenses_category_id">{{ trans('cruds.expense.fields.expenses_category') }}</label>
                    <select class="form-control select2 {{ $errors->has('expenses_category') ? 'is-invalid' : '' }}" name="expenses_category_id" id="expenses_category_id" required>
                        @foreach($expenses_categories as $id => $entry)
                            <option value="{{ $id }}" {{ old('expenses_category_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('expenses_category'))
                        <div class="invalid-feedback">
                            {{ $errors->first('expenses_category') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.expense.fields.expenses_category_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label class="required" for="name">{{ trans('cruds.expense.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.expense.fields.name_helper') }}</span>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="amount">{{ trans('cruds.expense.fields.amount') }}</label>
                    <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', '0') }}" step="0.01" required>
                    @if($errors->has('amount'))
                        <div class="invalid-feedback">
                            {{ $errors->first('amount') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.expense.fields.amount_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label class="required" for="date">{{ trans('cruds.expense.fields.date') }}</label>
                    <input class="form-control {{ $errors->has('date') ? 'is-invalid' : '' }}" type="date" name="date" id="date" value="{{ old('date') ?? date('Y-m-d') }}" required>
                    @if($errors->has('date'))
                        <div class="invalid-feedback">
                            {{ $errors->first('date') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.expense.fields.date_helper') }}</span>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required">{{ trans('cruds.branch.title_singular') }}</label>
                    <select class="form-control {{ $selected_branch == NULL ? 'select2' : '' }} {{ $errors->has('branch_id') ? 'is-invalid' : '' }}" id="branch_id" 
                        onchange="getBranch()" required {{ $selected_branch != NULL ? 'readonly' : '' }}>
                        @foreach ($branches as $id => $name)
                            <option value="{{ $id }}" {{ $selected_branch != NULL && $selected_branch->id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('branch_id'))
                        <div class="invalid-feedback">
                            {{ $errors->first('branch_id') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.invoice.fields.account_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label class="required" for="account_id">{{ trans('cruds.account.title_singular') }}</label>
                    <select class="form-control select2 {{ $errors->has('account_id') ? 'is-invalid' : '' }}" name="account_id" id="account_id" required>
                        @if ($selected_branch != NULL)
                            @foreach ($selected_branch->accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @if($errors->has('account_id'))
                        <div class="invalid-feedback">
                            {{ $errors->first('account_id') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.refund.fields.refund_reason_helper') }}</span>
                </div>
            </div>
            
            <div class="form-group row">
                <div class="col-md-6">
                    <label for="note">{{ trans('cruds.expense.fields.note') }}</label>
                    <input type="text" name="note" id="note" value="{{ old('note') }}" class="form-control" />
                    @if($errors->has('note'))
                        <div class="invalid-feedback">
                            {{ $errors->first('note') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.expense.fields.note_helper') }}</span>
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
    function getBranch() 
    {
        var branch_id = $('#branch_id').val();
        var url = "{{ route('admin.get-branch-accounts',':id') }}";
        url = url.replace(':id',branch_id);
        $.ajax({
            method : 'GET',
            url : url,
            success:function(response)
            {
                $('#account_id').empty();
                response.accounts.forEach(resp => {
                    $("#account_id").append(`<option value='${resp.id}'>${resp.name}</option>`)
                })
            }
        });
    }
</script>
{{-- <script>
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
</script> --}}
@endsection