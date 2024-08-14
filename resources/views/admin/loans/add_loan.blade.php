@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.loan.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.loans.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group row">
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                <div class="col-md-6">
                    <label class="required" for="name">{{ trans('cruds.loan.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.loan.fields.name_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label for="created_at">Created At</label>
                    <input type="date" class="form-control" value="{{ date('Y-m-d') }}" name="created_at">
                </div>
            </div>
            
            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="description">{{ trans('cruds.loan.fields.description') }}</label>
                    <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description" required>{{ old('description') }}</textarea>
                    @if($errors->has('description'))
                        <div class="invalid-feedback">
                            {{ $errors->first('description') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.loan.fields.description_helper') }}</span>
                </div>
                <div class="col-md-6">
                    <label class="required" for="amount">{{ trans('cruds.loan.fields.amount') }}</label>
                    <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', '0') }}" step="0.01" required>
                    @if($errors->has('amount'))
                        <div class="invalid-feedback">
                            {{ $errors->first('amount') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.loan.fields.amount_helper') }}</span>
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