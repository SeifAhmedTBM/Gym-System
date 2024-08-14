@extends('layouts.admin')
@section('content')
@if(Session::has('xl_sheet_error'))
    <div class="alert alert-danger font-weight-bold">
        <i class="fa fa-exclamation-circle"></i> {{ session('xl_sheet_error') }} .
    </div>
@endif
<div class="card shadow-sm">
    <div class="card-header">
        <h5><i class="fa fa-database"></i> {{ trans('global.data_migration') }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-9">
                <div class="alert alert-info font-weight-bold">
                    <i class="fa fa-exclamation-circle"></i> {{ trans('global.download_samples_note') }}
                </div>
            </div>
            <div class="col-md-3 text-right">
                <a href="{{ asset('XL_SHEETS.zip') }}" class="btn text-decoration-none font-weight-bold btn-primary btn-lg text-white btn-link" download>
                    <i class="fa fa-download"></i> {{ trans('global.download_samples') }}
                </a>
            </div>
        </div>
    </div>
</div>

@foreach (config('data_migration') as $route => $data_migration_item)
<div class="card shadow-sm mb-3">
    <div class="card-header font-weight-bold">
        <i class="fa fa-cogs"></i> {{ trans($data_migration_item['title']) }}
    </div>
    {!! Form::open(['method' => 'POST' , 'url' => route('admin.import.' . $route), 'files' => true]) !!}
    <div class="card-body">
        @isset($data_migration_item['notes'])
        <div class="alert alert-info font-weight-bold">
            <i class="fa fa-exclamation-circle"></i> {{ trans($data_migration_item['notes']) }} .
        </div>
        @endisset
        @isset($data_migration_item['available_roles'])
        <div class="alert alert-success font-weight-bold">
            <i class="fa fa-exclamation-circle"></i> {{ trans('global.available_roles') }}
            (
                @foreach (App\Models\Role::pluck('title') as $role)
                    <code class="mx-2">{{ $role }}</code>
                @endforeach
            )
        </div>
        @endisset
        @isset($data_migration_item['available_pricelists'])
        <div class="alert alert-success font-weight-bold">
            <i class="fa fa-exclamation-circle"></i> {{ trans('global.available_pricelists') }}
            (
                @forelse (App\Models\Pricelist::pluck('name') as $pricelist)
                    <code class="mx-2">{{ $pricelist }}</code>
                @empty
                    <code>{{ trans('global.no_data_available') }}</code>
                @endforelse
            )
        </div>
        @endisset
        <div class="form-row">
            @foreach ($data_migration_item['children'] as $key => $child)
            <div class="col-md-3">
                <div class="form-group">
                    <label for="{{ $child['name'] }}" class="d-block font-weight-bold {{ $child['is_required'] ? 'required' : '' }}">
                        {{ trans($child['title']) }}
                    </label>
                    <input type="file" {{ $child['is_required'] ? 'required' : '' }} class="form-control" name="{{ $child['name'] }}" id="{{ $child['name'] }}">
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-sm btn-primary">
            <i class="fa fa-check-circle"></i> {{ trans('global.submit') }}
        </button>
    </div>
    {!! Form::close() !!}
</div>
@endforeach

@endsection