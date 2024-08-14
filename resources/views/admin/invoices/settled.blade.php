@extends('layouts.admin')
@section('content')
    <div class="row form-group">
        <div class="col-lg-4 form-group">
            @can('invoice_filter')
                @include('admin_includes.filters', [
                    'columns' => [
                        'id' => ['label' => 'Invoice Number', 'type' => 'text'],
                        'phone' => [
                            'label' => 'Member Phone',
                            'type' => 'number',
                            'related_to' => 'membership.member',
                        ],
                        'member_code' => [
                            'label' => 'Member Code',
                            'type' => 'number',
                            'related_to' => 'membership.member',
                        ],
                        'discount' => ['label' => 'Discount', 'type' => 'number'],
                        'service_fee' => ['label' => 'Service Fee', 'type' => 'number'],
                        // 'branch_id' => [
                        //     'label' => 'Branch',
                        //     'type' => 'select',
                        //     'data' => $branches,
                        //     'related_to' => 'membership.member',
                        // ],
                        // 'sales_by_id' => ['label' => 'Sales By', 'type' => 'select', 'data' => $sales_bies],
                        // 'status' => [
                        //     'label' => 'Status',
                        //     'type' => 'select',
                        //     'data' => \App\Models\Invoice::STATUS_SELECT,
                        // ],
                        'is_reviewed' => [
                            'label' => 'Reviewed Status',
                            'type' => 'select',
                            'data' => [
                                '' => trans('global.pleaseSelect'),
                                'is_reviewed' => trans('global.is_reviewed'),
                                'not_reviewed' => trans('global.not_reviewed'),
                            ],
                        ],
                        'name' => [
                            'label' => 'Membership',
                            'type' => 'text',
                            'related_to' => 'membership.service_pricelist',
                        ],
                        // 'name' => ['label' => 'Trainer Name', 'type' => 'text', 'related_to' => 'membership.trainer'],
                        'created_at' => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true],
                    ],
                    'route' => 'admin.invoices.index',
                ])
                @include('csvImport.modal', [
                    'model' => 'Invoice',
                    'route' => 'admin.invoices.parseCsvImport',
                ])
            @endcan

            @can('export_invoices')
                <a href="{{ route('admin.invoices.export', request()->all()) }}" class="btn btn-info">
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
                        <h2 class="text-center">{{ trans('cruds.invoice.title') }}</h2>
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
                        <h2 class="text-center">{{ number_format($payments->sum('amount')) ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-2 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center">{{ trans('global.rest') }}</h2>
                        <h2 class="text-center">
                            {{ number_format($invoices->sum('net_amount') - $payments->sum('amount')) ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    <div class="card">
        <div class="card-header font-weight-bold">
            <i class="fa fa-list"></i> {{ trans('cruds.invoice.title_singular') }} {{ trans('global.list') }}
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
                        <th>
                            {{ trans('cruds.branch.title_singular') }}
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
                        <th>
                            {{ trans('cruds.invoice.fields.status') }}
                        </th>
                        @if (config('domains')[config('app.url')]['is_reviewed_invoices'] == true)
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


    {{-- <!-- Delete Modal -->
    <div class="modal fade" id="updateInvoiceReviewedStatusModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('global.edit') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(['method' => 'POST', 'id' => 'updateInvoiceReviewedStatusForm']) !!}
                <div class="modal-body">
                    <h4 class="text-success font-weight-bold text-center">
                        {{ trans('global.invoice_reviewed_will_be_changed') }}
                    </h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        {{ trans('global.close') }}
                    </button>
                    <button type="submit" class="btn btn-success">{{ trans('global.yes') }}</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade" id="settlement_invoice" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Settlement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(['method' => 'POST', 'id' => 'settlement_invoice_form']) !!}
                <div class="modal-body">
                    <h4 class="text-warning font-weight-bold text-center">
                        {{ trans('global.settlement_invoice') }}
                    </h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        {{ trans('global.close') }}
                    </button>
                    <button type="submit" class="btn btn-success">{{ trans('global.yes') }}</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection --}}
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtOverrideGlobals = {
                buttons: [],
                processing: true,
                serverSide: true,
                retrieve: true,
                searching: true,
                aaSorting: [],
                ajax: "{{ route('admin.invoices.settled', request()->all()) }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        searchable: false,
                    },
                    {
                        data: 'member',
                        name: 'membership.member.member_code',
                    },
                    {
                        data: 'membership_service',
                        name: 'membership_service',
                        searchable: false,
                    },
                    {
                        data: 'branch_name',
                        name: 'branch_name',
                    },
                    {
                        data: 'discount',
                        name: 'discount',
                        searchable: false,
                    },
                    {
                        data: 'amount',
                        name: 'net_amount',
                    },
                    {
                        data: 'trainer',
                        name: 'trainer',
                    },
                    {
                        data: 'sales_by_name',
                        name: 'sales_by.name',
                    },
                    {
                        data: 'status',
                        name: 'status',
                    },
                    @if (config('domains')[config('app.url')]['is_reviewed_invoices'] == true)
                        {
                            data: 'review_status',
                            name: 'is_reviewed',
                            searchable: false
                        },
                    @endif {
                        data: 'created_by',
                        name: 'created_by.name',
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: '{{ trans('global.actions') }}',
                        searchable: false
                    }
                ],
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 25,
            };
            let table = $('.datatable-Invoice').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });

        // function getInvoiceDetails(button) {
        //     let formURL = $(button).data('url');
        //     $("#updateInvoiceReviewedStatusForm").attr('action', formURL);
        // }

        // function setSettlementInvoice(button) {
        //     let formURL2 = $(button).data('url');
        //     $("#settlement_invoice_form").attr('action', formURL2);
        // }
    </script>
@endsection
