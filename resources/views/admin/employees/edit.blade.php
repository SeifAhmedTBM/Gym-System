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
            <div class="row form-group">
                <div class="col-md-12">
                    <label for="photo">{{ trans('cruds.member.fields.photo') }}</label>
                    <div class="needsclick dropzone {{ $errors->has('photo') ? 'is-invalid' : '' }}"
                         id="photo-dropzone">
                    </div>
                    @if ($errors->has('photo'))
                        <div class="invalid-feedback">
                            {{ $errors->first('photo') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.member.fields.photo_helper') }}</span>
                </div>
            </div>

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
            </div>

            <div class="form-row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('national', trans('cruds.lead.fields.national')) !!}
                        {!! Form::number('national', $employee->national, ['class' => 'form-control', 'placeholder' => trans('cruds.lead.fields.national')]) !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="target_amount">{{ trans('global.target_amount') }}</label>
                    {!! Form::number('target_amount', $employee->target_amount, ['class' => 'form-control', 'placeholder' => trans('global.target_amount')]) !!}
                </div>
                <div class="col-md-4">
                    <label for="branch_id">{{ trans('cruds.branch.title_singular') }}</label>
                    <select name="branch_id" id="branch_id" class="form-control select2">
                        @foreach ($branches as $id => $name)
                            <option value="{{ $id }}" {{ $employee->branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-4">
                    <label for="vacations_balance">{{ trans('global.vacations_balance') }}</label>
                    {!! Form::number('vacations_balance', old('vacation_balance',$employee->vacations_balance) ?? 0, ['class' => 'form-control', 'placeholder' => trans('global.vacations_balance')]) !!}
                </div>

                <div class="col-md-4">
                    <label for="access_card">{{ trans('global.access_card') }}</label>
                    {!! Form::number('access_card', old('vacation_balance',$employee->access_card) ?? 0, ['class' => 'form-control', 'placeholder' => trans('global.access_card')]) !!}
                </div>
            </div>
        </div>
    </div>
   

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

        Dropzone.options.photoDropzone = {
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
                $('form').find('input[name="photo"]').remove()
                $('form').append('<input type="hidden" name="photo" value="' + response.name + '">')
            },
            removedfile: function(file) {
                file.previewElement.remove()
                if (file.status !== 'error') {
                    $('form').find('input[name="photo"]').remove()
                    this.options.maxFiles = this.options.maxFiles + 1
                }
            },
            init: function() {
                @if (isset($member) && $member->photo)
                var file = {!! json_encode($member->photo) !!}
                this.options.addedfile.call(this, file)
                this.options.thumbnail.call(this, file, file.preview)
                file.previewElement.classList.add('dz-complete')
                $('form').append('<input type="hidden" name="photo" value="' + file.file_name + '">')
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