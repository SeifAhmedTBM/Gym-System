@extends('layouts.admin')
@section('content')
<div class="mb-2">
    @include('admin_includes.filters', [
    'columns' => [
        // 'name' => ['label' => 'Name', 'type' => 'text', 'related_to' => 'member'],
        // 'phone' => ['label' => 'Member Phone', 'type' => 'number', 'related_to' => 'member'],
        'member_code' => ['label' => 'Member Code', 'type' => 'number', 'related_to' => 'member'],
        // 'gender'     => ['label' => trans('global.gender'), 'type' => 'text', 'related_to' => 'member'],
        'service_id' => ['label' => trans('cruds.service.title_singular'), 'type' => 'select', 'data' => $services],
        'sales_by_id' => ['label' => trans('cruds.lead.fields.sales_by'), 'type' => 'select', 'data' => $sales],
        'start_date' => ['label' => trans('global.start_date'), 'type' => 'date', 'from_and_to' => true],
        'end_date' => ['label' => trans('global.end_date'), 'type' => 'date', 'from_and_to' => true],
    ],
    'route' => 'admin.reports.current-memberships'
    ])
    <a href="{{ route('admin.reports.export-current-memberships') }}" class="btn btn-success">
        <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
    </a>
</div>
<div class="card">
    <div class="card-header font-weight-bold">
        <i class="fa fa-list"></i> {{ trans('global.current_memberships') }}
    </div>
    <div class="card-body">
        <h3 class="text-right">
            {{ trans('global.total') }} : {{ $memberships->total() }}
        </h3>
        <div class="table-responsive">
            <table class="table table-bordered text-center table-outline table-hover">
                <thead class="thead-light">
                    <tr>
                        <th class="font-weight-bold text-dark">#</th>
                        <th class="font-weight-bold text-dark">{{ trans('cruds.member.title_singular') }}</th>
                        <th class="font-weight-bold text-dark">{{ trans('global.start_date') }}</th>
                        <th class="font-weight-bold text-dark">{{ trans('global.end_date') }}</th>
                        <th class="font-weight-bold text-dark">{{ trans('global.status') }}</th>
                        <th class="font-weight-bold text-dark">{{ trans('cruds.service.title_singular') }}</th>
                        <th class="font-weight-bold text-dark">{{ trans('cruds.lead.fields.sales_by') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($memberships as $membership)
                        <tr class="font-weight-bold">
                            <td>#{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('admin.members.show', $membership->member_id) }}" target="_blank" class="text-decoration-none">
                                    {{ App\Models\Setting::first()->member_prefix . $membership->member->member_code }} <br>
                                    {{ $membership->member->name }} <br>
                                    {{ $membership->member->phone }} <br>
                                </a>
                                {{ ucfirst($membership->member->gender) }}
                            </td>
                            <td>{{ $membership->start_date }}</td>
                            <td>{{ $membership->end_date }}</td>
                            <td>
                                <span class="badge badge-success px-3 py-3">
                                    {{ ucfirst($membership->membership_status) }}
                                </span>
                            </td>
                            <td>
                                {{ $membership->service_pricelist->service->name }} <br>
                                <span class="badge badge-danger px-1 py-1">
                                    {{ $membership->service_pricelist->service->service_type->name }}
                                </span>
                            </td>
                            <td>{{ $membership->sales_by->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <span class="float-right">
            {{ $memberships->appends(request()->all())->links() }}
        </span>
    </div>
</div>
@endsection