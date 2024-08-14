@extends('layouts.admin')
@section('content')
   
        <div class="row form-group">
            <div class="col-lg-6">
                @can('loan_create')
                    <a class="btn btn-success" href="{{ route('admin.loans.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.loan.title_singular') }}
                    </a>
                @endcan
                @can('loan_filter')

                    @include('admin_includes.filters', [
                    'columns' => [
                        'name' => ['label' => 'Employee', 'type' => 'text' , 'related_to' => 'employee'],
                        // 'name' => ['label' => 'Name', 'type' => 'text'],
                        'reason' => ['label' => 'Reason', 'type' => 'text'],
                        'amount' => ['label' => 'Amount', 'type' => 'number'],
                        'created_by_id' => ['label' => 'Created By', 'type' => 'select' , 'data' => $created_bies ,'realted_to' =>
                        'created_by'],
                        'created_at' => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true]
                    ],
                        'route' => 'admin.loans.index'
                    ])
                    @include('csvImport.modal', ['model' => 'Loan', 'route' => 'admin.loans.parseCsvImport'])
                @endcan
                
            </div>
            @can('loan_counter')
                <div class="col-lg-3 col-md-2 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.loan.title_singular') }}</h2>
                            <h2 class="text-center">{{ $loans->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-2 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.loan.fields.amount') }}</h2>
                            <h2 class="text-center">{{ number_format($loans->sum('amount')) }}</h2>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.loan.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Loan">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.loan.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.loan.fields.employee') }}
                        </th>
                        <th>
                            {{ trans('cruds.loan.fields.description') }}
                        </th>
                        <th>
                            {{ trans('cruds.loan.fields.amount') }}
                        </th>

                        <th>
                            {{ trans('cruds.account.title_singular') }}
                        </th>

                        <th>
                            {{ trans('cruds.loan.fields.created_at') }}
                        </th>
                        <th>
                            {{ trans('cruds.loan.fields.created_by') }}
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
            @can('loan_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.loans.massDestroy') }}",
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
                ajax: "{{ route('admin.loans.index',request()->all()) }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'employee_job_status',
                        name: 'employee.job_status'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'account_name',
                        name: 'account.name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
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
            let table = $('.datatable-Loan').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
