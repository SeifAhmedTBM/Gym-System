@extends('layouts.admin')
@section('content')
    @can('warehouse_product_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                <a class="btn btn-success" href="{{ route('admin.warehouse-products.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.warehouseProduct.title_singular') }}
                </a>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.warehouseProduct.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-WarehouseProduct">
                    <thead>
                        <tr>
                            <th width="10">

                            </th>
                            <th>
                                {{ trans('cruds.warehouseProduct.fields.id') }}
                            </th>
                            <th>
                                {{ trans('cruds.warehouseProduct.fields.product') }}
                            </th>
                            <th>
                                {{ trans('cruds.warehouseProduct.fields.wharehouse') }}
                            </th>
                            <th>
                                {{ trans('cruds.warehouseProduct.fields.balance') }}
                            </th>
                            <th>
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($warehouseProducts as $key => $warehouseProduct)
                            <tr data-entry-id="{{ $warehouseProduct->id }}">
                                <td>

                                </td>
                                <td>
                                    {{ $warehouseProduct->id ?? '' }}
                                </td>
                                <td>
                                    {{ $warehouseProduct->product->name ?? '' }}
                                </td>
                                <td>
                                    {{ $warehouseProduct->warehouse->name ?? '' }}
                                </td>
                                <td>
                                    {{ $warehouseProduct->balance ?? '' }}
                                </td>
                                <td>
                                    @can('warehouse_product_show')
                                        <a class="btn btn-xs btn-primary"
                                            href="{{ route('admin.warehouse-products.show', $warehouseProduct->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    @can('warehouse_product_edit')
                                        <a class="btn btn-xs btn-info"
                                            href="{{ route('admin.warehouse-products.edit', $warehouseProduct->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('warehouse_product_delete')
                                        <form action="{{ route('admin.warehouse-products.destroy', $warehouseProduct->id) }}"
                                            method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');"
                                            style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-xs btn-danger"
                                                value="{{ trans('global.delete') }}">
                                        </form>
                                    @endcan

                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>



@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
            @can('warehouse_product_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.warehouse-products.massDestroy') }}",
                className: 'btn-danger',
                action: function (e, dt, node, config) {
                var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                return $(entry).data('entry-id')
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

            $.extend(true, $.fn.dataTable.defaults, {
                orderCellsTop: true,
                order: [
                    [1, 'desc']
                ],
                pageLength: 100,
            });
            let table = $('.datatable-WarehouseProduct:not(.ajaxTable)').DataTable({
                buttons: dtButtons
            })
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        })
    </script>
@endsection
