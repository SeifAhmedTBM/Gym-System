@extends('layouts.admin')
@section('content')
<form method="POST" action="{{ route("admin.employees.update", [$employee->id]) }}" enctype="multipart/form-data">
    @method('PUT')
    @csrf
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('global.edit') }} {{ trans('cruds.employee.title_singular') }} | <b>{{ $employee->name }}</b></h5>
        </div>
        <div class="card-body">
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            @if($employee->user_id != NULL)
            <div class="alert alert-info font-weight-bold">
                <i class="fa fa-exclamation-circle"></i> 
                This employee has user with ID # {{$employee->user_id}} <a href="{{route('admin.users.edit', $employee->user_id)}}" style="color:black !important;"> Click Here to update !</a>
            </div>
            @endif
          
          
          

            <div class="form-row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('name', trans('global.name'), ['class' => 'required']) !!}
                        {!! Form::text('name', $employee->name, ['class' => 'form-control', 'placeholder' => trans('global.name')]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('phone', trans('global.phone'), ['class' => 'required']) !!}
                        {!! Form::number('phone', $employee->phone, ['class' => 'form-control', 'placeholder' => trans('global.phone')]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('national', trans('cruds.lead.fields.national')) !!}
                        {!! Form::number('national', $employee->national, ['class' => 'form-control', 'placeholder' => trans('cruds.lead.fields.national')]) !!}
                    </div>
                </div>
            </div>

            <div class="form-row">
                {{-- <div class="col-md-4">
                    <div class="form-group">
                        <label class="required">{{ trans('cruds.employee.fields.job_status') }}</label>
                        <select class="form-control {{ $errors->has('job_status') ? 'is-invalid' : '' }}" name="job_status" id="job_status" required>
                            <option value disabled {{ old('job_status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                            @foreach(App\Models\Employee::JOB_STATUS_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('job_status', $employee->job_status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('job_status'))
                            <div class="invalid-feedback">
                                {{ $errors->first('job_status') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.employee.fields.job_status_helper') }}</span>
                    </div>
                </div> --}}
                {{-- <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('finger_print_id', trans('global.finger_print_id')) !!}
                        {!! Form::number('finger_print_id', $employee->finger_print_id, ['class' => 'form-control', 'placeholder' => trans('global.finger_print_id')]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('access_card', trans('global.access_card')) !!}
                        {!! Form::text('access_card', $employee->access_card, ['class' => 'form-control']) !!}
                    </div>
                </div> --}}
            </div>
            <div class="form-group row">
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="required" for="salary">{{ trans('cruds.employee.fields.salary') }}</label>
                        <input class="form-control {{ $errors->has('salary') ? 'is-invalid' : '' }}" type="number" name="salary" id="salary" value="{{ old('salary', $employee->salary) }}" step="0.01" required>
                        @if($errors->has('salary'))
                            <div class="invalid-feedback">
                                {{ $errors->first('salary') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.employee.fields.salary_helper') }}</span>
                    </div>
                </div>
                {{-- <div class="col-md-4">
                    <label class="required" for="start_date">{{ trans('cruds.employee.fields.start_date') }}</label>
                    <input class="form-control date {{ $errors->has('start_date') ? 'is-invalid' : '' }}" type="text" name="start_date" id="start_date" value="{{ old('start_date', $employee->start_date) }}" required>
                    @if($errors->has('start_date'))
                        <div class="invalid-feedback">
                            {{ $errors->first('start_date') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.employee.fields.start_date_helper') }}</span>
                </div> --}}

                {{-- <div class="col-md-4">
                    <div class="form-group">
                        <label class="required">{{ trans('global.attendance_check') }}</label>
                        <select class="form-control {{ $errors->has('attendance_check') ? 'is-invalid' : '' }}" name="attendance_check" id="attendance_check" required>
                            <option value disabled {{ old('attendance_check', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                            @foreach(App\Models\Employee::CARD_CHECK_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('attendance_check', $employee->attendance_check) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('attendance_check'))
                            <div class="invalid-feedback">
                                {{ $errors->first('attendance_check') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.employee.fields.card_check_helper') }}</span>
                    </div>
                </div> --}}
            </div>
       
            <div class="form-row">
                {{-- <div class="col-md-4">
                    <div class="form-group">
                        <label class="required" for="order">{{ trans('cruds.employee.fields.order') }}</label>
                        <input class="form-control {{ $errors->has('order') ? 'is-invalid' : '' }}" type="number" name="order" id="order" value="{{ old('order', $employee->order) }}" step="1" required>
                        @if($errors->has('order'))
                            <div class="invalid-feedback">
                                {{ $errors->first('order') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.employee.fields.order_helper') }}</span>
                    </div>
                </div> --}}
                <div class="col-md-4">
                    <label for="target_amount">{{ trans('global.target_amount') }}</label>
                    {!! Form::number('target_amount', $employee->target_amount, ['class' => 'form-control', 'placeholder' => trans('global.target_amount')]) !!}
                </div>
                {{-- <div class="col-md-4">
                    <label class="required">{{ trans('cruds.employee.fields.status') }}</label>
                    <select class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" name="status" id="status" required>
                        <option value disabled {{ old('status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\Employee::STATUS_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('status', $employee->status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('status'))
                        <div class="invalid-feedback">
                            {{ $errors->first('status') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.employee.fields.status_helper') }}</span>
                </div> --}}
            </div>

            <div class="form-group row">
              
                <div class="col-md-4">
                    <label for="branch_id">{{ trans('cruds.branch.title_singular') }}</label>
                    <select name="branch_id" id="branch_id" class="form-control select2">
                        @foreach ($branches as $id => $name)
                            <option value="{{ $id }}" {{ $employee->branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="vacations_balance">{{ trans('global.vacations_balance') }}</label>
                    {!! Form::number('vacations_balance', old('vacation_balance',$employee->vacations_balance) ?? 0, ['class' => 'form-control', 'placeholder' => trans('global.vacations_balance')]) !!}
                </div>

            </div>
        </div>
    </div>
    {{-- @if ($employee->days_count > 0)
    <div class="card">
        <div class="card-header">
            <i class="fa fa-table"></i> {{ trans('global.schedule') }}
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ trans('global.days') }}</th>
                        <th>{{ trans('global.offday') }}</th>
                        <th>{{ trans('global.from') }}</th>
                        <th>{{ trans('global.to') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employee->days as $day)
                    <tr>
                        <td>
                            <input type="hidden" name="days[]" value="{{ $day->day }}">
                            <input type="hidden" name="days_ids[]" value="{{ $day->id }}">
                            <b>{{ config('weekdays')[$day->day] }}</b>
                        </td>
                        <td>
                            <label class="c-switch c-switch-success shadow-none">
                                <input type="checkbox" {{ $day->is_offday ? 'checked' : '' }} data-day="{{ $day->day }}" name="is_offday[{{ $day->day }}]" class="c-switch-input" value="1" onchange="disableDayTime(this)">
                                <span class="c-switch-slider shadow-none"></span>
                            </label>
                        </td>
                        <td>
                            {!! Form::time('from['.$day->day.']', $day->from, ['class' => 'form-control', $day->is_offday ? 'disabled' : '', 'id' => 'from_' . $day->day]) !!}
                        </td>
                        <td>
                            {!! Form::time('to['.$day->day.']', $day->to, ['class' => 'form-control', $day->is_offday ? 'disabled' : '', 'id' => 'to_' . $day->day]) !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-header">
            <i class="fa fa-table"></i> {{ trans('global.schedule_templates') }}
        </div>
        <div class="card-body">
            <div class="form-row bg-light font-weight-bold pt-3 rounded mb-4 px-2">
                <div class="col-md-4">
                    {{ trans('global.has_attendance_schedule') }}
                </div>
                <div class="col-md-8 text-right">
                    <label class="c-switch c-switch-3d c-switch-success">
                        <input type="checkbox" value="yes" name="attendance_check" onchange="checkAttendance()"  class="c-switch-input">
                        <span class="c-switch-slider shadow-none"></span>
                    </label>
                </div>
            </div>
            <div class="switchMe" style="display:none">
                <div class="form-group">
                    {!! Form::label('schedule_template_id', trans('global.schedule_template')) !!}
                    <select onclick="getScheduleDays(this)" id="schedule_template_id" class="form-control">
                        <option value selected disabled>{{ trans('global.pleaseSelect') }}</option>
                        @foreach ($schedule_templates as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ trans('global.days') }}</th>
                                <th>{{ trans('global.offday') }}</th>
                                <th>{{ trans('global.from') }}</th>
                                <th>{{ trans('global.to') }}</th>
                            </tr>
                        </thead>
                        <tbody class="appendDays">
                            <tr>
                                <td colspan="4" class="text-center">{{ trans('global.no_data_available') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>
    @endif --}}

<div class="card">
    <div class="card-body">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-check-circle"></i> {{ trans('global.save') }}
        </button>
    </div>
</div>

</form>


@endsection


@section('scripts')
    <script>
        function disableDayTime(checkBox) {
            let day_name = $(checkBox).data('day');
            if(checkBox.checked == true) {
                $("#from_"+day_name).val(' ');
                $("#from_"+day_name).attr('disabled', 'disabled');
                $("#to_"+day_name).val(' ');
                $("#to_"+day_name).attr('disabled', 'disabled');
            }else {
                $("#from_"+day_name).removeAttr('disabled');
                $("#to_"+day_name).removeAttr('disabled');
            }
        }

        function checkAttendance() {
            $(".switchMe").slideToggle();
        }

        function getScheduleDays(select) {
            let template_id = $(select).val();
            let url = "{{ route('admin.schedule-templates.get', ':id') }}";
            url = url.replace(':id', template_id);
            $.ajax({
                method : "GET",
                url : url,
                success: function(response) {
                    $(".appendDays").html('');
                    for(let i = 0 ; i < response.days.length ; i++) {
                        $(".appendDays").append(`
                        <tr>
                            <td>
                                <input type="hidden" name="days[]" value="${response.days[i].day}">
                                <b>${response.days[i].day} <span class="text-danger">*</span> </b><br>
                            </td>
                            <td>
                                <label class="c-switch c-switch-success shadow-none">
                                    <input type="checkbox" onchange="disableDayTime(this)" data-day="${response.days[i].day}" name="offday[${response.days[i].day}]" value="1" class="c-switch-input" ${response.days[i].is_offday == 1 ? 'checked' : ''}>
                                    <span class="c-switch-slider shadow-none"></span>
                                </label>
                            </td>
                            <td>
                                <input id="from_${response.days[i].day}" type="time" ${response.days[i].is_offday == 1 ? 'disabled' : ''} class="form-control" name="from[${response.days[i].day}]" value="${response.days[i].from}">
                            </td>
                            <td>
                                <input id="to_${response.days[i].day}" type="time" ${response.days[i].is_offday == 1 ? 'disabled' : ''}  class="form-control" name="to[${response.days[i].day}]" value="${response.days[i].to}">
                            </td>
                        </tr>
                        `);
                    }
                }
            })
        }
    </script>
@endsection