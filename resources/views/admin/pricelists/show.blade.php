@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.pricelist.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.pricelists.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.pricelist.fields.id') }}
                        </th>
                        <td>
                            {{ $pricelist->id }}
                        </td>
                    </tr>
                    
                    <tr>
                        <th>
                            {{ trans('cruds.pricelist.fields.service') }}
                        </th>
                        <td>
                            {{ $pricelist->service->name ?? '' }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ trans('cruds.pricelist.fields.freeze_count') }}
                        </th>
                        <td>
                            {{ $pricelist->freeze_count ?? '' }}
                        </td>
                    </tr>
                    
                    <tr>
                        <th>
                            {{ trans('cruds.pricelist.fields.invitation_count') }}
                        </th>
                        <td>
                            {{ $pricelist->invitation_count ?? '' }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ trans('cruds.pricelist.fields.spa_count') }}
                        </th>
                        <td>
                            {{ $pricelist->spa_count ?? '' }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ trans('cruds.pricelist.fields.session_count') }}
                        </th>
                        <td>
                            {{ $pricelist->session_count ?? '' }}
                        </td>
                    </tr>

                    <tr>
                        <th>
                            {{ trans('cruds.pricelist.fields.amount') }}
                        </th>
                        <td>
                            {{ $pricelist->amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.pricelist.fields.status') }}
                        </th>
                        <td>
                            {{ $pricelist->status ?? '' }}
                        </td>
                    </tr>

                    @if ($pricelist->pricelist_days_count > 0)
                        <tr>
                            <th>
                                {{ trans('global.days') }}
                            </th>
                            <td>
                                @foreach ($pricelist->pricelist_days as $key => $day)
                                    <span class="badge badge-info p-2">{{ \App\Models\PricelistDays::DAYS[$day->day] }}</span>
                                @endforeach
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.pricelists.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection