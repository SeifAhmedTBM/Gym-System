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
                    <h2 class="text-center">{{ count($membership_schedules) ?? 0 }}</h2>
                </div>
            </div>
        </div>
</div>


<div class="card">
    <div class="card-header">
        <h5>Employee {{$user->name ?? '-'}} in {{date('Y-m',strtotime($payroll->created_at))}} </h5>

       

    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Member</th>
                        <th>Session Name</th>
                        <th>Sessions Count</th>
                        <th>Consumed Count</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Invoice Amount</th>
                        <th>Percentage</th>
                        <th>Amount</th>
                        <th>Invoice Date</td>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach($membership_schedules as $msch)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>
                            {{$msch->membership->member->member_code ?? '-' }}
                             <br> 
                             {{$msch->membership->member->name ?? '-' }}
                            <br>
                            {{$msch->membership->member->phone ?? '-' }}
                        </td>
                        <td>
                            {{ $msch->schedule->session->name ?? '-' }}
                        </td>
                        <td>
                            {{ $msch->membership->service_pricelist->session_count ?? '-' }}
                        </td>
                        <td>
                            {{ $msch->membership->attendances->count() ?? 0 }}
                        </td>
                        <td>
                            {{ $msch->membership->start_date ?? '-' }}
                        </td>
                        <td>
                            {{ $msch->membership->end_date ?? '-' }}
                        </td>
                        <td>
                            {{ $msch->membership->invoice->net_amount ?? '-' }}
                        </td>
                        <td>
                            {{ $msch->schedule->comission_amount ?? 0 }}  % </td>
                        </td>
                        <td>
                            {{ ($msch->schedule->comission_amount * $msch->membership->invoice->net_amount)/100  }}  LE                       </td>
                        </td>
                        <td>{{ date('Y-m-d',strtotime( $msch->membership->invoice->created_at )) }}  </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection