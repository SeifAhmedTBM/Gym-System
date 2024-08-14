@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.edit') }} {{ trans('cruds.membershipAttendance.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.membership-attendances.update", [$membershipAttendance->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="sign_in">{{ trans('cruds.membershipAttendance.fields.sign_in') }}</label>
                <input class="form-control timepicker {{ $errors->has('sign_in') ? 'is-invalid' : '' }}" type="text" name="sign_in" id="sign_in" value="{{ old('sign_in', $membershipAttendance->sign_in) }}">
                @if($errors->has('sign_in'))
                    <div class="invalid-feedback">
                        {{ $errors->first('sign_in') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membershipAttendance.fields.sign_in_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="sign_out">{{ trans('cruds.membershipAttendance.fields.sign_out') }}</label>
                <input class="form-control timepicker {{ $errors->has('sign_out') ? 'is-invalid' : '' }}" type="text" name="sign_out" id="sign_out" value="{{ old('sign_out', $membershipAttendance->sign_out) }}">
                @if($errors->has('sign_out'))
                    <div class="invalid-feedback">
                        {{ $errors->first('sign_out') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membershipAttendance.fields.sign_out_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.membershipAttendance.fields.locker_status') }}</label>
                <select class="form-control {{ $errors->has('locker_status') ? 'is-invalid' : '' }}" name="locker_status" id="locker_status" required>
                    <option value disabled {{ old('locker_status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\MembershipAttendance::LOCKER_STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('locker_status', $membershipAttendance->locker_status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('locker_status'))
                    <div class="invalid-feedback">
                        {{ $errors->first('locker_status') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membershipAttendance.fields.locker_status_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="locker_id">{{ trans('cruds.membershipAttendance.fields.locker') }}</label>
                <select class="form-control select2 {{ $errors->has('locker') ? 'is-invalid' : '' }}" name="locker_id" id="locker_id" required>
                    @foreach($lockers as $id => $entry)
                        <option value="{{ $id }}" {{ (old('locker_id') ? old('locker_id') : $membershipAttendance->locker->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('locker'))
                    <div class="invalid-feedback">
                        {{ $errors->first('locker') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membershipAttendance.fields.locker_helper') }}</span>
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