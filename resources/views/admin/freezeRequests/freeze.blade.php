@extends('layouts.admin')
@section('content')
    @can('freeze_request_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                @include('admin_includes.filters', [
                'columns' => [
                    'name'          => ['label' => 'Name', 'type'         => 'text', 'related_to' => 'membership.member'],
                    'member_code'   => ['label' => 'Member Code', 'type'  => 'text', 'related_to' => 'membership.member'],
                    'phone'         => ['label' => 'Phone', 'type'        => 'number', 'related_to' => 'membership.member'],
                    'branch_id'     => ['label' => 'Branch', 'type' => 'select', 'data' => \App\Models\Branch::pluck('name','id'),'related_to' => 'membership.member'],
                    'created_at'    => ['label' => 'Created at', 'type'   => 'date', 'from_and_to' => true]
                ],
                    'route' => 'admin.freeze.index'
                ])

                @can('export_freezes')
                    <a href="{{ route('admin.freezes.export',request()->all()) }}" class="btn btn-info">
                        <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                    </a>
                @endcan
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.freezeRequest.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-FreezeRequest">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.member_code') }}
                        </th>
                        <th>
                            {{ trans('cruds.member.title_singular') }}
                        </th>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.membership') }}
                        </th>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.freeze') }}
                        </th>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.start_date') }}
                        </th>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.end_date') }}
                        </th>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.is_retroactive') }}
                        </th>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.created_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.freezeRequest.fields.created_at') }}
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
            @can('freeze_request_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.freeze-requests.massDestroy') }}",
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
                ajax: "{{ route('admin.freeze.index',request()->all()) }}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'member_code',
                        name: 'member_code'
                    },
                    {
                        data: 'member',
                        name: 'member'
                    },
                    {
                        data: 'membership_service',
                        name: 'membership.service_pricelist.name'
                    },
                    {
                        data: 'freeze',
                        name: 'freeze'
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'is_retroactive',
                        name: 'is_retroactive'
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
            let table = $('.datatable-FreezeRequest').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });
    </script>
@endsection
