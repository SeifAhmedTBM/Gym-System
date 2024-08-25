@php
    $requestedMonth = request()->input('month');
    $dateToDisplay = $requestedMonth ? \Carbon\Carbon::parse($requestedMonth) : \Carbon\Carbon::now();
@endphp

@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="text-center">
                        {{ trans('global.monthly_finance_report') }} - {{ $dateToDisplay->format('F Y') }}
                    </h5>                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.reports.monthlyFinance.report') }}" method="get">
        <div class="form-group row">

            <div class="input-group align-items-end">
                <div class=" col">
                    <label for="month">Date</label>
                    <input type="month" class="form-control" name="month" value="{{ request()->month  ?? date('Y-m') }}">
                </div>
                <div class=" col">
                    <label for="date">{{ trans('cruds.branch.title_singular') }}</label>
                    <select name="branch_id" id="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                        <option value="{{ NULL }}" selected >All Branch</option>
                        @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                            <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>


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
                               @foreach ($data as $index => $month)
                                   <tr>
                                       <td>
                                            @if(isset(request()->month))
                                                {{date(request()->month).'-'.str_pad($index, 2, '0', STR_PAD_LEFT)}}
                                            @else
                                               {{ date('Y-m').'-'.str_pad($index, 2, '0', STR_PAD_LEFT)}}
                                            @endif
                                       </td>
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
                        foreach($data as $index => $month)
                        {
                            echo "'" . str_pad($index, 2, '0', STR_PAD_LEFT) . "'" . ',';
                        }
                    @endphp
                ],
                datasets: [{
                    label: 'Net Income',
                    data: [
                        @php
                            foreach($data as $index => $month)
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