@extends('layouts.admin')
@section('content')
    <div  class="row form-group">
        <div class="col-lg-6">
            @can('refund_filter')
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    {{ trans('global.app_csvImport') }}
                </button>

                @include('admin_includes.filters', [
                'columns' => [
                    'refund_reason_id' => ['label' => 'Refund Reason', 'type' => 'select' , 'data' => $refund_reasons , 'related_to' => 'refund_reason'],
                    'amount'        => ['label' => 'Amount', 'type' => 'number'],
                    'invoice_id'    => ['label' => 'Invoice', 'type' => 'number'],
                    'created_by_id' => ['label' => 'Created By', 'type' => 'select' , 'data' => $created_bies],
                    'account_id'    => ['label' => 'Account', 'type' => 'select' , 'data' => $accounts],
                    'branch_id'     => ['label' => 'Branch', 'type' => 'select', 'data' => $branches,'related_to' => 'account'],
                    'status'        => ['label' => 'Status', 'type' => 'select' , 'data' => \App\Models\Refund::STATUS],
                    'created_at'    => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true]
                ],
                    'route' => 'admin.refunds.index'
                ])
                @include('csvImport.modal', ['model' => 'Refund', 'route' => 'admin.refunds.parseCsvImport'])
            @endcan

            @can('export_refunds')
                <a href="{{ route('admin.refunds.export',request()->all()) }}" class="btn btn-info">
                    <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                </a>
            @endcan
        </div>
        @can('refund_counter')
            <div class="col-lg-3 col-md-2 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center">{{ trans('cruds.refund.title_singular') }}</h2>
                        <h2 class="text-center">{{ $refunds->count() }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-2 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center">{{ trans('cruds.refund.fields.amount') }}</h2>
                        <h2 class="text-center">{{ number_format($refunds->sum('amount')) ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        @endcan
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
                            {{ trans('cruds.member.title_singular') }}
                        </th>
                        <th>
                            {{ trans('cruds.branch.title_singular') }}
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
                            {{ trans('cruds.status.title_singular') }}
                        </th>
                        <th>
                            {{ trans('cruds.refund.fields.created_by') }}
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
                retrieve: true,
                searching:true,
                aaSorting: [],
                ajax: "{!! route('admin.refunds.index',request()->all()) !!}",
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
                        data: 'branch_name',
                        name: 'branch_name'
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
                        data: 'status',
                        name: 'status'
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
            let table = $('.datatable-Refund').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
