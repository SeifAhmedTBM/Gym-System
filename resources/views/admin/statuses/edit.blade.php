@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.edit') }} {{ trans('cruds.status.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.statuses.update", [$status->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="row form-group">
                <div class="col-md-6">
                    <label class="required" for="name">{{ trans('cruds.status.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $status->name) }}" required>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.status.fields.name_helper') }}</span>
                </div>
                <div class="col-md-6">
                    <label class="required" for="color">{{ trans('cruds.status.fields.color') }}</label>
                    <select name="color" id="color" class="form-control {{ $errors->has('color') ? 'is-invalid' : '' }}" required>
                        <option value="primary" {{ $status->color == 'primary' ? 'selected' : '' }}>Blue</option>
                        <option value="info" {{ $status->color == 'info' ? 'selected' : '' }}>Sky Blue</option>
                        <option value="success" {{ $status->color == 'success' ? 'selected' : '' }}>Green</option>
                        <option value="warning" {{ $status->color == 'warning' ? 'selected' : '' }}>Yellow</option>
                        <option value="dark" {{ $status->color == 'dark' ? 'selected' : '' }}>Black</option>
                        <option value="danger" {{ $status->color == 'danger' ? 'selected' : '' }}>Red</option>
                        <option value="secondary" {{ $status->color == 'secondary' ? 'selected' : '' }}>Grey</option>
                    </select>
                    @if($errors->has('color'))
                        <div class="invalid-feedback">
                            {{ $errors->first('color') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.status.fields.color_helper') }}</span>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required">{{ trans('cruds.memberStatus.fields.need_followup') }}</label>
                    <select class="form-control {{ $errors->has('need_followup') ? 'is-invalid' : '' }}" name="need_followup" id="need_followup" required>
                        <option value disabled {{ old('need_followup', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                        @foreach(App\Models\Status::NEED_FOLLOWUP_SELECT as $key => $label)
                            <option value="{{ $key }}" {{ old('need_followup', $status->need_followup) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('need_followup'))
                        <div class="invalid-feedback">
                            {{ $errors->first('need_followup') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.memberStatus.fields.need_followup_helper') }}</span>
                </div>

                <div class="col-md-6">
                    <label class="" for="default_next_followup_days">{{ trans('cruds.memberStatus.fields.default_next_followup_days') }}</label>
                    <input class="form-control {{ $errors->has('default_next_followup_days') ? 'is-invalid' : '' }}" type="text" name="default_next_followup_days" id="default_next_followup_days" value="{{ old('default_next_followup_days', $status->default_next_followup_days) }}"  {{ $status->need_followup == 'no' ? 'required disabled' : '' }}>
                    @if($errors->has('default_next_followup_days'))
                        <div class="invalid-feedback">
                            {{ $errors->first('default_next_followup_days') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.memberStatus.fields.default_next_followup_days_helper') }}</span>
                </div>
                
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
@section('scripts')
    <script>
        $('#need_followup').on('change',function(){
            if ($('#need_followup').val() == 'no') {
                $('#default_next_followup_days').prop('disabled',true).prop('required',false);
                $('#default_next_followup_days').val('');
            }else{
                $('#default_next_followup_days').prop('disabled',false).prop('required',true);
            }
        });
    </script>
@endsection