@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.create') }} {{ trans('cruds.schedule.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.schedules.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="session_id">{{ trans('cruds.schedule.fields.session') }}</label>
                <select class="form-control select2 {{ $errors->has('session') ? 'is-invalid' : '' }}" name="session_id" id="session_id" required>
                    @foreach($sessions as $id => $entry)
                        <option value="{{ $id }}" {{ old('session_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('session'))
                    <div class="invalid-feedback">
                        {{ $errors->first('session') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.schedule.fields.session_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required">{{ trans('cruds.schedule.fields.day') }}</label>
                <select class="form-control select2 {{ $errors->has('day') ? 'is-invalid' : '' }}" name="day[]" id="day" multiple required>
                    {{-- <option value disabled {{ old('day', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option> --}}
                    @foreach(App\Models\Schedule::DAY_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('day', '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('day'))
                    <div class="invalid-feedback">
                        {{ $errors->first('day') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.schedule.fields.day_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="date">{{ trans('cruds.schedule.fields.date') }}</label>
                <input class="form-control {{ $errors->has('date') ? 'is-invalid' : '' }}" type="month" name="date" id="date" value="{{ old('date', date('Y-m')) }}" required>
                @if($errors->has('date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.schedule.fields.date_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="timeslot_id">{{ trans('cruds.schedule.fields.timeslot') }}</label>
                <select class="form-control select2 {{ $errors->has('timeslot') ? 'is-invalid' : '' }}" name="timeslot_id" id="timeslot_id" required>
                    @foreach($timeslots as $timeslot)
                        <option value="{{ $timeslot->id }}" {{ old('timeslot_id') == $timeslot->id ? 'selected' : '' }}>{{ date('g:i A',strtotime($timeslot->from )). ' - TO - ' . date('g:i A',strtotime($timeslot->to )) }}</option>
                    @endforeach
                </select>
                @if($errors->has('timeslot'))
                    <div class="invalid-feedback">
                        {{ $errors->first('timeslot') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.schedule.fields.timeslot_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="trainer_id">{{ trans('cruds.schedule.fields.trainer') }}</label>
                <select class="form-control select2 {{ $errors->has('trainer') ? 'is-invalid' : '' }}" name="trainer_id" id="trainer_id" required>
                    @foreach($trainers as $id => $entry)
                        <option value="{{ $id }}" {{ old('trainer_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('trainer'))
                    <div class="invalid-feedback">
                        {{ $errors->first('trainer') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.schedule.fields.trainer_helper') }}</span>
            </div>
            <div class="form-group" style="display:none">
                <label class="required" for="comission_type">{{ trans('cruds.schedule.fields.comission_type') }}</label>
                <select class="form-control select2 {{ $errors->has('comission_type') ? 'is-invalid' : '' }}" name="comission_type" id="comission_type" required>
                    <option value="fixed" >Fixed</option>
                    <option value="percentage" >Percentage</option>
                </select>
                @if($errors->has('comission_type'))
                    <div class="invalid-feedback">
                        {{ $errors->first('comission_type') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.schedule.fields.comission_type_helper') }}</span>
            </div>

            <div class="form-group">
                <label class="required" for="branch_id">Branch</label>
                <select class="form-control select2 {{ $errors->has('branch_id') ? 'is-invalid' : '' }}" name="branch_id" id="branch_id" required>
                    <option value="" >Select Branch</option>
                     @foreach($branches as $branch)
                     <option value="{{$branch->id}}" >{{$branch->name}}</option>
                     @endforeach
                </select>
                @if($errors->has('branch_id'))
                    <div class="invalid-feedback">
                        {{ $errors->first('branch_id') }}
                    </div>
                @endif
            </div>

            <div class="form-group" style="display:none">
                <label class="required" for="comission_amount">{{ trans('cruds.schedule.fields.comission_amount') }}</label>
                <input type="number" id="comission_amount" name="comission_amount" value="0" class="form-control">
                @if($errors->has('comission_amount'))
                    <div class="invalid-feedback">
                        {{ $errors->first('comission_amount') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.schedule.fields.comission_amount_helper') }}</span>
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