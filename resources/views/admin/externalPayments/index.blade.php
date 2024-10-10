@extends('layouts.admin')
@section('content')
    
        <div class="row form-group">
            <div class="col-lg-6">
                @can('external_payment_create')
                    <a class="btn btn-success" href="{{ route('admin.external-payments.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.externalPayment.title_singular') }}
                    </a>
                @endcan
                @can('external_payment_filter')

                    @include('admin_includes.filters', [
                        'columns' => [
                        'title'         => ['label' => 'Title', 'type' => 'text'],
                        // 'name'          => ['label' => 'Name', 'type' => 'text', 'related_to' => 'lead'],
                        // 'phone'         => ['label' => 'Phone', 'type' => 'number', 'related_to' => 'lead'],
                        'amount'        => ['label' => 'Amount', 'type' => 'number'],
                        'account_id'    => ['label' => 'Account', 'type' => 'select' , 'data' => $accounts , 'related_to' => 'account'],
                        'branch_id'     => ['label' => 'Branch', 'type' => 'select', 'data' => $branches,'related_to' => 'account'],
                        'external_payment_category_id'    => ['label' => 'External Payment Category', 'type' => 'select' , 'data' => $external_payment_categories , 'related_to' => 'external_payment_category'],
//                        'invoice_id'    => ['label' => 'Invoice', 'type' => 'number'],
                        'created_by_id' => ['label' => 'Created By', 'type' => 'select' , 'data' => $created_bies],
                        'created_at'    => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true]
                    ],
                        'route' => 'admin.external-payments.index'
                    ])
                    @include('csvImport.modal', ['model' => 'ExternalPayment', 'route' => 'admin.external-payments.parseCsvImport'])
                @endcan
            </div>
            @can('external_payment_counter')
                <div class="col-lg-3 col-md-2 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.externalPayment.title_singular') }}</h2>
                            <h2 class="text-center">{{ $externalPayments->count() }}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-2 col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.externalPayment.fields.amount') }}</h2>
                            <h2 class="text-center">{{ number_format($externalPayments->sum('amount')) ?? 0 }}</h2>
                        </div>    
                    </div>    
                </div>    
            @endcan
        </div>
    
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.externalPayment.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ExternalPayment">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.externalPayment.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.externalPayment.fields.title') }}
                        </th>
                        <th>
                            Other Revenue Category
                        </th>
                        <th>
                            {{ trans('cruds.branch.title_singular') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.title_singular') }}
                        </th>
                        <th>
                            {{ trans('cruds.externalPayment.fields.amount') }}
                        </th>
                        <th>
                            {{ trans('cruds.externalPayment.fields.notes') }}
                        </th>
                        <th>
                            {{ trans('cruds.externalPayment.fields.account') }}
                        </th>
                        <th>
                            {{ trans('cruds.externalPayment.fields.created_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.externalPayment.fields.created_at') }}
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

            let dtOverrideGlobals = {
                buttons:dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                searching:true,
                aaSorting: [],
                ajax: "{!! route('admin.external-payments.index', request()->all()) !!}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'external_payment_category_name',
                        name: 'external_payment_category.name'
                    },
                    {
                        data: 'branch_name',
                        name: 'branch_name'
                    },
                    {
                        data: 'lead_name',
                        name: 'lead.name'
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
            let table = $('.datatable-ExternalPayment').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
