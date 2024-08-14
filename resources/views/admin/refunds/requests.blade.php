@extends('layouts.admin')
@section('content')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                @can('refund_filter')

                    @include('admin_includes.filters', [
                    'columns' => [
                        'refund_reason_id' => ['label' => 'Refund Reason', 'type' => 'select' , 'data' => $refund_reasons , 'related_to' => 'refund_reason'],
                        'amount' => ['label' => 'Amount', 'type' => 'number'],
                        'invoice_id' => ['label' => 'Invoice', 'type' => 'number'],
                        'created_by_id' => ['label' => 'Created By', 'type' => 'select' , 'data' => $created_bies],
                        'account_id' => ['label' => 'Account', 'type' => 'select' , 'data' => $accounts],
                        'created_at' => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true]
                    ],
                        'route' => 'admin.refunds.index'
                    ])
                    @include('csvImport.modal', ['model' => 'Refund', 'route' => 'admin.refunds.parseCsvImport'])
                @endcan
            </div>
        </div>
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.refund.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Refund">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.refund.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.refund.fields.refund_reason') }}
                        </th>
                        <th>
                            {{ trans('cruds.refund.fields.invoice') }}
                        </th>
                        <th>
                            {{ trans('cruds.invoice.fields.account') }}
                        </th>
                        <th>
                            {{ trans('cruds.refund.fields.amount') }}
                        </th>
                        <th>
                            {{ trans('cruds.refund.fields.created_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.status.title_singular') }}
                        </th>
                        <th>
                            {{ trans('cruds.refund.fields.created_at') }}
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
            @can('refund_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.refunds.massDestroy') }}",
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
                retrieve: true,searching:false,
                aaSorting: [],
                ajax: "{{ route('admin.refund.requests',request()->all()) }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'refund_reason_name',
                        name: 'refund_reason.name'
                    },
                    {
                        data: 'invoice_id',
                        name: 'invoice_id'
                    },
                    {
                        data: 'account',
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
                        data: 'status',
                        name: 'status'
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
            let table = $('.datatable-Refund').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
