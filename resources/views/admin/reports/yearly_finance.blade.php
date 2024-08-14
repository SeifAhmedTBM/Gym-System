@extends('layouts.admin')
@section('content')
    
    <form action="{{ route('admin.reports.yearlyFinance.report') }}" method="get">
        <div class="form-group row">
            <label for="date">{{ trans('cruds.branch.title_singular') }}</label>
            <div class="input-group">
                <input type="text" class="form-control" name="year" value="{{ request()->year ?? date('Y') }}">
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
    </form>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="text-center">{{ trans('global.yearly_finance_report') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Chart</h5>
                </div>
                <div class="card-body">
                    <div class="col">
                        <div class="card-body">
                            <canvas id="yearly" width="400" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-statement">
                            <thead>
                                <tr>
                                    <th>
                                        {{ trans('global.billing.month') }}
                                    </th>
                                    <th>
                                        {{ trans('global.total_income') }}
                                    </th>
                                    <th>
                                        {{ trans('global.total_outcome') }}
                                    </th>
                                    <th>
                                        {{ trans('global.net_income') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                               @foreach ($months as $index => $month)
                                   <tr>
                                       <td>{{ date('F', mktime(0, 0, 0, $index, 1)) }}</td>
                                       <td>{{ number_format($month['total_income']) }} EGP</td>
                                       <td>{{ number_format($month['total_outcome']) }} EGP</td>
                                       <td>{{ number_format($month['net_income']) }} EGP</td>                                       
                                   </tr>
                               @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('yearly').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    @php
                        foreach($months as $index => $month)
                        {
                            echo "'" . date('F', mktime(0, 0, 0, $index, 1)) . "'" . ',';
                        }
                    @endphp
                ],
                datasets: [{
                    label: 'Net Income',
                    data: [
                        @php
                            foreach($months as $index => $month)
                            {
                                echo "'" . $month['net_income'] . "'" . ',';
                            }
                        @endphp
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
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