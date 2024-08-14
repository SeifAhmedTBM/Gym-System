@extends('layouts.admin')
@section('content')
@can('service_options_pricelist_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.service-options-pricelists.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.serviceOptionsPricelist.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'ServiceOptionsPricelist', 'route' => 'admin.service-options-pricelists.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        <h5>{{ trans('cruds.serviceOptionsPricelist.title_singular') }} {{ trans('global.list') }}</h5>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-ServiceOptionsPricelist">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.serviceOptionsPricelist.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.serviceOptionsPricelist.fields.service_option') }}
                        </th>
                        <th>
                            {{ trans('cruds.serviceOptionsPricelist.fields.pricelist') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($serviceOptionsPricelists as $key => $serviceOptionsPricelist)
                        <tr data-entry-id="{{ $serviceOptionsPricelist->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $serviceOptionsPricelist->id ?? '' }}
                            </td>
                            <td>
                                {{ $serviceOptionsPricelist->service_option->name ?? '' }}
                            </td>
                            <td>
                                {{ $serviceOptionsPricelist->pricelist->amount.'@'.$serviceOptionsPricelist->pricelist->service->name .' - '.$serviceOptionsPricelist->pricelist->service->service_type->name  ?? '-'}}
                            </td>
                            <td>
                                @can('service_options_pricelist_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.service-options-pricelists.show', $serviceOptionsPricelist->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('service_options_pricelist_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.service-options-pricelists.edit', $serviceOptionsPricelist->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('service_options_pricelist_delete')
                                    <form action="{{ route('admin.service-options-pricelists.destroy', $serviceOptionsPricelist->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
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
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('service_options_pricelist_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.service-options-pricelists.massDestroy') }}",
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
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-ServiceOptionsPricelist:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection