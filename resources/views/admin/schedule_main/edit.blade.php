@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('global.edit') }} {{ trans('cruds.schedule.title_singular') }} Main</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.schedule-mains.update', [$schedule_main->id]) }}"
                enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="required" for="session_id">{{ trans('cruds.schedule.fields.session') }}</label>
                        <select class="form-control select2 {{ $errors->has('session') ? 'is-invalid' : '' }}"
                            name="session_id" id="session_id" required>
                            @foreach ($sessions as $id => $entry)
                                <option value="{{ $id }}"
                                    {{ (old('session_id') ? old('session_id') : $schedule_main->session->id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $entry }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('session'))
                            <div class="invalid-feedback">
                                {{ $errors->first('session') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.schedule.fields.session_helper') }}</span>
                    </div>
                    <div class="col-md-6">
                        <label class="required" for="date">{{ trans('cruds.schedule.fields.date') }}</label>
                        <input class="form-control {{ $errors->has('date') ? 'is-invalid' : '' }}" type="month"
                            name="date" id="date" value="{{ old('date', $schedule_main->date) }}" required>
                        @if ($errors->has('date'))
                            <div class="invalid-feedback">
                                {{ $errors->first('date') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.schedule.fields.date_helper') }}</span>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-4">
                        <label class="required" for="timeslot_id">{{ trans('cruds.schedule.fields.timeslot') }}</label>
                        <select class="form-control select2 {{ $errors->has('timeslot') ? 'is-invalid' : '' }}"
                            name="timeslot_id" id="timeslot_id" required>
                            @foreach ($timeslots as $timeslot)
                                <option value="{{ $timeslot->id }}"
                                    {{ (old('timeslot_id') ? old('timeslot_id') : $schedule_main->timeslot->id ?? '') == $timeslot->id ? 'selected' : '' }}>
                                    {{ date('g:i A', strtotime($timeslot->from)) . ' TO ' . date('g:i A', strtotime($timeslot->to)) }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('timeslot'))
                            <div class="invalid-feedback">
                                {{ $errors->first('timeslot') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.schedule.fields.timeslot_helper') }}</span>
                    </div>

                    <div class="col-md-4">
                        <label class="required" for="trainer_id">{{ trans('cruds.schedule.fields.trainer') }}</label>
                        <select class="form-control select2 {{ $errors->has('trainer') ? 'is-invalid' : '' }}"
                            name="trainer_id" id="trainer_id" required>
                            @foreach ($trainers as $id => $entry)
                                <option value="{{ $id }}"
                                    {{ (old('trainer_id') ? old('trainer_id') : $schedule_main->trainer->id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $entry }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('trainer'))
                            <div class="invalid-feedback">
                                {{ $errors->first('trainer') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.schedule.fields.trainer_helper') }}</span>
                    </div>



                    <div class="col-md-4">
                        <label class="required" for="schedule_main_group_id">Schedule Main</label>
                        <select
                            class="form-control select2 {{ $errors->has('schedule_main_group_id') ? 'is-invalid' : '' }}"
                            name="schedule_main_group_id" id="schedule_main_group_id" required>
                            @foreach (App\Models\ScheduleMainGroup::pluck('name', 'id') as $id => $name)
                                <option value="{{ $id }}"
                                    {{ (old('schedule_main_group_id') ? old('schedule_main_group_id') : $schedule_main->schedule_main_group_id ?? '') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('schedule_main_group_id'))
                            <div class="invalid-feedback">
                                {{ $errors->first('schedule_main_group_id') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="required" for="branch_id">Branch</label>
                        <select class="form-control select2 {{ $errors->has('branch_id') ? 'is-invalid' : '' }}"
                            name="branch_id" id="branch_id" required>
                            @foreach ($branches as $id => $name)
                                <option value="{{ $id }}"
                                    {{ old('branch_id', $schedule_main->branch_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('branch_id'))
                            <div class="invalid-feedback">
                                {{ $errors->first('branch_id') }}
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="required"
                            for="commission_type">{{ trans('cruds.schedule.fields.comission_type') }}</label>
                        <select class="form-control select2 {{ $errors->has('commission_type') ? 'is-invalid' : '' }}"
                            name="commission_type" id="comission_type" required>
                            <option value="fixed" @if ($schedule_main->commission_type == 'fixed') selected @endif>Fixed</option>
                            <option value="percentage" @if ($schedule_main->commission_type == 'percentage') selected @endif>Percentage</option>
                        </select>
                        @if ($errors->has('commission_type'))
                            <div class="invalid-feedback">
                                {{ $errors->first('commission_type') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.schedule.fields.comission_type_helper') }}</span>
                    </div>

                    <div class="col-md-6">
                        <label class="required"
                            for="commission_amount">{{ trans('cruds.schedule.fields.comission_amount') }}</label>
                        <input type="number" id="commission_amount" name="commission_amount"
                            value="{{ $schedule_main->commission_amount }}" class="form-control">
                        @if ($errors->has('commission_amount'))
                            <div class="invalid-feedback">
                                {{ $errors->first('commission_amount') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.schedule.fields.comission_amount_helper') }}</span>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="required" for="status">{{ trans('cruds.status.title_singular') }}</label>
                        <select class="form-control select2 {{ $errors->has('status') ? 'is-invalid' : '' }}"
                            name="status" id="status" required>
                            @foreach (App\Models\ScheduleMain::STATUS as $key => $value)
                                <option value="{{ $key }}"
                                    {{ $schedule_main->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('status'))
                            <div class="invalid-feedback">
                                {{ $errors->first('status') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    @foreach ($schedule_main->schedules as $schedule)
                        <div class="col-md-6">
                            <input type="hidden" name="schedule_id[]" value="{{ $schedule->id }}">
                            <label for="">Day ({{ $loop->iteration }})</label>
                            <select name="day[]" id="" class="form-control">
                                @foreach (\App\Models\Schedule::DAY_SELECT as $key => $value)
                                    <option value="{{ $key }}" {{ $schedule->day == $key ? 'selected' : '' }}>
                                        {{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
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
