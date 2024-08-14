@extends('layouts.admin')
@section('content')
<a href="{{ route('admin.attendance-settings.index') }}" class="btn mb-2 btn-danger">
    <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back') }}
</a>
{!! Form::open(['method' => 'PUT', 'action' => ['Admin\AttendanceSettingController@update', $attendance_setting->id]]) !!}
<div class="card mb-2">
    <div class="card-header">
        <h5><i class="fa fa-edit"></i> {{ trans('global.edit') }} {{ trans('global.delay_rules') }}</h5>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('from', trans('global.from'), ['class' => 'required']) !!}
                    <div class="input-group">
                        {!! Form::text('from', $attendance_setting->from , ['class' => 'form-control', 'required']) !!}
                        <div class="input-group-append">
                            <span class="input-group-text">
                                {{ trans('global.minutes') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('to', trans('global.to'), ['class' => 'required']) !!}
                    <div class="input-group">
                        {!! Form::text('to', $attendance_setting->to , ['class' => 'form-control', 'required']) !!}
                        <div class="input-group-append">
                            <span class="input-group-text">
                                {{ trans('global.minutes') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('deduction', trans('global.deduction'), ['class' => 'required']) !!}
                    <div class="input-group">
                        {!! Form::number('deduction', $attendance_setting->deduction , ['class' => 'form-control', 'required', 'placeholder' => trans('global.ex_days'), 'step' => '0.01']) !!}
                        <div class="input-group-append">
                            <span class="input-group-text">
                                {{ trans('global.by_day') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="duplicateHere"></div>
    </div>
</div>
<button type="submit" class="btn btn-success">
    <i class="fa fa-check-circle"></i> {{ trans('global.update') }}
</button>
{!! Form::close() !!}
@endsection
