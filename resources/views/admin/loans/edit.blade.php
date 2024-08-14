@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.edit') }} {{ trans('cruds.loan.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.loans.update", [$loan->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="employee_id">{{ trans('cruds.loan.fields.employee') }}</label>
                    <select class="form-control select2 {{ $errors->has('employee') ? 'is-invalid' : '' }}" name="employee_id" id="employee_id" required>
                        @foreach($employees as $id => $entry)
                            <option value="{{ $id }}" {{ (old('employee_id') ? old('employee_id') : $loan->employee->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('employee'))
                        <div class="invalid-feedback">
                            {{ $errors->first('employee') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.loan.fields.employee_helper') }}</span>
                </div>
                <div class="col-md-6">
                    <label class="required" for="name">{{ trans('cruds.loan.fields.description') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $loan->name) }}" required>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.loan.fields.name_helper') }}</span>
                </div>
            </div>
           
            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="amount">{{ trans('cruds.loan.fields.amount') }}</label>
                    <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', $loan->amount) }}" step="0.01" required>
                    @if($errors->has('amount'))
                        <div class="invalid-feedback">
                            {{ $errors->first('amount') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.loan.fields.amount_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label class="required" for="created_at">Date </label>
                    <input type="date" class="form-control {{ $errors->has('created_at') ? 'is-invalid' : '' }}" name="created_at" id="created_at" value="{{ date('Y-m-d',strtotime($loan->created_at)) }}" required>
                    @if($errors->has('created_at'))
                        <div class="invalid-feedback">
                            {{ $errors->first('created_at') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.loan.fields.description_helper') }}</span>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="account_id">Account</label>
                    <select class="form-control" id="account_id" name="account_id" >
                        @foreach ($accounts  as $account)
                            <option value="{{$account->id}}" @if($account->id == $loan->account_id) selected @endif>{{$account->name}}</option> 
                        @endforeach
                    </select>    
                    @if($errors->has('account_id'))
                        <div class="invalid-feedback">
                            {{ $errors->first('account_id') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.loan.fields.description_helper') }}</span>
                </div>

                {{-- <div class="col-md-6">
                    <label class="required" for="description">{{ trans('cruds.loan.fields.description') }}</label>
                    <input type="text" class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description" value="{{ old('description', $loan->description) }}" required>
                    @if($errors->has('description'))
                        <div class="invalid-feedback">
                            {{ $errors->first('description') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.loan.fields.description_helper') }}</span>
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