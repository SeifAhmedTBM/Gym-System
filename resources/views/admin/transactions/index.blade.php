@extends('layouts.admin')
@section('content')
    
        <div style="margin-bottom: 10px;" class="row form-group">
            <div class="col-lg-6">
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    {{ trans('global.app_csvImport') }}
                </button>
                @include('admin_includes.filters', [
                'columns' => [
                    'account_id'            => ['label' => 'Account', 'type' => 'select' , 'data' => $accounts , 'related_to' => 'account'],
                    'created_at'            => ['label'=> 'Created at', 'type' => 'date', 'from_and_to' => true]
                ],
                    'route' => 'admin.transactions.index'
                ])

                {{-- @can('export_payments')
                    <a href="{{ route('admin.payments.export',request()->all()) }}" class="btn btn-info"><i class="fa fa-download">
                        </i> {{ trans('global.export_excel') }}
                    </a>
                @endcan --}}
            </div>
            {{-- @can('payment_counter')
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
            @endcan --}}
        </div>

    
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.payment.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Transaction">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.payment.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.transactions.fields.type') }}
                        </th>
                        <th>
                            {{ trans('global.notes') }}
                        </th>
                        <th>
                            {{ trans('cruds.invoice.fields.account') }}
                        </th>
                        <th>
                            {{ trans('cruds.payment.fields.amount') }}
                        </th>
                        <th>
                            {{ trans('cruds.bonu.fields.created_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.payment.fields.created_at') }}
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
                buttons:[],
                processing: true,
                serverSide: true,
                retrieve: true,
                searching:true,
                aaSorting: [],
                ajax: "{!! route('admin.transactions.index',request()->all()) !!}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'transactionable_type',
                        name: 'transactionable_type'
                    },
                    {
                        data: 'transactionable_id',
                        name: 'transactionable_id'
                    },
                    {
                        data: 'account',
                        name: 'account'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'created_by',
                        name: 'createdBy.name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                ],
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 20,
            };
            let table = $('.datatable-Transaction').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
