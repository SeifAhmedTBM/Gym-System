@extends('layouts.admin')
@section('content')
    @can('sales_tier_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.sales-tiers.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.salesTier.title_singular') }}
                </a>
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    {{ trans('global.app_csvImport') }}
                </button>
                @include('csvImport.modal', [
                    'model' => 'SalesTier',
                    'route' => 'admin.sales-tiers.parseCsvImport',
                ])
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.salesTier.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-SalesTier">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.salesTier.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.salesTier.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.salesTier.fields.month') }}
                        </th>
                        <th>
                            {{ trans('cruds.salesTier.fields.type') }}
                        </th>
                        <th>
                            {{ trans('cruds.salesTier.fields.status') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="transferToNextMonthModal" tabindex="-1" aria-labelledby="transferToNextMonthModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="transferToNextMonthModalLabel">
                        {{ trans('global.transfer_to_next_month') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(['method' => 'PUT', 'id' => 'transferSalesTierForm']) !!}
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('name', trans('global.new_plan_name'), ['class' => 'required']) !!}
                        {!! Form::text('name', null, ['class' => 'form-control shadow-none form-control-lg', 'id' => 'salesTierName']) !!}
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-danger"
                        data-dismiss="modal">{{ trans('global.cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ trans('global.transfer') }}</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            let dtOverrideGlobals = {
                buttons: [dtButtons],
                processing: true,
                serverSide: true,
                retrieve: true,
                searching: true,
                aaSorting: [],
                ajax: "{{ route('admin.sales-tiers.index') }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'month',
                        name: 'month'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'status',
                        name: 'status'
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
            let table = $('.datatable-SalesTier').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });

        function getSalesTierName(button) {
            let sales_tier_id = $(button).data('id');
            $.ajax({
                method: "GET",
                url: $(button).data('get'),
                success: function(response) {
                    $('#salesTierName').val(response.name);
                    $("#transferSalesTierForm").attr('action', $(button).data('route'));
                }
            })
        }
    </script>
@endsection
