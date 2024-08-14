@extends('layouts.admin')
@section('content')
<form action="{{ route('admin.reports.refundReasons.report') }}" method="get">
    <div class="row form-group">
        <div class="col-md-6">
            <label for="date">{{ trans('global.date') }}</label>
            <div class="input-group">
                <input type="month" class="form-control" name="date" value="{{ request('date') ?? date('Y-m') }}">
                <select name="branch_id" id="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                    <option value="{{ NULL }}" selected hidden disabled>Branch</option>
                    @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                        <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <div class="input-group-prepend">
                    <button class="btn btn-primary" type="submit" >{{ trans('global.submit') }}</button>
                </div>
            </div>
        </div>

        <div class="col-md-3 offset-md-3">
            <h2 class="text-center">{{ number_format($refunds) }}</h2>
            <h2 class="text-center">{{ trans('cruds.refund.title_singular') }}</h2>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5><i class="fa fa-file"></i> {{ trans('global.refund_reason_report') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th class="text-dark">{{ trans('cruds.refundReason.title_singular') }}</th>
                                <th class="text-dark">{{ trans('global.count') }}</th>
                                <th class="text-dark">{{ trans('global.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($refundReasons as $reason)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $reason->name }}</td>
                                <td>{{ $reason->refunds_count }}</td>
                                <td>{{ number_format($reason->refunds->sum('amount')) }}</td>
                            </tr>
                            @empty
                                <td colspan="4">{{ trans('global.no_data_available') }}</td>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Chart
            </div>
            <div class="card-body">
                <div class="col">
                    <div class="card-body">
                        <canvas id="refundReasons" width="400" height="400"></canvas>
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
    
    const ctx = document.getElementById('refundReasons').getContext('2d');
    const refundReasons = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: [@php 
                    foreach($refundReasons as $reason){
                        echo "'".$reason->name."'" . ',';
                    }
             @endphp],
            datasets: [{
                label: 'refundReasons',
                data: [
                    @php 
                        foreach($refundReasons as $reason){
                            echo "'".$reason->refunds_count."'" . ',';
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

                    foreach($refundReasons as $reason){
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