@extends('layouts.admin')
@section('content')
    
        <div class="row form-group">
            <div class="col-lg-6"> 
                @can('payment_filter')

                    @include('admin_includes.filters', [
                    'columns' => [
                        'invoice_id'    => ['label' => 'Invoice', 'type' => 'number'],
                        'account_id'    => ['label' => 'Account', 'type' => 'select' , 'data' => $accounts , 'related_to' => 'account'],
                        'branch_id'     => ['label' => 'Branch', 'type' => 'select', 'data' => $branches,'related_to' => 'account'],
                        'sales_by_id'   => ['label' => 'Sales By', 'type' => 'select' , 'data' => $sales_bies],
                        'created_at'    => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true]
                    ],
                        'route' => 'admin.payments.index'
                    ])
                    @include('csvImport.modal', ['model' => 'Payment', 'route' => 'admin.payments.parseCsvImport'])
                @endcan

                @can('export_payments')
                    <a href="{{ route('admin.payments.export',request()->all()) }}" class="btn btn-info"><i class="fa fa-download">
                        </i> {{ trans('global.export_excel') }}
                    </a>
                @endcan
            </div>
            @can('payment_counter')
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.payment.title_singular') }}</h2>
                            <h2 class="text-center">{{ $payments->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.payment.fields.amount') }}</h2>
                            <h2 class="text-center">{{ number_format($payments->sum('amount')) ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            @endcan
        </div>

    
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.payment.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Payment">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.payment.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.payment.fields.invoice') }}
                        </th>
                        <th>
                            {{ trans('cruds.member.title_singular') }}
                        </th>
                        <th>
                            {{ trans('cruds.invoice.fields.account') }}
                        </th>
                        <th>
                            {{ trans('cruds.branch.title_singular') }}
                        </th>
                        <th>
                            {{ trans('cruds.service.title_singular') }}
                        </th>
                        <th>
                            {{ trans('cruds.payment.fields.amount') }}
                        </th>
                        
                        <th>
                            {{ trans('cruds.status.title_singular') }}
                        </th>
                        <th>
                            {{ trans('global.notes') }}
                        </th>
                        <th>
                            {{ trans('cruds.payment.fields.sales_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.bonu.fields.created_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.payment.fields.created_at') }}
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
            @can('payment_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.payments.massDestroy') }}",
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
                ajax: "{!! route('admin.payments.index',request()->all()) !!}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'invoice',
                        name: 'invoice'
                    },
                    {
                        data: 'member_name',
                        name: 'invoice.membership.member.member_code'
                    },
                    {
                        data: 'account',
                        name: 'account.name'
                    },
                    {
                        data: 'branch_name',
                        name: 'branch_name'
                    },
                    {
                        data: 'membership',
                        name: 'membership'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'notes',
                        name: 'notes'
                    },
                    {
                        data: 'sales_by_name',
                        name: 'sales_by.name'
                    },
                    {
                        data: 'created_by',
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
            let table = $('.datatable-Payment').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
