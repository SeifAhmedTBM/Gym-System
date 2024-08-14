@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.create') }} {{ trans('cruds.account.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.accounts.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="name">{{ trans('cruds.account.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.account.fields.name_helper') }}</span>
                </div>
                <div class="col-md-6">
                    <label class="required" for="opening_balance">{{ trans('cruds.account.fields.opening_balance') }}</label>
                    <input class="form-control {{ $errors->has('opening_balance') ? 'is-invalid' : '' }}" type="number" name="opening_balance" id="opening_balance" value="{{ old('opening_balance', '0') }}" step="0.01" required>
                    @if($errors->has('opening_balance'))
                        <div class="invalid-feedback">
                            {{ $errors->first('opening_balance') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.account.fields.opening_balance_helper') }}</span>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-6">
                    <label for="branch_id">{{ trans('cruds.branch.title_singular') }}</label>
                    <select name="branch_id" id="branch_id" class="form-control select2">
                        @foreach ($branches as $id => $name)
                            <option value="{{ $id }}" {{ old('branch_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="commission_percentage">Commission Percentage</label>
                    <div class="input-group">
                        <input type="number" class="form-control" step="0.01" name="commission_percentage" value="1" required>
                        <div class="input-group-append">
                            <span class="input-group-text" >%</span>
                        </div>
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