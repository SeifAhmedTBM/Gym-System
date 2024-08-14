@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.edit') }} {{ trans('cruds.memberStatus.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.member-statuses.update", [$memberStatus->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.memberStatus.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $memberStatus->name) }}" required>
                @if($errors->has('name'))
                    <div class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.memberStatus.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="default_next_followup_days">{{ trans('cruds.memberStatus.fields.default_next_followup_days') }}</label>
                <input class="form-control {{ $errors->has('default_next_followup_days') ? 'is-invalid' : '' }}" type="text" name="default_next_followup_days" id="default_next_followup_days" value="{{ old('default_next_followup_days', $memberStatus->default_next_followup_days) }}" required>
                @if($errors->has('default_next_followup_days'))
                    <div class="invalid-feedback">
                        {{ $errors->first('default_next_followup_days') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.memberStatus.fields.default_next_followup_days_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.memberStatus.fields.need_followup') }}</label>
                <select class="form-control {{ $errors->has('need_followup') ? 'is-invalid' : '' }}" name="need_followup" id="need_followup" required>
                    <option value disabled {{ old('need_followup', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\MemberStatus::NEED_FOLLOWUP_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('need_followup', $memberStatus->need_followup) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('need_followup'))
                    <div class="invalid-feedback">
                        {{ $errors->first('need_followup') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.memberStatus.fields.need_followup_helper') }}</span>
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