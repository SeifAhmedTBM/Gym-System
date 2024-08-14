@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.edit') }} {{ trans('cruds.reminder.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.reminders.update", [$reminder->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="due_date">{{ trans('cruds.reminder.fields.due_date') }}</label>
                <input class="form-control date {{ $errors->has('due_date') ? 'is-invalid' : '' }}" type="text" name="due_date" id="due_date" value="{{ old('due_date', $reminder->due_date) }}">
                @if($errors->has('due_date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('due_date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.reminder.fields.due_date_helper') }}</span>
            </div>
            
            <div class="form-group">
                <label class="required" for="lead_id">{{ trans('cruds.reminder.fields.lead') }}</label>
                <select class="form-control select2 {{ $errors->has('lead') ? 'is-invalid' : '' }}" name="lead_id" id="lead_id" required>
                    @foreach($leads as $id => $entry)
                        <option value="{{ $id }}" {{ (old('lead_id') ? old('lead_id') : $reminder->lead->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('lead'))
                    <div class="invalid-feedback">
                        {{ $errors->first('lead') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.reminder.fields.lead_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="user_id">{{ trans('cruds.reminder.fields.user') }}</label>
                <select class="form-control select2 {{ $errors->has('user') ? 'is-invalid' : '' }}" name="user_id" id="user_id" required>
                    @foreach($users as $id => $entry)
                        <option value="{{ $id }}" {{ (old('user_id') ? old('user_id') : $reminder->user->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('user'))
                    <div class="invalid-feedback">
                        {{ $errors->first('user') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.reminder.fields.user_helper') }}</span>
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