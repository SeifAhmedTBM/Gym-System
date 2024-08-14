@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-warning text-center ">
            <h4><i class="fas fa-exclamation-triangle"></i> {{ trans('global.services_report_alert') }}</h4>
        </div>
    </div>
</div>
<form action="{{ route('admin.reports.services') }}" method="get">
    <div class="row my-3">
        <div class="col-md-3">
            <label for="date">{{ trans('global.date') }}</label>
            <div class="input-group">
                <input type="month" class="form-control" name="date" value="{{ request('date') ?? date('Y-m') }}">
                <div class="input-group-prepend">
                    <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                </div>
            </div>
        </div>

        <div class="col-md-3 offset-md-6">
            <h3 class="text-center">{{ trans('global.total_income') }}</h3>
            <h3 class="text-center">{{ number_format($report->sum('payments')) }} EGP</h3>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5><i class="fa fa-file"></i> {{ trans('global.services_report') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th class="text-dark">{{ trans('cruds.service.fields.name') }}</th>
                                <th class="text-dark">{{ trans('global.count') }}</th>
                                <th class="text-dark">{{ trans('global.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $total_amount = 0;
                                $total_count = 0;
                            ?>
                            @foreach ($report as $rep)
                                @php
                                    $total_amount = $total_amount + $rep['payments'];
                                    $total_count = $total_count+$rep['memberships_count'];
                                @endphp
                               <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $rep['service_name'] }}</td>
                                    <td>{{ $rep['memberships_count'] }}</td>
                                    <td>{{ number_format($rep['payments']) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>Total</td>
                                <td></td>
                                <td>{{ number_format($total_count) }}</td>
                                <td>{{ number_format($total_amount) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Chart</h5>
            </div>
            <div class="card-body">
                <div class="col">
                    <div class="card-body">
                        <canvas id="services" width="400" height="400"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    
    const ctx = document.getElementById('services').getContext('2d');
    const services = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [@php 
                    foreach($report as $rep){
                        echo "'".$rep['service_name']."'" . ',';
                    }
             @endphp],
            datasets: [{
                label: 'Services',
                data: [
                    @php 
                        foreach($report as $rep){
                            echo "'".$rep['memberships_count']."'" . ',';
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

                    foreach($report as $rep){
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