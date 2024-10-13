@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('global.create') }} {{ trans('cruds.serviceType.title_singular') }}</h5>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('admin.service-types.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="required" for="name">{{ trans('cruds.serviceType.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name"
                        id="name" value="{{ old('name', '') }}" required>
                    @if ($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.serviceType.fields.name_helper') }}</span>
                </div>

                <div class="form-group">
                    <label for="description">{{ trans('cruds.serviceType.fields.description') }}</label>
                    <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description"
                        id="description">{{ old('description') }}</textarea>
                    @if ($errors->has('description'))
                        <div class="invalid-feedback">
                            {{ $errors->first('description') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.serviceType.fields.description_helper') }}</span>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4 mt-4">
                                <h5>Main Service</h5>
                            </div>
                            <div class="col-md-6">
                                <label class="c-switch c-switch-3d c-switch-success my-4">
                                    <input checked type="checkbox" name="main_service" id="main_service" value="true"
                                        class="c-switch-input">
                                    <span class="c-switch-slider shadow-none"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="session_type">Session Type</label>
                        <select name="session_type" id="session_type" class="form-control">
                            <option value disabled {{ old('session_type	', null) === null ? 'selected' : '' }}>
                                {{ trans('global.pleaseSelect') }}</option>
                            @foreach (App\Models\ServiceType::SESSION_TYPE as $key => $label)
                                <option value="{{ $key }}"
                                    {{ old('session_type	') === (string) $key ? 'selected' : '' }}>{{ $label }}
                                </option>
                            @endforeach
                            {{-- <option value="non_sessions">Non-Session</option>
                        <option value="sessions">Sessions</option> --}}
                        </select>
                        @if ($errors->has('session_type'))
                            <div class="invalid-feedback">
                                {{ $errors->first('session_type') }}
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4 mt-4">
                                <h5>IS PT</h5>
                            </div>
                            <div class="col-md-6">
                                <label class="c-switch c-switch-3d c-switch-success my-4">
                                    <input  type="checkbox" name="it_pt" id="it_pt" value="true"
                                        class="c-switch-input">
                                    <span class="c-switch-slider shadow-none"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4 mt-4">
                                <h5>IS CLASS  </h5>
                            </div>
                            <div class="col-md-6">
                                <label class="c-switch c-switch-3d c-switch-success my-4">
                                    <input  type="checkbox" name="isClass" id="isClass" value="true"
                                        class="c-switch-input">
                                    <span class="c-switch-slider shadow-none"></span>
                                </label>
                            </div>
                        </div>
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
