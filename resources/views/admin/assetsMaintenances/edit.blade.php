@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.edit') }} {{ trans('cruds.assetsMaintenance.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.assets-maintenances.update", [$assetsMaintenance->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="date">{{ trans('cruds.assetsMaintenance.fields.date') }}</label>
                <input class="form-control date {{ $errors->has('date') ? 'is-invalid' : '' }}" type="text" name="date" id="date" value="{{ old('date', $assetsMaintenance->date) }}" required>
                @if($errors->has('date'))
                    <div class="invalid-feedback">
                        {{ $errors->first('date') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.assetsMaintenance.fields.date_helper') }}</span>
            </div>
            @livewire('get-accounts-component', ['assetsMaintenance' => $assetsMaintenance->id])
            <div class="form-group">
                <label for="comment">{{ trans('cruds.assetsMaintenance.fields.comment') }}</label>
                <textarea class="form-control {{ $errors->has('comment') ? 'is-invalid' : '' }}" name="comment" id="comment">{{ old('comment', $assetsMaintenance->comment) }}</textarea>
                @if($errors->has('comment'))
                    <div class="invalid-feedback">
                        {{ $errors->first('comment') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.assetsMaintenance.fields.comment_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="asset_id">{{ trans('cruds.assetsMaintenance.fields.asset') }}</label>
                <select class="form-control select2 {{ $errors->has('asset') ? 'is-invalid' : '' }}" name="asset_id" id="asset_id" required>
                    @foreach($assets as $id => $entry)
                        <option value="{{ $id }}" {{ (old('asset_id') ? old('asset_id') : $assetsMaintenance->asset->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('asset'))
                    <div class="invalid-feedback">
                        {{ $errors->first('asset') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.assetsMaintenance.fields.asset_helper') }}</span>
            </div>
            <div class="form-group">
                <label class="required" for="maintence_vendor_id">{{ trans('cruds.assetsMaintenance.fields.maintence_vendor') }}</label>
                <select class="form-control select2 {{ $errors->has('maintence_vendor') ? 'is-invalid' : '' }}" name="maintence_vendor_id" id="maintence_vendor_id" required>
                    @foreach($maintence_vendors as $id => $entry)
                        <option value="{{ $id }}" {{ (old('maintence_vendor_id') ? old('maintence_vendor_id') : $assetsMaintenance->maintence_vendor->id ?? '') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('maintence_vendor'))
                    <div class="invalid-feedback">
                        {{ $errors->first('maintence_vendor') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.assetsMaintenance.fields.maintence_vendor_helper') }}</span>
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