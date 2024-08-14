@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.serviceOptionsPricelist.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.service-options-pricelists.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.serviceOptionsPricelist.fields.id') }}
                        </th>
                        <td>
                            {{ $serviceOptionsPricelist->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.serviceOptionsPricelist.fields.service_option') }}
                        </th>
                        <td>
                            {{ $serviceOptionsPricelist->pricelist->amount.'@'.$serviceOptionsPricelist->pricelist->service->name .' - '.$serviceOptionsPricelist->pricelist->service->service_type->name  ?? '-'}}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.serviceOptionsPricelist.fields.pricelist') }}
                        </th>
                        <td>
                            {{ $serviceOptionsPricelist->pricelist->amount ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.service-options-pricelists.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection