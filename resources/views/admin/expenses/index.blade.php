@extends('layouts.admin')
@section('content')
    
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
//                        'name' => ['label' => 'Name', 'type' => 'text'],
//                        'expenses_category_id' => ['label' => 'Expenses Category', 'type' => 'text','data' => 'Rent'],
                        'amount' => ['label' => 'Amount', 'type' => 'number'],
                        'account_id' => ['label' => 'Account', 'type' => 'select' , 'data' => $accounts],
                        'expenses_category_id' => $status ? [
                                'label' => 'Expenses Category',
                                'type' => 'category',
                                'data' => $expenses_categories,
                            ] : [
                                'label' => 'Expenses Category',
                                'type' => 'select',
                                'data' => $expenses_categories,
                                'related_to' => 'expenses_category'],
                        'created_by_id' => ['label' => 'Created By', 'type' => 'select', 'data' => $users],
                        'date' => ['label' => 'Date', 'type' => 'date','from_and_to' => true],
                    ],
                        'route' =>'admin.expenses.index'
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
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Expense">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
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
            </table>
        </div>
    </div>



@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('expense_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.expenses.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
                return entry.id
                });
            
                if (ids.length === 0) {
                alert('{{ trans('global.datatables.zero_selected') }}')
            
                return
                }
            
                if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({
                headers: {'x-csrf-token': _token},
                method: 'POST',
                url: config.url,
                data: { ids: ids, _method: 'DELETE' }})
                .done(function () { location.reload() })
                }
                }
                }
                dtButtons.push(deleteButton)
            @endcan

            let dtOverrideGlobals = {
                buttons:[],
                processing: true,
                serverSide: true,
                retrieve: true,
                searching:true,
                aaSorting: [],

                ajax: "{!!  route('admin.expenses.index', request()->all()) !!}",

                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'expenses_category_name',
                        name: 'expenses_category.name'
                    },
                    {
                        data: 'branch_name',
                        name: 'branch_name'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'account_name',
                        name: 'account.name'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                   
                    {
                        data: 'created_by_name',
                        name: 'created_by.name'
                    },
                   

                    
                    {
                        data: 'actions',
                        name: '{{ trans('global.actions') }}'
                    }
                ],
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 50,
            };
            let table = $('.datatable-Expense').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
