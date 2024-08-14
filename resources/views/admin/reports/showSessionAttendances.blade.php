@extends('layouts.admin')
@section('content')
    <a href="{{ route('admin.reports.trainerCommissions.report') }}" class="btn btn-danger mb-2">
        <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back') }}
    </a>
    <div class="card shadow-sm">
        <div class="card-header">
            <h5><i class="fas fa-dumbbell"></i> {{$schedule->session->name}} | {{$schedule->timeslot->from }} Session Attendees {{$session_date}} </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered text-center table-hover table-outline">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Customer Code</th>
                                <th>Customer Name</th>
                                <th>Customer Mobile</th>
                                <th>Pricelist Name</th>
                                <th>Pricelist Amount</th>
                                <th>Pricelist Session Count</th>
                                <th>Session Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                           @php
                               $total_sessions_cost = 0;
                           @endphp
                            @foreach ($attendances as $index => $attendance)
                              <tr>
                                <td>{{$loop->iteration}}</td>
                                 <td>{{$attendance->member->member_code ?? ''}}</td>
                                 <td>{{$attendance->member->name ?? ''}}</td>
                                 <td>{{$attendance->member->phone ?? ''}}</td>
                                 <td>{{$attendance->membership->service_pricelist->name ?? ''}}</td>
                                 <td>{{$attendance->membership->service_pricelist->amount ?? ''}}</td>
                                 <td>{{$attendance->membership->service_pricelist->session_count ?? ''}}</td>
                                 @if($attendance->membership->service_pricelist->session_count > 0)
                                    <td>{{round($attendance->membership->invoice->net_amount / $attendance->membership->service_pricelist->session_count) ?? ''}}</td>
                                 @else
                                    <td> 0 </td>
                                 @endif
                              </tr>
                              @php
                                  $total_sessions_cost += round($attendance->membership->invoice->net_amount / $attendance->membership->service_pricelist->session_count);
                              @endphp
                            @endforeach
                            
                        </tbody>
                        <tfoot>
                            <tr class="text-center">
                                <td colspan="7"></td>
                                <td class="bg-secondary font-weight-bold">{{round($total_sessions_cost)}} LE</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

  

  
@endsection
