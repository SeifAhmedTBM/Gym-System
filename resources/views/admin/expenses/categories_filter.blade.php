
@extends('layouts.admin')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.4/css/dataTables.dataTables.css" />
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button{
        padding:0px !important;
    }
</style>
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-6">
                @can('expense_create')
                    <a class="btn btn-success" href="{{ route('admin.expenses.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.expense.title_singular') }}
                    </a>

                @endcan

                @can('export_expenses')
                    <a href="{{ route('admin.expenses.export',request()->all()) }}" class="btn btn-info"><i class="fa fa-download">
                        </i> {{ trans('global.export_excel') }}
                    </a>
                @endcan

                    @can('expenses_filter')
                        @include('admin_includes.filters', [
                        'columns' => [
//                            'name' => ['label' => 'Name', 'type' => 'text'],
                            'amount' => ['label' => 'Amount', 'type' => 'number'],
                            'account_id' => ['label' => 'Account', 'type' => 'select' , 'data' => $accounts],
//                            'expenses_category_id'  => ['label' => 'Expenses Category', 'type' => 'select' , 'data' => $expenses_categories ,'related_to' => 'expenses_category', 'value'=>2],
                            'created_by_id' => ['label' => 'Created By', 'type' => 'select', 'data' => $users],
                            'date' => ['label' => 'Date', 'type' => 'date','from_and_to' => true],
//                            'created_at' => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true]
                        ],
                            'route' => 'admin.expenses_categories_show_by_filter'
                        ])
                        @include('csvImport.modal', ['model' => 'Expense', 'route' => 'admin.expenses.parseCsvImport'])
                    @endcan
            </div>
            @can('expenses_counter')
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-body" style="text-align:center">
                        <h2 class="text-center">{{ trans('cruds.expense.title_singular') }}</h2>
                        <h2 class="text-center">{{ $expenses->count() }}</h2>
                        <small class="text-center text-danger">current Month Total</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-body" style="text-align:center">
                        <h2 class="text-center">{{ trans('cruds.expense.fields.amount') }}</h2>
                        <h2 class="text-center">{{ number_format($expenses->sum('amount')) ?? 0 }}</h2>
                        <small class="text-center text-danger">current Month Total</small>
                    </div>
                </div>
            </div>
            @endcan
        </div>

    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.expense.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Expense" id="myTable">
                <thead>
                    <tr>

                        <th>
                            {{ trans('cruds.expense.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.expense.fields.expenses_category') }}
                        </th>
                        <th>
                            {{ trans('cruds.branch.title_singular') }}
                        </th>
                        <th>
                            {{ trans('cruds.expense.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.expense.fields.date') }}
                        </th>
                        <th>
                            {{ trans('cruds.account.title_singular') }}
                        </th>
                        <th>
                            {{ trans('cruds.expense.fields.amount') }}
                        </th>
                        <th>
                            {{ trans('cruds.expense.fields.created_by') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                      <tr>
                        <td>{{$expense->id}}</td>
                        <td>{{$expense->expenses_category?->name}}</td>
                        <td>{{$expense->account?->branch?->name}}</td>
                        <td>{{$expense->note}}</td>
                        <td>{{$expense->date}}</td>
                        <td>{{$expense->account?->name}}</td>
                        <td>{{$expense->amount}}</td>
                        <td>{{$expense->created_by->name}}</td>
                        <td>
                            <div class="dropdown">
                                <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                                    Action
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <a class="dropdown-item" href="/admin/expenses/{{$expense->id}}">
                                        <i class="fa fa-eye"></i> &nbsp; View
                                    </a>
                                    <a class="dropdown-item" href="/admin/expenses/{{$expense->id}}/edit">
                                        <i class="fa fa-edit"></i> &nbsp; Edit
                                    </a>
                                    <form action="/admin/expenses/{{$expense->id}}" method="POST" onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="z4QFvWSQb3v1LhLC5Wp9JkTKWZ4EXS4D0Dy3VtKq">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fa fa-trash"></i> &nbsp; Delete
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </td>
                      </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


<script src="https://cdn.datatables.net/2.1.4/js/dataTables.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready( function () {
    $('#myTable').DataTable();
    } );
</script>
@endsection
@section('scripts')
@parent
@endsection
