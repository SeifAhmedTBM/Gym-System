@extends('layouts.admin')
@section('content')
    <a href="{{ route('admin.reports.trainerCommissions.report') }}" class="btn btn-danger mb-2">
        <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back') }}
    </a>
    <div class="card shadow-sm">
        <div class="card-header">
            <h5><i class="fas fa-dumbbell"></i> {{ trans('global.trainer_commissions') }} | {{ $trainer->name }} | {{ request()->date }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered text-center table-hover table-outline">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Session Name</th>
                                <th>Time</th>
                                <th>Date</th>
                                <th>Attendance Count</th>
                                <th>Commission Type</th>
                                <th>Commission Amount</th>
                                <th>Total Session Cost</th>
                                <th>Total Session Comission</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                                $total_session_cost = 0;
                                $total_comission = 0;
                            @endphp
                            @foreach ($sessions_array as $schedule_id => $dates)
                                @foreach ($dates as $day=> $memberships)
                                @php
                                    $schedule = \App\Models\Schedule::find($schedule_id);
                                @endphp
                                <tr>
                                    <td> {{ $i }}</td>
                                    <td>
                                        <a href="{{route('admin.reports.showSessionAttendances.report',[
                                            'trainer_id'=>$trainer->id,
                                            'schedule_id'=>$schedule_id,
                                            'session_date'=>$day])}}">
                                        {{ $schedule->session->name }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ date('h:i A',strtotime($schedule->timeslot->from)) }} 
                                        <br> 
                                        {{ date('h:i A',strtotime($schedule->timeslot->to)) }} 
                                    </td>
                                    <td>{{$day}}</td>
                                    <td>{{count($memberships)}}</td>
                                    <td> {{ $schedule->comission_type }} </td>
                                    <td> 
                                        {{$schedule->comission_amount }} 
                                        @if($schedule->comission_type == 'fixed')
                                        LE 
                                        @else
                                        %
                                        @endif
                                     </td>
                                     @php
                                        $invoices_cost = 0;
                                        foreach ($memberships as $key => $membership_id) {
                                            $membership = \App\Models\Membership::find($membership_id);
                                            if($membership->service_pricelist->session_count > 0 ){
                                                $invoices_cost += round($membership->invoice->net_amount/$membership->service_pricelist->session_count);
                                            }
                                        }
                                        if($schedule->comission_type == 'fixed'){
                                            $total_session_cost += $invoices_cost;
                                            $total_comission += $schedule->comission_amount;
                                        }else{
                                            $total_session_cost += $invoices_cost;
                                            $total_comission += ($invoices_cost*$schedule->comission_amount)/100;
                                        }
                                       
                                     @endphp
                                    <td> 
                                        {{round($invoices_cost)}} LE
                                    </td>
                                    <td> 
                                        @if ($schedule->comission_type == 'fixed')
                                        {{$schedule->comission_amount}}
                                        @else
                                            {{round(($invoices_cost*$schedule->comission_amount)/100)}}
                                        @endif 
                                     </td>
                                </tr>
                                @php
                                    $i=$i+1;
                                @endphp
                                @endforeach
                            @endforeach
                            
                        </tbody>
                        <tfoot>
                            <tr class="text-center">
                                <td colspan="7"></td>
                                <td>{{round($total_session_cost)}}</td>
                                <td class="bg-secondary font-weight-bold">{{round($total_comission)}} EGP</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

  

  
@endsection
