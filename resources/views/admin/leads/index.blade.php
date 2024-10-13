@extends('layouts.admin')
@section('content')
    <div class="form-group row">
        <div class="col-lg-9">
            @can('lead_create')
                <a class="btn btn-success" href="{{ route('admin.leads.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.lead.title_singular') }}
                </a>
            @endcan
            
            @can('lead_import')
                <button class="btn btn-warning" data-toggle="modal" data-target="#importLead">
                    <i class="fa fa-upload"></i> {{ trans('global.import_data') }}
                </button>
            @endcan

            @can('export_leads')
                <a href="{{ route('admin.leads.export',request()->all()) }}" class="btn btn-info">
                    <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                </a>
            @endcan
            
            @can('lead_filter')
            @include('admin_includes.filters', [
                'columns' => [
                    'name'              => ['label' => 'Name', 'type' => 'text'],
                    'status_id'         => ['label' => 'Status', 'type' => 'select', 'data' => $statuses],
                    'source_id'         => ['label' => 'Source', 'type' => 'select', 'data' => $sources],
                    'address_id'        => ['label' => 'Address', 'type' => 'select', 'data' => $addresses],
                    'phone'             => ['label' => 'Phone', 'type' => 'number'],
                    'parent_phone'      => ['label' => 'Parent Phone', 'type' => 'number'],
                    'parent_phone_two'  => ['label' => 'Parent Phone 2', 'type' => 'number'],
                    'sport_id'          => ['label' => 'Sport', 'type' => 'select','data' => $sports],
                    'branch_id'         => ['label' => 'Branch', 'type' => 'select', 'data' => $branches],
                    'gender'            => ['label' => 'Gender', 'type' => 'select', 'data' => ['male' => 'Male', 'female' => 'Female']],
                    'sales_by_id'       => ['label' => 'Sales By', 'type' => 'select', 'data' => $sales],
                    'created_at'        => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true]
                ],
                    'route' => 'admin.leads.index'
                ])
                {{-- @include('csvImport.modal', ['model' => 'Lead', 'route' => 'admin.leads.parseCsvImport']) --}}
            @endcan

            @can('lead_import')
                @include('admin.leads.import-model',['route' => 'admin.import.leads_and_members'])
            @endcan

        </div>
        @can('lead_counter')
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center">{{ trans('cruds.lead.title') }}</h2>
                        <h2 class="text-center">{{ $leads }}</h2>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    @if(Session::has('xl_sheet_error'))
        <div class="alert alert-danger font-weight-bold">
            <i class="fa fa-exclamation-circle"></i> {{ session('xl_sheet_error') }} .
        </div>
    @endif
   
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.lead.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped table-hover ajaxTable datatable datatable-Lead">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                      
                        <th>
                            {{ trans('cruds.lead.fields.photo') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.gender') }}
                        </th>
                        <th>
                            {{ trans('cruds.branch.title_singular') }}
                        </th>
                        <th>
                            Parent Phone
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.source') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.address') }}
                        </th>
                        @if (config('domains')[config('app.url')]['sports_option'] == true)
                        <th>
                            {{ trans('global.sport') }}
                        </th>
                        @endif
                        <th>
                            {{ trans('cruds.lead.fields.sales_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.lead.fields.created_at') }}
                        </th>
                        <th>
                            {{ trans('global.notes') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('global.import_data') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.lead.import') }}" method="post" class="modalForm2" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <label for="">File</label>
                        <input type="file" name="upload" id="upload" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- leads actions modal --}}
    <div class="modal fade" id="leadAction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
         <div class="modal-dialog modal-lg" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="exampleModalLabel">{{ trans('cruds.reminder.fields.action') }}</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <form action="" method="post" class="modalForm2">
                     @csrf
                     <div class="modal-body">
                         <div class="alert alert-info font-weight-bold">
                             <i class="fa fa-exclamation-circle"></i> {{ trans('global.if_empty_due_date') }} .
                         </div>
                         
                         @livewire('reminder-actions')
 
                         <div class="form-group">
                             <label for="notes">{{ trans('cruds.lead.fields.notes') }}</label>
                             <textarea name="notes" id="notes" rows="7" class="form-control"></textarea>
                         </div>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                         <button type="submit" class="btn btn-primary">Save changes</button>
                     </div>
                 </form>
             </div>
         </div>
    </div>

@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
                 @can('lead_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.leads.massDestroy') }}",
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
                ajax: "{!! route('admin.leads.index', request()->all()) !!}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'photo',
                        name: 'photo',
                        sortable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'gender',
                        name: 'gender'
                    },
                    {
                        data: 'branch_name',
                        name: 'branch.name'
                    },
                    {
                        data: 'parent',
                        name: 'parent'
                    },
                    {
                        data: 'source_name',
                        name: 'source.name'
                    },
                    {
                        data: 'status_name',
                        name: 'status.name'
                    },
                    {
                        data: 'address_name',
                        name: 'address.name'
                    },
                    @if (config('domains')[config('app.url')]['sports_option'] == true)
                    {
                        data: 'sport_name',
                        name: 'sport.name'
                    },
                    @endif
                    {
                        data: 'sales_by_name',
                        name: 'sales_by.name'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'notes',
                        name: 'notes'
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
                pageLength: 10,
            };
            let table = $('.datatable-Lead').DataTable(dtOverrideGlobals);
            table.DataTable
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
                
            });
        });
    </script>

    <script>
        function leadAction(id) {
            var id = id;
            var url = "{{ route('admin.leadAction', ':id') }}";
            url = url.replace(':id', id);
            $(".modalForm2").attr('action', url);
        }

        function formSubmit() {
            $('modalForm2').submit();
        }
    </script>
@endsection
