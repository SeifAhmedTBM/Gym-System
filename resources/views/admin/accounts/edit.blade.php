@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.edit') }} {{ trans('cruds.account.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.accounts.update", [$account->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="name">{{ trans('cruds.account.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $account->name) }}" required>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.account.fields.name_helper') }}</span>
                </div>
                <div class="col-md-6">
                    <label for="branch_id">{{ trans('cruds.branch.title_singular') }}</label>
                    <select name="branch_id" id="branch_id" class="form-control select2">
                        @foreach ($branches as $id => $name)
                            <option value="{{ $id }}" {{ $account->branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-6">
                    <label for="commission_percentage">Commission Percentage</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" name="commission_percentage" value="{{ $account->commission_percentage }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text" >%</span>
                        </div>
                    </div>
                </div>

                @if (Auth()->user()->roles[0]->title == 'Super Admin' || Auth()->user()->roles[0]->title == 'Developer' || Auth()->user()->roles[0]->title == 'Admin')
                <div class="col-md-6">
                    <label for="" class="d-block">Manager</label>
                    <label class="c-switch c-switch-success shadow-none mt-2">
                        <input type="checkbox" name="manager" value="1" class="c-switch-input" {{ $account->manager == true ? 'checked' : '' }}>
                        <span class="c-switch-slider shadow-none"></span>
                    </label>
                </div>
            @endif
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