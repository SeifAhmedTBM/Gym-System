@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5><i class="fa fa-file"></i> {{ trans('global.expenses_report') }}</h5>
            </div>
            <div class="card-body">
                {!! Form::open(['method' => 'GET', 'url' => route('admin.reports.expenses.report')]) !!}
                <div class="form-row">
                    <div class="col-md-6 form-group">
                        {!! Form::label('month', trans('global.month'), ['class' => 'required']) !!}
                        <div class="input-group">
                            <input type="month" value="{{ request('date') ?? date('Y-m') }}" name="date" class="form-control">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-filter"></i>
                                </button>
                            </div>
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
                            @foreach ($expensesCategory as $expensesCat)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $expensesCat->name }}</td>
                                    <td>{{ $expensesCat->expenses_count }}</td>
                                    <td>{{ $expensesCat->expenses()->sum('amount') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-right font-weight-bold">
                {{ trans('global.total') }} : {{ $expensesCategory->sum('expenses_count') }} {{ trans('cruds.expense.title') }}
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
                    foreach($expensesCategory as $expensesCat){
                        echo "'".$expensesCat->name."'" . ',';
                    }
                @endphp],
            datasets: [{
                label: '# of Votes',
                data: [
                    @php 
                        foreach($expensesCategory as $expensesCat){
                            echo "'".$expensesCat->expenses_count."'" . ',';
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

                    foreach($expensesCategory as $expensesCat){
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