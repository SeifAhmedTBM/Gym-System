@extends('layouts.admin')
@section('content')
    <div class="row form-group">
        <div class="col-md-8">
            <form action="{{ route('admin.reports.dayuse') }}" method="get">
                <label for="date">{{ trans('cruds.branch.title_singular') }}</label>
                <div class="input-group">
                    <select name="branch_id" id="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                        <option value="{{ NULL }}" selected>All Branches</option>
                        @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                            <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <select name="sport_id" id="sport_id" class="form-control">
                        <option value="{{ NULL }}">Select Sport</option>
                        @foreach (\App\Models\Sport::pluck('name','id') as $id => $name)
                            <option value="{{ $id }}" {{ request('sport_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-prepend">
                        <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-2">
            {{-- @can('export_inactive_members')
                <label for="">{{ trans('global.export_excel') }}</label>
                <a href="{{ route('admin.inactiveMembers.export',request()->all()) }}" class="btn btn-info"><i class="fa fa-download"></i> {{ trans('global.export_excel') }}</a>
            @endcan --}}
        </div>
        
        <div class="col-md-2">
            <h4 class="text-center">{{ $members->count() }}</h4>
            <h4 class="text-center">Dayuse Members</h4>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-check-circle"></i> Dayuse Members
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover zero-configuration text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('cruds.member.title_singular') }}</th>
                                    <th>Memberships count</th>
                                    <th>{{ trans('global.sport') }}</th>
                                    <th>{{ trans('cruds.branch.title_singular') }}</th>
                                    <th>{{ trans('cruds.lead.fields.sales_by') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($members as $member)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <a href="{{ route('admin.members.show',$member->id) }}" target="_blank">
                                                {{ $member->name ?? '-' }} <br>
                                                {{ $member->phone }}
                                            </a>
                                        </td>
                                        <td>{{ $member->memberships_count }}</td>
                                        <td>{{ $member->sport->name ?? '-' }}</td>
                                        <td>{{ $member->branch->name ?? '-' }}</td>
                                        <td>{{ $member->sales_by->name ?? '-' }}</td>
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