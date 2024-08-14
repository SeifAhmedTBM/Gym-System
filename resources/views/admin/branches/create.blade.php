@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.branch.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.branches.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="name">{{ trans('cruds.branch.fields.name') }}</label>
                    <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', '') }}" required>
                    @if($errors->has('name'))
                        <div class="invalid-feedback">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.branch.fields.name_helper') }}</span>
                </div>
                <div class="col-md-6">
                    <label class="required" for="member_prefix">{{ trans('cruds.branch.fields.member_prefix') }}</label>
                    <input class="form-control {{ $errors->has('member_prefix') ? 'is-invalid' : '' }}" type="text" name="member_prefix" id="member_prefix" value="{{ old('member_prefix', '') }}" required>
                    @if($errors->has('member_prefix'))
                        <div class="invalid-feedback">
                            {{ $errors->first('member_prefix') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.branch.fields.member_prefix_helper') }}</span>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label class="required" for="invoice_prefix">{{ trans('cruds.branch.fields.invoice_prefix') }}</label>
                    <input class="form-control {{ $errors->has('invoice_prefix') ? 'is-invalid' : '' }}" type="text" name="invoice_prefix" id="invoice_prefix" value="{{ old('invoice_prefix', '') }}" required>
                    @if($errors->has('invoice_prefix'))
                        <div class="invalid-feedback">
                            {{ $errors->first('invoice_prefix') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.branch.fields.invoice_prefix_helper') }}</span>
                </div>
                <div class="col-md-6">
                    <label class="required" for="address">{{ trans('cruds.branch.fields.address') }}</label>
                    <input class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}" type="text" name="address" id="address" value="{{ old('address', '') }}" required>
                    @if($errors->has('address'))
                        <div class="invalid-feedback">
                            {{ $errors->first('address') }}
                        </div>
                    @endif
                    <span class="help-block">{{ trans('cruds.branch.fields.address_helper') }}</span>
                </div>
                
            </div>
            <div class="form-group row">
                <div class="col-md-6">
                    <label for="sales_manager_id">Sales Manager</label>
                    <select name="sales_manager_id" id="sales_manager_id" class="form-control select2">
                        <option value="{{ NULL }}" disabled selected>{{ trans('global.pleaseSelect') }}</option>
                        @foreach ($sales_managers as $id => $name)
                            <option value="{{ $id }}" {{ old('sales_manager_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="fitness_manager_id">Fitness Manager</label>
                    <select name="fitness_manager_id" id="fitness_manager_id" class="form-control select2">
                        <option value="{{ NULL }}" disabled selected>{{ trans('global.pleaseSelect') }}</option>
                        @foreach ($fitness_managers as $id => $name)
                            <option value="{{ $id }}" {{ old('fitness_manager_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label for="">Partner Percentage</label>
                    <div class="input-group">
                        <input class="form-control {{ $errors->has('partner_percentage') ? 'is-invalid' : '' }}" type="number"
                            name="partner_percentage" id="partner_percentage" value="{{ old('partner_percentage') ?? 0 }}" required step="0.001" placeholder="Ex:10,20">
                        <div class="input-group-append">
                            <span class="input-group-text" >%</span>
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