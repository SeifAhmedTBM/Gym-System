@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.masterCard.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.master-cards.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="required" for="master_card">{{ trans('cruds.masterCard.fields.master_card') }}</label>
                <input class="form-control {{ $errors->has('master_card') ? 'is-invalid' : '' }}" type="text" name="master_card" id="master_card" value="{{ old('master_card', '') }}" required>
                @if($errors->has('master_card'))
                    <div class="invalid-feedback">
                        {{ $errors->first('master_card') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.masterCard.fields.master_card_helper') }}</span>
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