@extends('layouts.admin')
@section('content')
    
    <form action="{{ route('admin.reports.yearlyFinance.report') }}" method="get">
        <div class="form-group row">
            <div class="input-group align-items-end ">
                <div class=" col">
                    <label for="yearInput">Year</label>
{{--                    <input type="" class="form-control" name="year" value="{{ request()->year ?? date('Y') }}">--}}
                    <input type="number" id="yearInput" class="form-control" min="1000" max="9999" name="year" value="{{ request()->year ?? date('Y') }}" maxlength="4">
                </div>

           <div class="col">
               <label for="branch_id">{{ trans('cruds.branch.title_singular') }}</label>
               <select name="branch_id" id="branch_id" class="form-control" {{ $employee && $employee->branch_id != NULL ? 'readonly' : '' }}>
                   <option value="{{ NULL }}" selected>All Branch</option>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const yearInput = document.getElementById('yearInput');

            // Function to limit input to 4 digits
            function validateYearInput(event) {
                const inputValue = event.target.value;
                if (inputValue.length > 4) {
                    event.target.value = inputValue.slice(0, 4);
                }
            }

            yearInput.addEventListener('input', validateYearInput);
        });
    </script>
@endsection
