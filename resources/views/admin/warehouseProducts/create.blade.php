@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.create') }} {{ trans('cruds.warehouseProduct.title_singular') }}</h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.warehouse-products.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="product_id">{{ trans('cruds.warehouseProduct.fields.product') }}</label>
                <select class="form-control select2 {{ $errors->has('product') ? 'is-invalid' : '' }}" name="product_id" id="product_id">
                    @foreach($products as $id => $entry)
                        <option value="{{ $id }}" {{ old('product_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('product'))
                    <div class="invalid-feedback">
                        {{ $errors->first('product') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.warehouseProduct.fields.product_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="wharehouse_id">{{ trans('cruds.warehouseProduct.fields.wharehouse') }}</label>
                <select class="form-control select2 {{ $errors->has('wharehouse') ? 'is-invalid' : '' }}" name="wharehouse_id" id="wharehouse_id">
                    @foreach($wharehouses as $id => $entry)
                        <option value="{{ $id }}" {{ old('wharehouse_id') == $id ? 'selected' : '' }}>{{ $entry }}</option>
                    @endforeach
                </select>
                @if($errors->has('wharehouse'))
                    <div class="invalid-feedback">
                        {{ $errors->first('wharehouse') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.warehouseProduct.fields.wharehouse_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="balance">{{ trans('cruds.warehouseProduct.fields.balance') }}</label>
                <input class="form-control {{ $errors->has('balance') ? 'is-invalid' : '' }}" type="number" name="balance" id="balance" value="{{ old('balance', '0') }}" step="0.01">
                @if($errors->has('balance'))
                    <div class="invalid-feedback">
                        {{ $errors->first('balance') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.warehouseProduct.fields.balance_helper') }}</span>
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