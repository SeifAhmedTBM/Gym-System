@extends('layouts.admin')
@section('content')
    
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-4">
                @can('invoice_filter')

                
                    @include('admin_includes.filters', [
                    'columns' => [
                        // 'name' => ['label' => 'Trainer Name', 'type' => 'text', 'related_to' => 'membership.trainer'],
                        'phone' => ['label' => 'Member Phone', 'type' => 'number', 'related_to' => 'membership.member'],
                        'member_code' => ['label' => 'Member Code', 'type' => 'number', 'related_to' => 'membership.member'],
                        // 'email' => ['label' => 'Member Email', 'type' => 'email', 'related_to' => 'membership.member.user'],
                        'service_fee' => ['label' => 'Service Fee', 'type' => 'number'],
                        'discount' => ['label' => 'Discount', 'type' => 'number'],
                        'sales_by_id' => ['label' => 'Sales By', 'type' => 'select' , 'data' =>$sales_bies],
                        'status' => ['label' => 'Status', 'type' => 'select', 'data' => \App\Models\Invoice::STATUS_SELECT],
                        'name' =>['label' => 'Membership', 'type' => 'text', 'related_to' => 'membership.service_pricelist'],
                        'created_at' => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true],
                    ],
                        'route' => 'admin.invoices.partial'
                    ])
                    @include('csvImport.modal', ['model' => 'Invoice', 'route' => 'admin.invoices.parseCsvImport'])
                @endcan

                @can('export_partial_invoices')
                    <a href="{{ route('admin.partial-invoices.export',request()->all()) }}" class="btn btn-info">
                        <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                    </a>
                @endcan
                
            </div>
        </div>
        
        @can('invoice_counter')
            <div class="row form-group">
                <div class="col-lg-3 col-md-2 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.invoice.title_singular') }}</h2>
                            <h2 class="text-center">{{ $invoices->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-2 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.invoice.fields.net_amount') }}</h2>
                            <h2 class="text-center">{{ number_format($invoices->sum('net_amount')) ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-2 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.invoice.fields.paid_amount') }}</h2>
                            <h2 class="text-center">{{ number_format($payments) ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-2 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('global.rest') }}</h2>
                            <h2 class="text-center">{{ number_format($invoices->sum('net_amount') - $payments) ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.invoice.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Invoice">
                <thead>
                    <tr>
                        <th width="10"></th>
                        <th>#</th>
                        <th>
                            {{ trans('cruds.member.title_singular') }}
                        </th>
                        <th width="150">
                            {{ trans('cruds.invoice.fields.membership') }}
                        </th>
                        <th width="100">
                            {{ trans('cruds.invoice.fields.discount') }}
                        </th>
                        <th width="150">
                            {{ trans('invoices::invoice.invoice') }}
                        </th>
                        <th>
                            {{ trans('cruds.invoice.fields.trainer_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.invoice.fields.sales_by') }}
                        </th>
                        @if(config('domains')[config('app.url')]['is_reviewed_invoices'] == true)
                        <th>
                            {{ trans('global.review_status') }}
                        </th>
                        @endif
                        <th>
                            {{ trans('cruds.loan.fields.created_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.invoice.fields.created_at') }}
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
            @can('invoice_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.invoices.massDestroy') }}",
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
                ajax: "{{ route('admin.invoices.partial',request()->all()) }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'member',
                        name: 'member'
                    },
                    {
                        data: 'membership_service',
                        name: 'membership_service'
                    },
                    {
                        data: 'discount',
                        name: 'discount'
                    },
                    {
                        data: 'amount',
                        name: 'net_amount'
                    },
                    {
                        data: 'trainer',
                        name: 'trainer.name',
                        searchable: false
                    },
                    {
                        data: 'sales_by_name',
                        name: 'sales_by.name'
                    },
                    @if(config('domains')[config('app.url')]['is_reviewed_invoices'] == true)
                    {
                        data: 'review_status',
                        name: 'reviewed'
                    },
                    @endif
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
            let table = $('.datatable-Invoice').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
