@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.create') }} {{ trans('cruds.serviceOptionsPricelist.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.service-options-pricelists.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="service_option_id">{{ trans('cruds.serviceOptionsPricelist.fields.service_option') }}</label>
                <select class="form-control select2 {{ $errors->has('service_option') ? 'is-invalid' : '' }}" name="service_option_id" id="service_option_id" required>
                    @foreach($service_options as $id => $entry)
                        <option value="{{ $id }}" {{ old('service_option_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('service_option'))
                    <div class="invalid-feedback">
                        {{ $errors->first('service_option') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.serviceOptionsPricelist.fields.service_option_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="pricelist_id">{{ trans('cruds.serviceOptionsPricelist.fields.pricelist') }}</label>
                <select class="form-control select2 {{ $errors->has('pricelist') ? 'is-invalid' : '' }}" name="pricelist_id" id="pricelist_id" required>
                    @foreach($pricelists as $pricelist)
                        <option value="{{ $pricelist->id }}" {{ old('pricelist_id') == $pricelist->id ? 'selected' : '' }}>{{ $pricelist->amount.'@'.$pricelist->service->name .' - '.$pricelist->service->service_type->name  ?? '-'}} </option>
                    @endforeach
                </select>
                @if($errors->has('pricelist'))
                    <div class="invalid-feedback">
                        {{ $errors->first('pricelist') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.serviceOptionsPricelist.fields.pricelist_helper') }}</span>
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