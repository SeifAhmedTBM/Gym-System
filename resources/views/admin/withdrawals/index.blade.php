@extends('layouts.admin')
@section('content')
    
        <div class="row form-group">
            <div class="col-lg-6">
                @can('withdrawal_create')
                    <a class="btn btn-success" href="{{ route('admin.withdrawals.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.withdrawal.title_singular') }}
                    </a>
                @endcan

                @can('withdrawal_filter')

                    @include('admin_includes.filters', [
                    'columns' => [
                    'amount' => ['label' => 'Amount', 'type' => 'number'],
                    'account_id' => ['label' => 'Account', 'type' => 'select' , 'data' => $accounts , 'related_to' => 'account'],
                    'created_by_id' => ['label' => 'Created By', 'type' => 'select' , 'data' => $created_bies],
                    'created_at' => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true]
                    ],
                    'route' => 'admin.withdrawals.index'
                    ])
                    @include('csvImport.modal', ['model' => 'Withdrawal', 'route' => 'admin.withdrawals.parseCsvImport'])
                @endcan
                
            </div>
            @can('withdrawal_counter')
                <div class="col-lg-3 col-md-2 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.withdrawal.title_singular') }}</h2>
                            <h2 class="text-center">{{ $withdrawals->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-2 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.withdrawal.fields.amount') }}</h2>
                            <h2 class="text-center">{{ number_format($withdrawals->sum('amount')) ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.withdrawal.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Withdrawal">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.withdrawal.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.withdrawal.fields.amount') }}
                        </th>
                        <th>
                            {{ trans('cruds.withdrawal.fields.notes') }}
                        </th>
                        <th>
                            {{ trans('cruds.withdrawal.fields.account') }}
                        </th>
                        <th>
                            {{ trans('cruds.withdrawal.fields.created_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.withdrawal.fields.created_at') }}
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
            @can('withdrawal_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.withdrawals.massDestroy') }}",
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
                ajax: "{!! route('admin.withdrawals.index',request()->all()) !!}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'notes',
                        name: 'notes'
                    },
                    {
                        data: 'account_name',
                        name: 'account.name'
                    },
                    {
                        data: 'created_by_name',
                        name: 'created_by.name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
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
            let table = $('.datatable-Withdrawal').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
