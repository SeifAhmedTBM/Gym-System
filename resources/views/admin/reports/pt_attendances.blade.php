@extends('layouts.admin')
@section('content')
    <div class="form-group">
        <form action="{{ URL::current() }}" method="get">
            <div class="input-group">
                <input type="date" class="form-control" name="from" value="{{ request('from') ?? date('Y-m-01') }}">
                <input type="date" class="form-control" name="to" value="{{ request('to') ?? date('Y-m-t') }}">
    
                <select name="branch_id" id="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                    <option value="{{ NULL }}" selected>All Branches</option>
                    @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                        <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <div class="input-group-prepend">
                    <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                </div>
            </div>
        </form>
    </div>

    <div class="form-group">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-user"></i> PT Membership Attendances
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered table-hover zero-configuration">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Member</th>
                            <th>Membership</th>
                            <th>Branch</th>
                            <th>Coach</th>
                            <th>Assign Date</th>
                            <th>Sign In</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($membership_attendances as $attend)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <a href="{{ route('admin.members.show',[$attend->membership->member_id]) }}" target="_blank" >
                                        {{ ($attend->membership->member->branch->member_prefix ?? '-').($attend->membership->member->member_code ?? '-') }} <br>
                                        {{ $attend->membership->member->name ?? '-' }} <br>
                                        {{ $attend->membership->member->phone ?? '-' }}
                                    </a>
                                </td> 
                                <td>{{ $attend->membership->service_pricelist->name ?? '-' }}</td>       
                                <td>{{ $attend->branch->name ?? '-' }}</td>       
                                <td>{{ $attend->membership->assigned_coach->name ?? '-' }}</td>       
                                <td>{{ $attend->membership->assign_date ?? '-' }}</td>       
                                <td>{{ $attend->sign_in ?? '-' }}</td>       
                                <td>{{ $attend->created_at->format('Y-m-d') ?? '-' }}</td>       
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection