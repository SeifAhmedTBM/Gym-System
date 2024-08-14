@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.assetsMaintenance.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.assets-maintenances.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.assetsMaintenance.fields.id') }}
                        </th>
                        <td>
                            {{ $assetsMaintenance->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.assetsMaintenance.fields.date') }}
                        </th>
                        <td>
                            {{ $assetsMaintenance->date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.assetsMaintenance.fields.amount') }}
                        </th>
                        <td>
                            {{ $assetsMaintenance->amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.assetsMaintenance.fields.comment') }}
                        </th>
                        <td>
                            {{ $assetsMaintenance->comment }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.assetsMaintenance.fields.asset') }}
                        </th>
                        <td>
                            {{ $assetsMaintenance->asset->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.assetsMaintenance.fields.maintence_vendor') }}
                        </th>
                        <td>
                            {{ $assetsMaintenance->maintence_vendor->name ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.assets-maintenances.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection