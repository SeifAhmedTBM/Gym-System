@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('global.show') }} {{ trans('cruds.salesTier.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.sales-tiers.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.salesTier.fields.id') }}
                        </th>
                        <td>
                            {{ $salesTier->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesTier.fields.name') }}
                        </th>
                        <td>
                            {{ $salesTier->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('global.users') }}
                        </th>
                        <td>
                            @foreach ($salesTier->sales_tiers_users as $user)
                                <span class="badge p-2 badge-primary">
                                    {{ $user->user->name }}
                                </span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('global.ranges') }}
                        </th>
                        <td>
                            @foreach ($salesTier->sales_tiers_ranges as $range)
                                <span class="badge text-white badge-secondary p-2">
                                    {{ $range->range_from }} - TO - {{ $range->range_to }} ( {{ $range->commission }}% )
                                </span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesTier.fields.type') }}
                        </th>
                        <td>
                            {{ App\Models\SalesTier::TYPE_SELECT[$salesTier->type] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.salesTier.fields.status') }}
                        </th>
                        <td>
                            {{ App\Models\SalesTier::STATUS_SELECT[$salesTier->status] ?? '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.sales-tiers.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection