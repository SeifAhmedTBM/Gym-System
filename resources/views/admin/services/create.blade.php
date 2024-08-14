@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.create') }} {{ trans('cruds.service.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.services.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="row form-group">
                <div class="col-md-6">
                    <label class="required" for="name">{{ trans('cruds.service.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.service.fields.name_helper') }}</span>
                </div>
                <div class="col-md-6">
                    <label class="required" for="expiry">{{ trans('cruds.service.fields.expiry') }}</label>
                    <div class="input-group">
                        <input class="form-control {{ $errors->has('expiry') ? 'is-invalid' : '' }}" type="number" name="expiry" id="expiry" value="{{ old('expiry', '') }}" step="0.01" required>
                        <div class="input-group-append">
                            <select name="type" class="form-control">
                                @foreach (App\Models\Service::EXPIRY_TYPES as $db_type => $type_name)
                                    <option value="{{ $db_type }}">{{ $type_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if($errors->has('expiry'))
                        <div class="invalid-feedback">
                            {{ $errors->first('expiry') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.service.fields.expiry_helper') }}</span>
                </div>
                
            </div>

            <div class="row form-group">
                <div class="col-md-6">
                    <label class="required" for="service_type_id">{{ trans('cruds.service.fields.service_type') }}</label>
                    <select class="form-control select2 {{ $errors->has('service_type') ? 'is-invalid' : '' }}" name="service_type_id" id="service_type_id" required>
                        @foreach($service_types as $id => $entry)
                            <option value="{{ $id }}" {{ old('service_type_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('service_type'))
                        <div class="invalid-feedback">
                            {{ $errors->first('service_type') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.service.fields.service_type_helper') }}</span>
                </div>
                <input type="hidden" name="status" value="active">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="required" for="order">{{ trans('cruds.employee.fields.order') }}</label>
                        <input class="form-control {{ $errors->has('order') ? 'is-invalid' : '' }}" type="number" name="order" id="order" value="{{ old('order', '0') }}" step="1" required>
                        @if($errors->has('order'))
                            <div class="invalid-feedback">
                                {{ $errors->first('order') }}
                            </div>
                        @endif
                        <span class="help-block">{{ trans('cruds.employee.fields.order_helper') }}</span>
                    </div>
                </div>
            </div>

            <div class=" form-group row">
                <div class="col-md-6">
                    <label class="required" for="trainer">{{ trans('cruds.service.fields.trainer') }}</label>
                    <select name="trainer" id="trainer" class="form-control select2 {{ $errors->has('trainer') ? 'is-invalid' : '' }}">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                    @if($errors->has('trainer'))
                        <div class="invalid-feedback">
                            {{ $errors->first('trainer') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.service.fields.trainer_helper') }}</span>
                </div>
                <div class="col-md-6">
                    <label class="required" for="sales_commission">{{ trans('global.commission') }}</label>
                    <select name="sales_commission" id="sales_commission" class="form-control select2 {{ $errors->has('sales_commission') ? 'is-invalid' : '' }}">
                        @foreach (\App\Models\Service::SALES_COMMISSIONS as $id => $entry)
                            <option value="{{ $id }}">{{ $entry }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('sales_commission'))
                        <div class="invalid-feedback">
                            {{ $errors->first('sales_commission') }}
                        </div>
                    @endif
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