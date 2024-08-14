@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-warning text-center ">
            <h4><i class="fas fa-exclamation-triangle"></i> {{ trans('global.member_source_report_alert') }}</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5><i class="fa fa-file"></i> {{ trans('global.members_source_report') }}</h5>
            </div>
            <div class="card-body">
                {!! Form::open(['method' => 'GET', 'url' => route('admin.reports.membersSource')]) !!}
                <div class="form-group">
                    {!! Form::label('month', trans('global.month'), ['class' => 'required']) !!}
                    <div class="input-group">
                        <input type="month" value="{{ request('date') ?? date('Y-m') }}" name="date" class="form-control">
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
                {!! Form::close() !!}
                <div class="table-responsive">
                    <table class="table table-bordered text-center table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th class="text-dark">{{ trans('cruds.source.fields.name') }}</th>
                                <th class="text-dark">{{ trans('global.count') }}</th>
                                <th class="text-dark">{{ trans('global.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_sum = 0;
                                $total_count = 0;
                            @endphp
                            @foreach ($sources_members as $key => $payments)
                                @php
                                    $total_sum += $payments->sum('amount');
                                    $total_count += $payments->count();
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $key }}</td>
                                    <td>{{ $payments->count() }}</td>
                                    <td>
                                        {{ number_format($payments->sum('amount')) }} EGP
                                    </td> 
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2"></th>
                                <th>{{ number_format($total_count) }}</th>
                                <th>{{ number_format($total_sum) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
               <h5> Chart</h5>
            </div>
            <div class="card-body">
                <div class="col">
                    <canvas id="myChart" width="400" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [@php 
                    foreach($sources_members as $key => $payments){
                        echo "'".$key."'" . ',';
                    }
                @endphp],
            datasets: [{
                label: '# of Votes',
                data: [
                    @php 
                        foreach($sources_members as $payments){
                            echo "'".$payments->sum('amount')."'" . ',';
                        }
                    @endphp
                ],
                backgroundColor: [
                    @php 
                    function random_color_part() {
                        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
                    }

                    function random_color() {
                        return random_color_part() . random_color_part() . random_color_part();
                    }

                    foreach($sources_members as $payments){
                           echo "'#" . random_color() . "',";
                        }
                    @endphp
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    </script>
@endsection