@extends('layouts.admin')
@section('content')
<form action="{{ route('admin.reports.external-payment-categories.report') }}" method="get">
    <div class="row form-group">
        <div class="col-md-6">
            <label for="date">{{ trans('global.date') }}</label>
            <div class="input-group">
                <input type="month" class="form-control" name="date" value="{{ request('date') ?? date('Y-m') }}">
                <select name="branch_id" id="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                    <option value="{{ NULL }}" selected hidden disabled>All Branches</option>
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
            <h2 class="text-center">{{ number_format($external_payment_categories->sum('external_payments_sum_amount')) }}</h2>
            <h2 class="text-center">{{ trans('global.amount') }}</h2>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5><i class="fa fa-file"></i> Other Revenue Categories Report</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center table-striped table-hover zero-configuration">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th class="text-dark">Other Revenue Category</th>
                                <th class="text-dark">{{ trans('global.count') }}</th>
                                <th class="text-dark">{{ trans('global.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($external_payment_categories as $ex_category)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $ex_category->name }}</td>
                                <td>{{ $ex_category->external_payments_count }}</td>
                                <td>{{ number_format($ex_category->external_payments_sum_amount) }}</td>
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
                        <canvas id="external_payment_category" width="400" height="400"></canvas>
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

        const ctx = document.getElementById('external_payment_category').getContext('2d');
        const external_payment_category = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [@php
                        foreach($external_payment_categories as $ex_category){
                            echo "'".$ex_category->name."'" . ',';
                        }
                @endphp],
                datasets: [{
                    label: 'External-payment-categories',
                    data: [
                        @php
                            foreach($external_payment_categories as $ex_category){
                                echo "'".$ex_category->external_payments_count."'" . ',';
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

                        foreach($external_payment_categories as $ex_category){
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