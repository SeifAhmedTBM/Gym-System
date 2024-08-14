<form action="{{ route('admin.home') }}" method="get">
    <div class="form-group row">
        <div class="col-md-6">
            <div class="input-group">
                <input type="month" class="form-control" name="date" value="{{ request('date') ?? date('Y-m') }}">
                <div class="input-group-prepend">
                    <button class="btn btn-primary" type="submit">{{ trans('global.submit') }}</button>
                </div>
            </div>
        </div>
    </div>
</form>
<div class="form-group">
    <div class="card">
        <div class="card-header">
            Branches
        </div>
        <div class="card-body">
            <div class="form-group">
                @foreach ($branches as $branch)
                    <div class="progress-group">
                        <div class="progress-group-header align-items-end">
                            <i class="cil-globe-alt progress-group-icon me-2"></i>
                            <div>{{ $branch->name }}</div>
                            <div class="ms-auto font-weight-bold me-2">
                                {{ number_format($branch->sales_manager->employee->target_amount ?? 0).' EGP' }}
                            </div>
                            <div class="text-white small">
                                ({{ $branch->sales_manager && $branch->sales_manager->employee && $branch->sales_manager->employee->target_amount != 0 ? number_format(($branch->payments_sum_amount / $branch->sales_manager->employee->target_amount) * 100,2).' %' : '0 %' }})
                            </div>
                        </div>
                        <div class="progress-group-bars">
                            <div class="progress progress-xs">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $branch->sales_manager && $branch->sales_manager->employee && $branch->sales_manager->employee->target_amount != 0 ? number_format(($branch->payments_sum_amount / $branch->sales_manager->employee->target_amount) * 100,2) : '0' }}%" aria-valuenow="{{ $branch->sales_manager && $branch->sales_manager->employee && $branch->sales_manager->employee->target_amount != 0 ? number_format(($branch->payments_sum_amount / $branch->sales_manager->employee->target_amount) * 100,2) : '0' }}" aria-valuemin="0" aria-valuemax="100"><strong>{{ number_format($branch->payments_sum_amount).' EGP' }}</strong></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="form-group">
                <table class="table table-bordered table-striped table-hover zero-configuration text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Branch</th>
                            <th>Target</th>
                            <th>Collected</th>
                            <th>Percentage</th>
                            <th>Remaining</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($branches as $branch)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $branch->name }} <br> 
                                    Sales Manager : {!! $branch->sales_manager->name ?? '<span class="badge badge-danger">Not found !</span>' !!}
                                </td>
                                <td>{{ number_format($branch->sales_manager->employee->target_amount ?? 0) }}</td>
                                <td>
                                    <a href="{{ route('admin.payments.index',[
                                        'created_at[from]'  => date('Y-m-01'),
                                        'created_at[to]'    => date('Y-m-t'),
                                        'relations[account][branch_id]' => $branch->id
                                    ]) }}" target="_blank">
                                        {{ number_format($branch->payments_sum_amount) }}
                                    </a>
                                </td>
                                <td>
                                    {{ $branch->sales_manager && $branch->sales_manager->employee && $branch->sales_manager->employee->target_amount != 0 ? number_format(($branch->payments_sum_amount / $branch->sales_manager->employee->target_amount) * 100,2).' %' : '0 %' }}
                                </td>
                                <td>{{ number_format($branch->remaining) }}</td>
                                <td>
                                    <a href="{{ route('admin.reports.sales_details',[$branch->id,request('date') ?? date('Y-m')]) }}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
