@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5><i class="fa fa-file"></i> {{ trans('global.leads_source_report').' | '.$source->name }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ URL::current() }}" method="get">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" value="{{ request('member_code') ?? NULL }}" name="member_code" class="form-control" placeholder="Member Code">
                            <input type="text" value="{{ request('phone') ?? NULL }}" name="phone" class="form-control" placeholder="Member Phone">
                            <select name="branch_id" id="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                                <option value="{{ NULL }}" selected hidden disabled>Branch</option>
                                @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                                    <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-filter"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th class="text-dark">{{ trans('cruds.lead.title_singular') }}</th>
                                <th class="text-dark">{{ trans('cruds.branch.title_singular') }}</th>
                                <th class="text-dark">{{ trans('cruds.lead.fields.sales_by') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($leads as $key => $lead)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="d-block">{{ $lead->name }}</span>
                                        <span class="d-block">{{ $lead->phone }}</span>
                                        <span class="d-block">{{ $lead->gender }}</span>
                                    </td>
                                    <td>{{ $lead->branch->name ?? '-' }}</td>
                                    <td>{{ $lead->sales_by->name ?? '-' }}</td>
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