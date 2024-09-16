@extends('layouts.admin')
@section('content')
    <div class="row form-group">
        <div class="col-md-4">
            <form action="{{ route('admin.members.active') }}" method="get">
                <label for="date">{{ trans('cruds.branch.title_singular') }}</label>
                <div class="input-group">
                    <select name="branch_id" id="branch_id" class="form-control"
                        {{ $employee && $employee->branch_id != null ? 'readonly' : '' }}>
                        <option value="{{ null }}" selected>All Branches</option>
                        @foreach (\App\Models\Branch::pluck('name', 'id') as $id => $name)
                            <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-prepend">
                        <button class="btn btn-primary" type="submit">{{ trans('global.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-2 offset-md-4">
            @can('export_active_members')
                <label for="">{{ trans('global.export_excel') }}</label>
                <a href="{{ route('admin.activeMembers.export', request()->all()) }}" class="btn btn-info">
                    <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                </a>
            @endcan
        </div>

        <div class="col-md-2">
            <h4 class="text-center">{{ $memberships->count() }}</h4>
            <h4 class="text-center">{{ trans('global.active_members') }}</h4>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-check"></i> {{ trans('global.active_members') }}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover zero-configuration">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('cruds.member.title_singular') }}</th>
                                    <th>{{ trans('cruds.branch.title_singular') }}</th>
                                    <th>{{ trans('cruds.service.title_singular') }}</th>
                                    <th>{{ trans('global.start_date') }}</th>
                                    <th>{{ trans('global.end_date') }}</th>
                                    <th>{{ trans('global.amount') }}</th>
                                    <th>{{ trans('global.last_attendance') }}</th>
                                    @if (config('domains')[config('app.url')]['sports_option'] == true)
                                        <th>Sport</th>
                                    @endif
                                    <th>{{ trans('cruds.lead.fields.sales_by') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($memberships as $membership)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('admin.members.show', $membership->member_id) }}"
                                                target="_blank">
                                                {{ $membership->member->name ?? '-' }}
                                            </a>
                                            <span class="d-block font-weight-bold">
                                                {{ \App\Models\Setting::first()->member_prefix . $membership->member->member_code ?? '-' }}
                                            </span>
                                            <span class="d-block font-weight-bold">
                                                {{ $membership->member->phone ?? '-' }}
                                            </span>
                                        </td>
                                        <td>{{ $membership->member->branch->name ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('admin.memberships.show', $membership->id) }}"
                                                target="_blank">
                                                {{ $membership->service_pricelist->name ?? '-' }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $membership->start_date ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $membership->end_date ?? '-' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.invoices.show', $membership->invoice->id) }}">
                                                <span class="d-block">
                                                    {{ trans('global.total') }} :
                                                    {{ $membership->invoice->net_amount ?? 0 }}
                                                </span>
                                                <span class="d-block">
                                                    {{ trans('invoices::invoice.paid') }} :
                                                    {{ $membership->invoice->payments->sum('amount') ?? 0 }}
                                                </span>
                                                <span class="d-block">
                                                    {{ trans('global.rest') }} : {{ $membership->invoice->rest ?? 0 }}
                                                </span>
                                            </a>
                                        </td>
                                        <td>
                                            {!! $membership->last_attendance ?? '<span class="badge badge-danger">No attendance</span>' !!}
                                        </td>
                                        @if (config('domains')[config('app.url')]['sports_option'] == true)
                                            <td>{{ $membership->member->sport->name ?? '-' }}</td>
                                        @endif
                                        <td>
                                            {{ $membership->sales_by->name ?? '-' }}
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
