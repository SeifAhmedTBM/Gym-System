@extends('layouts.admin')
@section('content')
{{-- <div class="mb-2">
    @can('export_trainers')
        {!! Form::open(['class' => 'd-inline', 'method' => 'POST', 'url' => route('admin.reports.trainers-report.export', request()->all())]) !!}
            <button type="submit" class="btn btn-success">
                <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
            </button>
        {!! Form::close() !!}
    @endcan
</div> --}}
<form action="{{ route('admin.reports.trainerCommissions.report') }}" method="get">
    <div class="row form-group">
        <div class="col-md-3">
            <label for="date">{{ trans('global.date') }}</label>
            <div class="input-group">
                <input type="month" class="form-control" name="date" value="{{ request('date') ?? date('Y-m') }}">
                <div class="input-group-prepend">
                    <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="card shadow-sm">
    <div class="card-header">
        <h5><i class="fas fa-dumbbell"></i> {{ trans('global.trainer_commissions') }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                <thead class="thead-light">
                    <tr>
                        <th class="text-dark">{{ trans('global.name') }}</th>
                        <th class="text-dark">{{ trans('global.session_count') }}</th>
                        <th class="text-dark">Total Sessions Cost</th>
                        <th class="text-dark">Comission Amount</th>
                        <th class="text-dark">{{ trans('global.view') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i=1;
                    @endphp
                    @forelse ($sessions_array as $trainer_id => $schedules)
                        @php

                            $sessions_count = 0;
                            $total_total_sessions_cost =0;
                            $total_comission = 0;
                        
                            foreach($schedules as $schedule_id => $days){
                                
                                $schedule_obj = \App\Models\Schedule::find($schedule_id);
                                $comission_type = $schedule_obj->comission_type;
                                $comission_amount = $schedule_obj->comission_amount;
                                $sessions_count += count($days);
                                
                                foreach ($days as $day => $memberships) {
                                    $total_sessions_cost = 0;
                                    foreach ($memberships as $key => $membership) {
                                        $membership_obj = \App\Models\Membership::find($membership);
                                        if($membership_obj->service_pricelist->session_count > 0 ){
                                            $total_sessions_cost += round($membership_obj->invoice->net_amount/$membership_obj->service_pricelist->session_count);
                                        }
                                        
                                    }
                                    if($schedule_obj->comission_type == 'fixed'){
                                        $total_total_sessions_cost += $total_sessions_cost;
                                        $total_comission += $schedule_obj->comission_amount;
                                    }else{
                                        $total_total_sessions_cost += $total_sessions_cost;
                                        $total_comission += round(($total_sessions_cost*$schedule_obj->comission_amount)/100);
                                    }
                                }
                                
                            }
                        @endphp
                        <tr>
                            <td class="font-weight-bold text-dark">{{ \App\Models\User::find($trainer_id)->name }}</td>
                            <td class="font-weight-bold text-dark">
                                {{$sessions_count}} 
                            </td>
                            <td class="font-weight-bold text-dark">{{round($total_total_sessions_cost)}} LE </td>
                            
                            <td class="font-weight-bold text-dark">{{round($total_comission)}} LE </td>
                            <td>
                                <a href="{{ route('admin.reports.showTrainerCommissions.report', [\App\Models\User::find($trainer_id)->id,'date='.request()->date]) }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-eye"></i> {{ trans('global.show') }}
                                </a>
                            </td>
                        </tr>
                        @php
                            $i++;
                        @endphp
                    @empty
                        <tr>
                            <td colspan="8">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{-- {{ $trainers->links() }} --}}
    </div>
</div>
@endsection