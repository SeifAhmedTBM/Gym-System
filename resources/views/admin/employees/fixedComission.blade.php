@extends('layouts.admin')
@section('content')
    <div class="row form-group">
        <div class="col-lg-6">


        </div>
        <div class="col-lg-3 col-md-2 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-center">Total</h2>
                    <h2 class="text-center">{{ $total_comissions ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-2 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h2 class="text-center">Count</h2>
                    <h2 class="text-center">{{ $sessions->count() ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <h5>Employee {{ $user->name ?? '-' }} in {{ date('Y-m', strtotime($payroll->created_at)) }} </h5>



        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Session Name</th>
                            <th>Session Date</th>
                            <th>Session Comission</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($sessions as $attend)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $attend->schedule->session->name ?? '-' }} <br>
                                    {{ date('h:i A', strtotime($attend->schedule->timeslot->from)) ?? '-' }}</td>
                                <td>{{ date('Y-m-d', strtotime($attend->created_at)) ?? '-' }}</td>
                                <td>{{ $attend->schedule->comission_amount }} EGP </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
