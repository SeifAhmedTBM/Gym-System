@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.create') }} {{ trans('cruds.deduction.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.deductions.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="employee_id">{{ trans('cruds.deduction.fields.employee') }}</label>
                    <select class="form-control select2 {{ $errors->has('employee') ? 'is-invalid' : '' }}" name="employee_id" id="employee_id" required>
                        @foreach($employees as $id => $entry)
                            <option value="{{ $id }}" {{ old('employee_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('employee'))
                        <div class="invalid-feedback">
                            {{ $errors->first('employee') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.deduction.fields.employee_helper') }}</span>
                </div>
                {{-- <div class="col-md-6">
                    <label class="required" for="name">{{ trans('cruds.deduction.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.deduction.fields.name_helper') }}</span>
                </div> --}}
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="reason">{{ trans('cruds.deduction.fields.reason') }}</label>
                    <textarea class="form-control {{ $errors->has('reason') ? 'is-invalid' : '' }}" name="reason" id="reason" required>{{ old('reason') }}</textarea>
                    @if($errors->has('reason'))
                        <div class="invalid-feedback">
                            {{ $errors->first('reason') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.deduction.fields.reason_helper') }}</span>
                </div>
                <div class="col-md-6">
                    <label class="required" for="amount">{{ trans('cruds.deduction.fields.amount') }}</label>
                    <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="number" name="amount" id="amount" value="{{ old('amount', '0') }}" step="0.01" required>
                    @if($errors->has('amount'))
                        <div class="invalid-feedback">
                            {{ $errors->first('amount') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.deduction.fields.amount_helper') }}</span>
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