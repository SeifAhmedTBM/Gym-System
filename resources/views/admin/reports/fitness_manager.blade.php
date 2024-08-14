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
                        <div class="input-group-prepend">
                            <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="form-group">
        <div class="card">
            <div class="card-header">
                Fitness Manager Report
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered table-hover zero-configuration">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Branch</th>
                            <th>All Memberships</th>
                            <th>Assigned Memberships</th>
                            <th>Unassigned Memberships</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fitness_managers as $fitness_manager)
                            <tr>
                                <td>{{ $fitness_manager['name'] }}</td>
                                <td>{{ $fitness_manager['branch_name'] }}</td>
                                <td>{{ number_format($fitness_manager['memberships_count']) }}</td>
                                <td>{{ number_format($fitness_manager['assigned_memberships_count']) }}</td>
                                <td>{{ number_format($fitness_manager['unassigned_memberships_count']) }}</td>
                                <td>
                                    <a href="{{ route('admin.reports.show-fitness-manager-report',[
                                        $fitness_manager['id'],'from' => (request('from') ?? date('Y-m-01')),'to' => (request('to') ?? date('Y-m-t'))
                                        ]) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fa fa-eye"></i> Show
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection