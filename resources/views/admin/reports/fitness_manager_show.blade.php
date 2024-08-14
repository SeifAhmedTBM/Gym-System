@extends('layouts.admin')
@section('content')
    <div class="form-group">
        <form action="{{ URL::current() }}" method="get">
            <div class="row">
                <div class="col-md-6">
                    <label for="date">{{ trans('global.date') }}</label>
                    <div class="input-group">
                        <input type="date" class="form-control" name="from" value="{{ request('from') ?? date('Y-m-01') }}">
                        <input type="date" class="form-control" name="to" value="{{ request('to') ?? date('Y-m-t') }}">
                        <select name="trainer_id" class="form-control">
                            <option value="{{ NULL }}" selected>Trainer</option>
                            @foreach ($trainers as $trainer_id => $trainer_name)
                                <option value="{{ $trainer_id }}" {{ $trainer_id == request('trainer_id') ? 'selected' : '' }}>{{ $trainer_name }}</option>
                            @endforeach
                        </select>
                        <div class="input-group-prepend">
                            <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="form-group row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4>Memberships</h4>
                    <h4>{{ number_format($memberships->count()) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4>Assigned Memberships</h4>
                    <h4>{{ number_format($assigned_memberships->count()) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4>Unassigned Memberships</h4>
                    <h4>{{ number_format($unassigned_memberships->count()) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="card">
            <div class="card-body">
                <div class="nav nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active" id="assigned_memberships-tab" data-toggle="pill" href="#assigned_memberships"
                        role="tab" aria-controls="assigned_memberships" aria-selected="true">
                        Assigned Memberships
                    </a>
                    <a class="nav-link" id="unassigned_memberships-tab" data-toggle="pill" href="#unassigned_memberships"
                        role="tab" aria-controls="unassigned_memberships" aria-selected="true">
                        Unassigned Memberships
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4><i class="fa fa-user"></i> {{ $fitness_manager->name }}</h4>
            </div>
            <div class="card-body">
                <div class="tab-content" id="v-pills-tabContent">
                    <div class="tab-pane fade show active" id="assigned_memberships" role="tabpanel" aria-labelledby="assigned_memberships-tab">
                        <div class="form-group">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-striped table-bordered table-hover zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Member</th>
                                                <th>Membership</th>
                                                <th>Assigned Date</th>
                                                <th>Coach</th>
                                                <td>Created At</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           @foreach ($assigned_memberships as $assigned_membership)
                                               <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.members.show',[$assigned_membership->member_id]) }}" target="_blank">
                                                            {{ ($assigned_membership->member->branch->member_prefix ?? '-').($assigned_membership->member->member_code) }} <br>
                                                            {{ $assigned_membership->member->name ?? '-' }} <br>
                                                            {{ $assigned_membership->member->phone ?? '-' }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        {{ $assigned_membership->service_pricelist->name ?? '-' }} <br>
                                                        <span class="badge badge-{{ App\Models\Membership::STATUS[$assigned_membership->status] }}">
                                                            {{ App\Models\Membership::SELECT_STATUS[$assigned_membership->status] }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $assigned_membership->assign_date ?? '-' }}</td>
                                                    <td>{{ $assigned_membership->assigned_coach->name ?? '-' }}</td>
                                                    <td>{{ $assigned_membership->created_at }}</td>
                                               </tr>
                                           @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="unassigned_memberships" role="tabpanel" aria-labelledby="unassigned_memberships-tab">
                        <div class="form-group">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-striped table-bordered table-hover zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Member</th>
                                                <th>Membership</th>
                                                <th>Assigned Date</th>
                                                <th>Coach</th>
                                                <th>Created At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           @foreach ($unassigned_memberships as $unassigned_membership)
                                               <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.members.show',[$unassigned_membership->member_id]) }}" target="_blank">
                                                            {{ ($unassigned_membership->member->branch->member_prefix ?? '-').($unassigned_membership->member->member_code) }} <br>
                                                            {{ $unassigned_membership->member->name ?? '-' }} <br>
                                                            {{ $unassigned_membership->member->phone ?? '-' }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        {{ $unassigned_membership->service_pricelist->name ?? '-' }} <br>
                                                        <span class="badge badge-{{ App\Models\Membership::STATUS[$unassigned_membership->status] }}">
                                                            {{ App\Models\Membership::SELECT_STATUS[$unassigned_membership->status] }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $unassigned_membership->assign_date ?? '-' }}</td>
                                                    <td>{{ $unassigned_membership->assigned_coach->name ?? '-' }}</td>
                                                    <td>{{ $assigned_membership->created_at }}</td>
                                               </tr>
                                           @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection