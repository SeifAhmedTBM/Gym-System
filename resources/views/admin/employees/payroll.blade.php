@extends('layouts.admin')
@section('content')
<div class="from-group row">
    <div class="col-lg-4 col-md-2 col-sm-12">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center">Total Salaries</h5>
                <h5 class="text-center">{{ number_format($payrolls->sum('basic_salary')) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-2 col-sm-12">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center">Total Bonuses</h5>
                <h5 class="text-center">{{ number_format($payrolls->sum('bonus')) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-2 col-sm-12">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center">Total Deductions</h5>
                <h5 class="text-center">{{ number_format($payrolls->sum('deduction')) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-2 col-sm-12">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center">Total Loans</h5>
                <h5 class="text-center">{{ number_format($payrolls->sum('loans')) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-2 col-sm-12">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center">Total Comissions</h5>
                <h5 class="text-center">{{ number_format($total_comissions) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-2 col-sm-12">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center">Total Net</h5>
                <h5 class="text-center">{{ number_format($payrolls->sum('net_salary')) }}</h5>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.payroll.get') }}" method="get">
            <div class="row">
                <div class="col-md-12">
                    <label for="date">{{ trans('global.date') }}</label>
                    <div class="input-group">
                        <input type="month" class="form-control" name="date" value="{{ request('date') ?? date('Y-m') }}">
                        <select name="branch_id" id="branch_id" class="form-control" {{ $emp && $emp->branch_id != NULL ? 'readonly' : '' }}>
                            <option value="{{ NULL }}" selected >All Branches</option>
                            @foreach (\App\Models\Branch::pluck('name','id') as $id => $name)
                                <option value="{{ $id }}" {{ $branch_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <select name="role_id" id="role_id" class="form-control">
                            <option value="{{ NULL }}" selected >All Roles</option>
                            @foreach (\App\Models\Role::pluck('title','id') as $id => $title)
                                <option value="{{ $id }}" {{ request('role_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                            @endforeach
                        </select>
                        <div class="input-group-prepend">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i> {{ trans('global.submit') }}</button>
                            <a href="{{ route('admin.payroll.export',request()->all()) }}" class="btn btn-info">
                                <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header font-weight-bold">
        <div class="form-group row">
            <div class="col-md-3">
                <i class="fa fa-money-bill"></i> {{ trans('global.payroll') }}
            </div>
        </div>
    </div>
    <div class="card-body">
        <h4>{{ trans('global.payroll') }} : 
            <span class="badge badge-success px-2 py-2">
                {{ request('date') ? request('date') : date('Y-m') }}
            </span>
        </h4>

        <form action="{{ route('admin.payroll.confirm_all',request()->all()) }}" method="post">
            @csrf
            @method('PUT')
            <div class="form-group row">
                <div class="col-md-4 offset-md-6">
                    <label for="account_id" class="required">Account</label>
                    <select name="account_id" id="account_id" class="form-control" required>
                        <option value="{{ NULL }}">{{ trans('global.pleaseSelect') }}</option>
                        @foreach ($accounts as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
        
                <div class="col-md-2">
                    <label>{{ trans('global.confirm') }}</label>
                    <button class="btn btn-dark btn-block float-right" type="submit">
                        <i class="fa fa-check"></i> Confirm All
                    </button>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-bordered table-outline text-center table-hover zero-configuration">
                <thead class="thead-light">
                    <tr>
                        <th class="text-dark">{{ trans('global.name') }}</th>
                        <th class="text-dark">{{ trans('cruds.branch.title_singular') }}</th>
                        <th class="text-dark">{{ trans('global.basic_salary') }}</th>
                        <th class="text-dark">{{ trans('global.bonuses') }}</th>
                        <th class="text-dark">{{ trans('global.all_deductions') }}</th>
                        <th class="text-dark">{{ trans('cruds.loan.title') }}</th>
                        <th class="text-dark">Fixed Comissions amount</th>
                        <th class="text-dark">Percentage Comissions amount</th>
                        <th class="text-dark">Total Comissions amount</th>
                        <th class="text-dark">{{ trans('global.net_salary') }}</th>
                        <th class="text-dark">Confirmed</th>
                        <th class="text-dark">{{ trans('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payrolls as $payroll)
                        <tr class="{{ $payroll->status == 'confirmed' ? 'table-success' : '' }}">
                            <td class="font-weight-bold">
                                <i class="far fa-user"></i> {{ $payroll->employee->name ?? '-'}}<br>
                                <i class="fa fa-phone"></i> {{ $payroll->employee->phone ?? '-'}}<br>
                                <span class="badge badge-info px-2 py-2">
                                    {{ $payroll->employee->user->roles[0]->title }}
                                </span>
                            </td>
                            <td>{{ $payroll->employee->branch->name ?? '-' }}</td>
                            <td>
                                {{ number_format($payroll->basic_salary) . ' EGP' }}
                            </td>
                            <td>
                                {{ number_format($payroll->bonus) . ' EGP' }}
                            </td>
                            <td>
                                {{ number_format($payroll->deduction) . ' EGP' }}
                            </td>
                            <td>
                                {{ number_format($payroll->loans) . ' EGP' }}
                            </td>
                            <td>
                                <a href="{{ route('admin.payroll.fixedComission', $payroll->id) }}" class="btn btn-sm btn-primary">
                                {{ number_format($payroll->fixed_comissions) . ' EGP' }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.payroll.percentageComission', $payroll->id) }}" class="btn btn-sm btn-primary">
                                    {{ number_format($payroll->percentage_comissions) . ' EGP' }}
                                </a>
                            </td>
                            <td>
                                {{ number_format($payroll->fixed_comissions+$payroll->percentage_comissions) . ' EGP' }}
                            </td>
                            <td>
                                {{ number_format($payroll->net_salary) . ' EGP' }}
                            </td>
                            <td>
                                {!! $payroll->status == 'confirmed' ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>' !!}
                            </td>
                            <td>
                                <div class="btn group">
                                    <a href="{{ route('admin.payroll.show', $payroll->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-eye"></i> {{ trans('global.view') }}
                                    </a>
                                    @if ($payroll->status == 'unconfirmed')
                                        <form action="{{ route('admin.payroll.status', $payroll->id) }}" method="POST"
                                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="PUT">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="fa fa-check-circle"></i> {{ trans('global.confirm') }}
                                            </button>
                                        </form>
                                    {{-- @else
                                        <form action="{{ route('admin.payroll.status', $payroll->id) }}" method="POST"
                                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="PUT">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fa fa-times-circle"></i> {{ trans('global.unconfirmed') }}
                                            </button>
                                        </form> --}}
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">{{ trans('global.no_data_available') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection