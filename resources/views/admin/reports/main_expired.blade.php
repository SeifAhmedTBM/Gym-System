@extends('layouts.admin')
@section('content')
    <div class="row my-2">
        <div class="col-md-12">
            {{-- <form action="{{ URL::current() }}" method="get">
                <div class="input-group">
                    <select name="branch_id" id="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                        <option value="{{ NULL }}" selected>All Branches</option>
                        @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                            <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <input type="text" class="form-control" name="member_code" placeholder="Member Code" value="{{ request('member_code') ?? NULL }}">
                    <input type="text" class="form-control" name="phone" placeholder="Member Phone" value="{{ request('phone') ?? NULL }}">
                    <select name="sales_by_id" id="sales_by_id" class="form-control">
                        <option value="{{ NULL }}" selected>Sales By</option>
                        @foreach (\App\Models\User::whereRelation('roles','title','Sales')->pluck('name','id') as $sales_id => $sales_name)
                            <option value="{{ $sales_id }}" {{ request('sales_by_id') == $sales_id ? 'selected' : '' }}>{{ $sales_name }}</option>
                        @endforeach
                    </select>
                    <select name="pricelist_id" id="pricelist_id" class="form-control">
                        <option value="{{ NULL }}" selected>Pricelist</option>
                        @foreach (\App\Models\Pricelist::whereStatus('active')->pluck('name','id') as $pricelist_id => $pricelist_name)
                            <option value="{{ $pricelist_id }}" {{ request('pricelist_id') == $pricelist_id ? 'selected' : '' }}>{{ $pricelist_name }}</option>
                        @endforeach
                    </select>
                    <input type="date" class="form-control" name="date_from" value="{{ date('Y-m-01') }}">
                    <input type="date" class="form-control" name="date_to" value="{{ date('Y-m-01') }}">
                    <div class="input-group-prepend">
                        <button type="submit" class="btn btn-primary "><i class="fa fa-search"></i> Search</button>
                        <form action="{{ route('admin.export.expired.memberships',request()->all()) }}" method="post">
                            @csrf
                            <input type="hidden" name="main_service" value="{{ URL::current() == route('admin.reports.expired') ? 'true' : 'false' }}">
                            <button type="submit" class="btn btn-info text-white">
                                <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                            </button>
                        </form>
                    </div>
                </div>
            </form> --}}
            @include('admin_includes.filters', [
            'columns' => [
                'member_code'   => ['label' => 'Member Code', 'type' => 'number'],
                'sales_by_id'   => ['label' => 'Sales By', 'type' => 'select', 'data' => $sales],
                'branch_id'     => ['label' => 'Branch', 'type' => 'select', 'data' => $branches],
                'end_date'      => ['label' => trans('global.end_date'), 'type' => 'date', 'from_and_to' => true, 'related_to' => 'memberships'],
                // 'created_at' => ['label' => trans('global.created_at'), 'type' => 'date', 'from_and_to' => true]
            ],
                'route' => 'admin.reports.main-expired'
            ])
            <a href="{{ route('admin.reports.export-main-expired',request()->all()) }}" class="btn btn-info text-white">
                <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
            </a>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-2 offset-10">
            <h2 class="text-center">{{ $members->count() }}</h2>
            <h2 class="text-center">{{ trans('cruds.membership.title_singular') }}</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><i class="fa fa-user-times"></i> Main Expired</h5>
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
                                <th>{{ trans('cruds.branch.title_singular') }}</th>
                                <th>{{ trans('cruds.membership.title') }}</th>
                                <th>{{ trans('cruds.service.fields.service_type') }}</th>
                                <th>{{ trans('global.date') }}</th>
                                <th>{{ trans('global.trainer') }}</th>
                                <th>{{ trans('cruds.lead.fields.sales_by') }}</th>
                                <th>{{ trans('global.created_at') }}</th>
                            </thead>
                            <tbody>
                                @foreach ($members as $member)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('admin.members.show',$member->id) }}">
                                                {{ $member->branch->member_prefix.$member->member_code }} <br>
                                                {{ $member->name }} <br>
                                                {{ $member->phone }} <br>
                                                Memberships : {{ $member->memberships_count }}
                                            </a>
                                        </td>
                                        <td>{{ $member->branch->name ?? '-' }}</td>
                                        <td>
                                            {{ $member->memberships->first()->service_pricelist->name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $member->memberships->first()->service_pricelist->service->service_type->name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ 'Start Date : '.$member->memberships->first()->start_date ?? '-' }} <br>
                                            {{ 'End Date : '.$member->memberships->first()->end_date ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $member->memberships->first()->trainer->name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $member->memberships->first()->sales_by->name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $member->memberships->first()->created_at->toFormattedDateString() ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection