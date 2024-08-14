@extends('layouts.admin')
@section('content')
    <div class="row my-2">
        {{-- <div class="card">
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-3">
                        <label for="sales_by_id">Sales By</label>
                        <select name="sales_by_id" id="sales_by_id" class="form-control select2" multiple>
                            @foreach ($sales as $sale_id => $sale_name)
                                <option value="{{ $sale_id }}">{{ $sale_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="sales_by_id">Sales By</label>
                        <select name="sales_by_id" id="sales_by_id" class="form-control select2" multiple>
                            @foreach ($sales as $sale_id => $sale_name)
                                <option value="{{ $sale_id }}">{{ $sale_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="branch_id">Branch</label>
                        <select name="branch_id" id="branch_id" class="form-control select2" multiple>
                            @foreach ($branches as $branch_id => $branch_name)
                                <option value="{{ $branch_id }}">{{ $branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="service_type_id">Service Type</label>
                        <select name="service_type_id" id="service_type_id" class="form-control select2" multiple>
                            @foreach ($service_types as $service_type_id => $service_type_name)
                                <option value="{{ $service_type_id }}">{{ $service_type_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="col-md-10">
            @include('admin_includes.filters', [
                'columns' => [
                    // 'start_date' => ['label' => trans('global.start_date'), 'type' => 'date', 'from_and_to' => true, 'related_to' => 'memberships'],
                    'member_code'       => ['label' => 'Member Code', 'type' => 'text','related_to' => 'member'],
                    'phone'             => ['label' => 'Member Phone', 'type' => 'text','related_to' => 'member'],
                    'name'              => ['label' => 'Member Name', 'type' => 'text','related_to' => 'member'],
                    'service_type_id'   => ['label' => 'Service Type', 'type' => 'select', 'data' => $service_types,'related_to' => 'service_pricelist.service'],
                    'branch_id'         => ['label' => 'Branch', 'type' => 'select', 'data' => $branches,'related_to' => 'member'],
                    'sales_by_id'       => ['label' => 'Sales By', 'type' => 'select', 'data' => $sales],
                    'status'            => ['label' => 'Status', 'type' => 'select','data' => \App\Models\Membership::SELECT_STATUS],
                    'end_date'          => ['label' => trans('global.end_date'), 'type' => 'date', 'from_and_to' => true],
                    // 'created_at'        => ['label' => trans('global.created_at'), 'type' => 'date', 'from_and_to' => true]
                ],
                'route' => 'admin.membership.expiring-expired'
            ])

            @can('export_expiring_memberships')
                <a href="{{ route('admin.export-expiring-expired', request()->all()) }}" class="btn btn-info">
                    <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                </a>
            @endcan

        </div>
        <div class="col-md-2">
            <h2 class="text-center">{{ number_format($expiring_memberships->count()) }}</h2>
            <h2 class="text-center">{{ trans('cruds.membership.title_singular') }}</h2>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5><i class="fa fa-user-times"></i> Expiring & Expired Memberships</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered zero-configuration">
                            <thead>
                                <th>#</th>

                                <th>
                                    {{ trans('cruds.membership.fields.member') }}
                                </th>
                                <th>
                                    {{ trans('cruds.membership.fields.start_date') }}
                                </th>
                                <th>
                                    {{ trans('cruds.membership.fields.end_date') }}
                                </th>
                                <th>
                                    Trainer
                                </th>
                                <th>
                                    {{ trans('cruds.membership.fields.trainer') }}
                                </th>
                                <th>
                                    {{ trans('cruds.membership.fields.service') }}
                                </th>
                                <th>
                                    {{ trans('cruds.service.fields.service_type') }}
                                </th>
                                <th>{{ trans('cruds.status.title_singular') }}</th>
                                <th>
                                    {{ trans('cruds.branch.title_singular') }}
                                </th>
                                <th>
                                    {{ trans('cruds.membership.fields.sales_by') }}
                                </th>
                                <th>
                                    {{ trans('global.remaining_sessions') }}
                                </th>
                                <th>
                                    {{ trans('cruds.membership.fields.created_at') }}
                                </th>
                            </thead>
                            <tbody>
                                @foreach ($expiring_memberships as $membership)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td> 
                                            <a href="{{ route('admin.members.show', $membership->member_id) }}"
                                                target="_blank">{{ $membership->member->branch->member_prefix . '' . $membership->member->member_code }}
                                                <strong>{{ $membership->member->name ?? '-' }}</strong> <br> 
                                                <strong>{{ $membership->member->phone ?? '-' }}</strong> 
                                            </a> 
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $membership->start_date ?? '' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-danger">
                                                {{ $membership->end_date ?? '' }}
                                            </span>
                                        </td>
                                        <td>
                                            {!! $membership->trainer->name ?? '<span class="badge badge-danger">No Trainer</span>' !!}
                                        </td>
                                        <td>
                                            {!! $membership->assigned_coach->name ?? '<span class="badge badge-danger">No Trainer</span>' !!}
                                            <br>
                                            {{ $membership->assign_date ?? '' }}
                                        </td>
                                        <td>
                                            {{ $membership->service_pricelist->name ?? '-' }}
                                            <br>
                                            {{ $membership->service_pricelist->service->service_type->session_type ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $membership->service_pricelist->service->service_type->name ?? '-' }}
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ App\Models\Membership::STATUS[$membership->status] }}">
                                                {{ App\Models\Membership::SELECT_STATUS[$membership->status] }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $membership->member->branch->name ?? '-' }}
                                        </td>
                                        <td>{{ $membership->sales_by->name ?? '-' }}</td>
                                        <td>
                                            @if ($membership->service_pricelist->service->service_type->session_type == 'non_sessions')
                                                {{ $membership->attendances_count . ' \ ' . $membership->service_pricelist->session_count }}
                                            @else
                                                {{ $membership->trainer_attendances_count . ' \ ' . $membership->service_pricelist->session_count }}
                                            @endif
                                        </td>
                                        <td>{{ $membership->created_at->toFormattedDateString() ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- {{ $memberships->appends(request()->all())->links() }} --}}
                </div>
            </div>
        </div>
    </div>
@endsection
