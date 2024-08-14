@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.create') }} {{ trans('cruds.membershipAttendance.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.membership-attendances.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="sign_in">{{ trans('cruds.membershipAttendance.fields.sign_in') }}</label>
                <input class="form-control timepicker {{ $errors->has('sign_in') ? 'is-invalid' : '' }}" type="text" name="sign_in" id="sign_in" value="{{ old('sign_in') }}">
                @if($errors->has('sign_in'))
                    <div class="invalid-feedback">
                        {{ $errors->first('sign_in') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membershipAttendance.fields.sign_in_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="sign_out">{{ trans('cruds.membershipAttendance.fields.sign_out') }}</label>
                <input class="form-control timepicker {{ $errors->has('sign_out') ? 'is-invalid' : '' }}" type="text" name="sign_out" id="sign_out" value="{{ old('sign_out') }}">
                @if($errors->has('sign_out'))
                    <div class="invalid-feedback">
                        {{ $errors->first('sign_out') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.membershipAttendance.fields.sign_out_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="membership_id">{{ trans('cruds.membershipAttendance.fields.membership') }}</label>
                <select class="form-control select2 {{ $errors->has('membership') ? 'is-invalid' : '' }}" name="membership_id" id="membership_id" required>
                    @foreach($memberships as $id => $entry)
                        <option value="{{ $id }}" {{ old('membership_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('membership'))
                    <div class="invalid-feedback">
                        {{ $errors->first('membership') }}
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