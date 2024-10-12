@extends('layouts.admin')
@section('content')
    <form action="{{ URL::current() }}" method="get">
        <div class="form-group">
            <div class="input-group">
                <input type="date" value="{{ request('from') ?? date('Y-m-01') }}" name="from" class="form-control">
                <input type="date" value="{{ request('to') ?? date('Y-m-t') }}" name="to" class="form-control">
                <select name="branch_id" id="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                    <option value="{{ NULL }}" selected>All Branches</option>
                    @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                        <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="source_id" id="source_id" class="form-control">
                    <option value="{{ NULL }}" selected>Source</option>
                    @foreach (\App\Models\Source::pluck('name','id') as $source_id => $source_name)
                        <option value="{{ $source_id }}" {{ request('source_id') == $source_id ? 'selected' : '' }}>
                            {{ $source_name }}
                        </option>
                    @endforeach
                </select>
                <select name="sales_by_id" id="sales_by_id" class="form-control">
                    <option value="{{ NULL }}" selected>Select Sales By</option>
                    @foreach (\App\Models\User::whereRelation('roles','title','Sales')->whereRelation('employee','status','active')->pluck('name','id') as $sales_by_id => $sales_by_name)
                        <option value="{{ $sales_by_id }}" {{ request('sales_by_id') == $sales_by_id ? 'selected' : '' }}>{{ $sales_by_name }}</option>
                    @endforeach
                </select>
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-filter"></i>
                    </button>
                    <a href="{{ route('admin.reports.guest-log-report.export',request()->all()) }}" class="btn btn-info">
                        <i class="fa fa-download"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>
    @if ($latest_leads->isNotEmpty())
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="nav nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            @foreach ($latest_leads as $key => $leads)
                                <a class="nav-link {{ $loop->iteration == 1 ? 'active' : '' }}"
                                    id="{{ str_replace(' ', '_', $key) }}-tab" data-toggle="pill"
                                    href="#{{ str_replace(' ', '_', $key) }}" role="tab"
                                    aria-controls="{{ str_replace(' ', '_', $key) }}" aria-selected="true">
                                    {{ $key }}
                                    <span class="badge badge-info rounded-circle">{{ $leads->count() }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content" id="v-pills-tabContent">
                            @foreach ($latest_leads as $key => $leads)
                                <div class="tab-pane fade {{ $loop->iteration == 1 ? 'show active' : '' }}"
                                    id="{{ str_replace(' ', '_', $key) }}" role="tabpanel"
                                    aria-labelledby="{{ $key }}-tab">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table
                                                class="table table-bordered table-striped table-hover zero-configuration">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Name</th>
                                                        <th>Type</th>
                                                        <th>Sales By</th>
                                                        <th>Source</th>
                                                        <th>Branch</th>
                                                        <th>Membership</th>
                                                        <th>Paid</th>
                                                        <th>Rest</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($leads as $lead)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>
                                                                <a href="{{ $lead->type == 'member' ? route('admin.members.show', $lead->id) : route('admin.leads.show', $lead->id) }}"
                                                                    target="_blank">
                                                                    {{ $lead->last_membership ?  $lead->member_code : ' ' }}
                                                                    <br>
                                                                    {{ $lead->name }} <br>
                                                                    {{ $lead->phone }} <br>
                                                                </a>
                                                            </td>
                                                            <td>{{ $lead->type }}</td>
                                                            <td>
                                                                {{ $lead->sales_by->name ?? '-' }}
                                                            </td>
                                                            <td>
                                                                {{ $lead->source->name ?? '-' }}
                                                            </td>
                                                            <td>
                                                                {{ $lead->branch->name ?? '-' }}
                                                            </td>
                                                            <td>
                                                                {{ $lead->last_membership->service_pricelist->name ?? '-' }}
                                                                <br>
                                                                {{ $lead->last_membership ? number_format($lead->last_membership->invoice->net_amount) : ' ' }}
                                                            </td>
                                                            <td>
                                                                {{ $lead->last_membership ? number_format($lead->last_membership->invoice->payments->sum('amount')) : 0 }}
                                                            </td>
                                                            <td>
                                                                {{ $lead->last_membership ? number_format($lead->last_membership->invoice->rest) : '-' }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        NOT FOUND
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <h2 class="text-center py-5">{{ trans('global.no_data_available') }}</h2>
    @endif
@endsection