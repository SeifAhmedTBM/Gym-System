@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.create') }} {{ trans('cruds.employeeSetting.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.employee-settings.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="start_date">{{ trans('cruds.employeeSetting.fields.start_date') }}</label>
                <input class="form-control date {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="text" name="start_date" id="start_date" value="{{ old('start_date') }}" required>
                @if($errors->has('start_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('start_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employeeSetting.fields.start_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="end_date">{{ trans('cruds.employeeSetting.fields.end_date') }}</label>
                <input class="form-control date {{ $errors->has('end_date') ? 'is-invalid' : '' }}" type="text" name="end_date" id="end_date" value="{{ old('end_date') }}" required>
                @if($errors->has('end_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('end_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employeeSetting.fields.end_date_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="start_time">{{ trans('cruds.employeeSetting.fields.start_time') }}</label>
                <input class="form-control timepicker {{ $errors->has('start_time') ? 'is-invalid' : '' }}" type="text" name="start_time" id="start_time" value="{{ old('start_time') }}" required>
                @if($errors->has('start_time'))
                    <div class="invalid-feedback">
                        {{ $errors->first('start_time') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employeeSetting.fields.start_time_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="end_time">{{ trans('cruds.employeeSetting.fields.end_time') }}</label>
                <input class="form-control timepicker {{ $errors->has('end_time') ? 'is-invalid' : '' }}" type="text" name="end_time" id="end_time" value="{{ old('end_time') }}" required>
                @if($errors->has('end_time'))
                    <div class="invalid-feedback">
                        {{ $errors->first('end_time') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employeeSetting.fields.end_time_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="default_month_days">{{ trans('cruds.employeeSetting.fields.default_month_days') }}</label>
                <input class="form-control {{ $errors->has('default_month_days') ? 'is-invalid' : '' }}" type="number" name="default_month_days" id="default_month_days" value="{{ old('default_month_days', '') }}" step="0.01" required>
                @if($errors->has('default_month_days'))
                    <div class="invalid-feedback">
                        {{ $errors->first('default_month_days') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employeeSetting.fields.default_month_days_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="default_vacation_days">{{ trans('cruds.employeeSetting.fields.default_vacation_days') }}</label>
                <input class="form-control {{ $errors->has('default_vacation_days') ? 'is-invalid' : '' }}" type="number" name="default_vacation_days" id="default_vacation_days" value="{{ old('default_vacation_days', '') }}" step="0.01" required>
                @if($errors->has('default_vacation_days'))
                    <div class="invalid-feedback">
                        {{ $errors->first('default_vacation_days') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employeeSetting.fields.default_vacation_days_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="created_by_id">{{ trans('cruds.employeeSetting.fields.created_by') }}</label>
                <select class="form-control select2 {{ $errors->has('created_by') ? 'is-invalid' : '' }}" name="created_by_id" id="created_by_id">
                    @foreach($created_bies as $id => $entry)
                        <option value="{{ $id }}" {{ old('created_by_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('created_by'))
                    <div class="invalid-feedback">
                        {{ $errors->first('created_by') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.employeeSetting.fields.created_by_helper') }}</span>
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