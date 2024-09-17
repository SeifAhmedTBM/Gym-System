@extends('layouts.admin')
@section('content')
    <form method="POST" action="{{ route('admin.employees.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ trans('global.create') }} {{ trans('cruds.user.title_singular') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-row font-weight-bold pt-3 rounded mb-4 px-2">
                            <div class="col-md-3">
                                {{ trans('global.can_login') }}
                            </div>
                            <div class="col-md-9 text-right">
                                <label class="c-switch c-switch-3d c-switch-success">
                                    <input checked type="checkbox" name="can_login" id="can_login" value="yes"
                                        class="c-switch-input">
                                    <span class="c-switch-slider shadow-none"></span>
                                </label>
                            </div>
                        </div>
                        <div class="hideMe">
                            <div class="alert alert-info font-weight-bold">
                                <i class="fa fa-exclamation-circle"></i> {{ trans('global.password_will_be_1_to_6') }}
                            </div>
                            {{-- <div class="form-group">
                            {!! Form::label('email', trans('cruds.user.fields.email'), ['class' => 'required']) !!}
                            {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => trans('cruds.user.fields.email') , 'required' => true]) !!}
                        </div> --}}
                            <div class="form-group">
                                {!! Form::label('role_id', trans('cruds.role.title_singular')) !!}
                                {!! Form::select('role_id', $roles, null, ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ trans('global.create') }} {{ trans('cruds.employee.title_singular') }}</h5>
                    </div>
                    <div class="card-body">

                        <div class="form-group row">

                            <div class="col-md-6">
                                {!! Form::label('name', trans('global.name'), ['class' => 'required']) !!}
                                {!! Form::text('name', null, [
                                    'class' => 'form-control',
                                    'placeholder' => trans('global.name'),
                                    'required' => true,
                                ]) !!}
                            </div>

                            <div class="col-md-6">
                                {!! Form::label('phone', trans('global.phone'), ['class' => 'required']) !!}
                                {!! Form::number('phone', null, [
                                    'class' => 'form-control',
                                    'placeholder' => trans('global.phone'),
                                    'required' => true,
                                ]) !!}
                            </div>

                        </div>
                        <div class="form-group row">

                            <div class="col-md-6">
                                {!! Form::label('national', trans('cruds.lead.fields.national')) !!}
                                {!! Form::number('national', null, [
                                    'class' => 'form-control',
                                    'placeholder' => trans('cruds.lead.fields.national'),
                                ]) !!}
                            </div>
                            <div class="col-md-6">
                                <label for="branch_id">{{ trans('cruds.branch.title_singular') }}</label>
                                <select name="branch_id" id="branch_id" class="form-control select2" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                                    @foreach ($branches as $key => $value)
                                        <option value="{{ $key }}" {{ $employee && $employee->branch_id == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="required"
                                        for="salary">{{ trans('cruds.employee.fields.salary') }}</label>
                                    <input class="form-control {{ $errors->has('salary') ? 'is-invalid' : '' }}"
                                        type="number" name="salary" id="salary" value="{{ old('salary', '0') }}"
                                        step="0.01" required>
                                    @if ($errors->has('salary'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('salary') }}
                                        </div>
                                    @endif
                                    <span class="help-block">{{ trans('cruds.employee.fields.salary_helper') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="target_amount">{{ trans('global.target_amount') }}</label>
                                {!! Form::number('target_amount', null, [
                                    'class' => 'form-control',
                                    'placeholder' => trans('global.target_amount'),
                                ]) !!}
                            </div>
                        </div>
                        <input type="hidden" name="job_status" value="fulltime">
                        <input type="hidden" name="start_date" value="{{ date('Y-m-d') }}">
                        <input type="hidden" name="status" value="active">
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="vacations_balance">{{ trans('global.vacations_balance') }}</label>
                                {!! Form::number('vacations_balance', old('vacation_balance', 0), [
                                    'class' => 'form-control',
                                    'placeholder' => trans('global.vacations_balance'),
                                ]) !!}
                            </div>

                            <div class="col-md-6">
                                <label for="access_card">{{ trans('global.access_card') }}</label>
                                {!! Form::number('access_card', old('vacation_balance') ?? 0, [
                                    'class' => 'form-control',
                                    'placeholder' => trans('global.access_card'),
                                ]) !!}
                            </div>
                            <div class="form-group">
                                <label class="required" for="image">{{ trans('cruds.gallery.fields.images') }}</label>
                                <div class="needsclick dropzone {{ $errors->has('image') ? 'is-invalid' : '' }}" id="image-dropzone">
                                </div>
                                @if($errors->has('image'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('image') }}
                                    </div>
                                @endif
                                <span class="help-block">{{ trans('cruds.gallery.fields.images_helper') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="card">
        <div class="card-header">
            <h5><i class="fa fa-table"></i> {{ trans('global.schedule_templates') }}</h5>
        </div>
        <div class="card-body">
            <div class="form-row font-weight-bold pt-3 rounded mb-4 px-2">
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
                    <table class="table text-center table-bordered table-outline">
                        <thead>
                            <tr>
                                <th>{{ trans('global.days') }}</th>
                                <th>{{ trans('global.offday') }}</th>
                                <th>{{ trans('global.from') }}</th>
                                <th>{{ trans('global.to') }}</th>
                                <th>{{ trans('global.working_hours') }}</th>
                                <th>{{ trans('global.flexible') }}</th>
                            </tr>
                        </thead>
                        <tbody class="appendDays">
                            <tr>
                                <td colspan="6" class="text-center">{{ trans('global.no_data_available') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div> --}}
        <div class="card">
            <div class="card-body">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-check-circle"></i> {{ trans('global.create') }}
                </button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        Dropzone.options.imageDropzone = {
            url: '{{ route('admin.employees.storeMedia') }}',
            maxFilesize: 5, // MB
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            maxFiles: 1,
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            params: {
                size: 5,
                width: 4096,
                height: 4096
            },
            success: function(file, response) {
                $('form').find('input[name="image"]').remove()
                $('form').append('<input type="hidden" name="image" value="' + response.name + '">')
            },
            removedfile: function(file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="image"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function() {
                @if (isset($member) && $member->image)
                var file = {!! json_encode($member->image) !!}
                this.options.addedfile.call(this, file)
                this.options.thumbnail.call(this, file, file.preview)
                file.previewElement.classList.add('dz-complete')
                $('form').append('<input type="hidden" name="image" value="' + file.file_name + '">')
                this.options.maxFiles = this.options.maxFiles - 1
                @endif
            },
            error: function(file, response) {
                if ($.type(response) === 'string') {
                    var message = response //dropzone sends it's own error messages in string
                } else {
                    var message = response.errors.file
                }
                file.previewElement.classList.add('dz-error')
                _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
                _results = []
                for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                    node = _ref[_i]
                    _results.push(node.textContent = message)
                }

                return _results
            }
        }
    </script>
    <script>
        function getScheduleDays(select) {
            let template_id = $(select).val();
            let url = "{{ route('admin.schedule-templates.get', ':id') }}";
            url = url.replace(':id', template_id);
            $.ajax({
                method: "GET",
                url: url,
                success: function(response) {
                    $(".appendDays").html('');
                    for (let i = 0; i < response.days.length; i++) {
                        $(".appendDays").append(`
                        <tr>
                            <td>
                                <input type="hidden" name="days[]" value="${response.days[i].day}">
                                <b class="d-block">${response.days[i].day}</b>
                                <span class="badge mt-2 badge-success px-2 py-2">
                                    ${new Date(response.days[i].day).toLocaleString('en-us', {weekday:'long'})}    
                                </span>
                            </td>
                            <td>
                                <label class="c-switch mt-2 c-switch-success shadow-none">
                                    <input type="checkbox" onchange="disableDayTime(this)" data-day="${response.days[i].day}" name="offday[${response.days[i].day}]" value="1" class="c-switch-input" ${response.days[i].is_offday == 1 ? 'checked' : ''}>
                                    <span class="c-switch-slider shadow-none"></span>
                                </label>
                            </td>
                            <td>
                                <input type="time" id="from_${response.days[i].day}" class="form-control" ${response.days[i].flexible ? 'disabled' : ''} name="from[${response.days[i].day}]" value="${response.days[i].from}">
                            </td>
                            <td>
                                <input type="time" id="to_${response.days[i].day}" class="form-control" ${response.days[i].flexible ? 'disabled' : ''} name="to[${response.days[i].day}]" value="${response.days[i].to}">
                            </td>
                            <td width="200">
                                <input type="numbers" id="working_hours${response.days[i].day}" class="form-control" name="working_hours[${response.days[i].day}]" ${!response.days[i].flexible ? 'disabled' : ''} value="${response.days[i].working_hours}">
                            </td>
                            <td>
                                <label class="c-switch mt-2 c-switch-success shadow-none">
                                    <input type="checkbox" onchange="enableFlexibility(this)" data-day="${response.days[i].day}" name="flexible[${response.days[i].day}]" value="1" class="c-switch-input" ${response.days[i].flexible ? 'checked' : ''}>
                                    <span class="c-switch-slider shadow-none"></span>
                                </label>
                            </td>
                        </tr>
                        `);
                    }
                }
            })
        }

        function checkAttendance() {
            $(".switchMe").slideToggle();
        }

        $("#can_login").change(function() {
            if (this.checked == true) {
                $(".hideMe").slideDown();
                $('#email').attr('required', true);
            } else {
                $(".hideMe").slideUp();
                $('#email').attr('required', false);
            }
        });


        function disableDayTime(checkBox) {
            let day_name = $(checkBox).data('day');
            if (checkBox.checked == true) {
                $("#from_" + day_name).attr('disabled', 'disabled');
                $("#to_" + day_name).attr('disabled', 'disabled');
            } else {
                $("#from_" + day_name).removeAttr('disabled');
                $("#to_" + day_name).removeAttr('disabled');
            }
        }

        function enableFlexibility(checkBox) {
            let day_name = $(checkBox).data('day');
            if (checkBox.checked == true) {
                $("#from_" + day_name).attr('disabled', 'disabled');
                $("#to_" + day_name).attr('disabled', 'disabled');
                $("#working_hours" + day_name).removeAttr('disabled');
            } else {
                $("#from_" + day_name).removeAttr('disabled');
                $("#to_" + day_name).removeAttr('disabled');
                $("#working_hours" + day_name).attr('disabled', 'disabled');
            }
        }
    </script>
@endsection
