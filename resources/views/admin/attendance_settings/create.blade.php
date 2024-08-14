@extends('layouts.admin')
@section('content')
<a href="{{ route('admin.attendance-settings.index') }}" class="btn btn-danger mb-2">
    <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back') }}
</a>
<div class="card">
    <div class="card-header">
        <h5><i class="fa fa-plus-circle"></i> {{ trans('global.add') }} {{ trans('global.delay_rules') }}</h5>
    </div>
    {!! Form::open(['method' => 'POST', 'action' => 'Admin\AttendanceSettingController@store']) !!}
    <div class="card-body">
        <div class="form-row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('from', trans('global.from'), ['class' => 'required']) !!}
                    <div class="input-group">
                        {!! Form::text('from[]', null, ['class' => 'form-control', 'required']) !!}
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
                        {!! Form::text('to[]', null, ['class' => 'form-control', 'required']) !!}
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
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                {{ trans('global.by_day') }}
                            </span>
                        </div>
                        {!! Form::number('deduction[]', null, ['class' => 'form-control', 'required', 'placeholder' => trans('global.ex_days'), 'step' => '0.01']) !!}
                        <div class="input-group-append">
                            <button class="btn btn-success" type="button" onclick="duplicateForm(this)"><i class="fa fa-plus-circle"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="duplicateHere"></div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-check-circle"></i> {{ trans('global.create') }}
        </button>
    </div>
    {!! Form::close() !!}
</div>
@endsection

@section('scripts')
    <script>
        function duplicateForm() {
            $('.duplicateHere').append(`
            <div class="form-row duplicated">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('from', trans('global.from'), ['class' => 'required']) !!}
                        <div class="input-group">
                            {!! Form::text('from[]', null, ['class' => 'form-control', 'required']) !!}
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
                            {!! Form::text('to[]', null, ['class' => 'form-control', 'required']) !!}
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
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    {{ trans('global.by_day') }}
                                </span>
                            </div>
                            {!! Form::number('deduction[]', null, ['class' => 'form-control', 'required', 'placeholder' => trans('global.ex_days'), 'step' => '0.01']) !!}
                            <div class="input-group-append">
                                <button class="btn btn-danger" type="button" onclick="removeDuplicated(this)"><i class="fa fa-times-circle"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `);
        }

        function removeDuplicated(button) {
            $(button).closest('.duplicated').remove();
        }
    </script>
@endsection