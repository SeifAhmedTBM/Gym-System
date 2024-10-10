@extends('layouts.admin')
@section('content')
        <div class="form-group row">
            <div class="col-lg-9">
                @can('membership_create')
                    <a class="btn btn-success" href="{{ route('admin.memberships.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.membership.title_singular') }}
                    </a>
                @endcan

                @can('membership_filter')

                    @include('admin_includes.filters', [
                    'columns' => [
                        // 'id' => ['label' => 'ID', 'type' => 'number'],
                        'name'              => ['label' => 'Name', 'type' => 'text', 'related_to' => 'member'],
                        'phone'             => ['label' => 'Member Phone', 'type' => 'number', 'related_to' => 'member'],
                        'member_code'       => ['label' => 'Member Code', 'type' => 'text', 'related_to' => 'member'],
                        'email'             => ['label' => 'Member Email', 'type' => 'email', 'related_to' => 'member.user'],
                        'sales_by_id'       => ['label' => 'Sales By', 'type' => 'select', 'data' => $sales],
                        'trainer_id'        => ['label' => 'Trainer', 'type' => 'select', 'data' => $trainers],
                        // 'service_pricelist_id' => ['label' => 'Service', 'type' => 'select', 'data' => $services],
                        'service_type_id'   => ['label' => 'Service Type', 'type' => 'select', 'data' => $service_types,'related_to' => 'service_pricelist.service'],
                        'branch_id'         => ['label' => 'Branch', 'type' => 'select', 'data' => $branches,'related_to' => 'member'],
                        'gender'            => ['label' => trans('global.gender'), 'type' => 'text', 'related_to' => 'member'],
                        'status'            => ['label' => trans('global.status'), 'type' => 'select', 'data' => \App\Models\Membership::SELECT_STATUS],
                        'start_date'        => ['label' => trans('global.start_date'), 'type' => 'date', 'from_and_to' => true],
                        'end_date'          => ['label' => trans('global.end_date'), 'type' => 'date', 'from_and_to' => true],
                        'created_at'        => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true],
                    ],
                    'route' => 'admin.memberships.index'
                    ])

                    @include('csvImport.modal', ['model' => 'Membership', 'route' => 'admin.memberships.parseCsvImport'])
                @endcan
                @can('export_memberships')
                    <a href="{{ route('admin.memberships.export',request()->all()) }}" class="btn btn-info">
                        <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                    </a>
                @endcan

                {{-- <button type="button" data-toggle="modal" data-target="#sendReminder" class="btn btn-dark"><i
                        class="fa fa-plus-circle"></i> Reminder</button> --}}
            </div>
            @can('membership_counter')
                {{-- <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">{{ trans('cruds.membership.title') }}</h2>
                            <h2 class="text-center">{{ $memberships_count }}</h2>
                        </div>
                    </div>
                </div> --}}
            @endcan
        </div>
    
    <div class="card">
        <div class="card-header">
            {{ trans('cruds.membership.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            {{-- @livewire('loading-memberships') --}}
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Membership">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.membership.fields.id') }}
                        </th>
                        {{-- <th>
                            {{ trans('cruds.lead.fields.member_code') }}
                        </th> --}}
                        <th>
                            {{ trans('cruds.membership.fields.member') }}
                        </th>
                        {{-- <th>
                            {{ trans('cruds.member.fields.phone') }}
                        </th> --}}
                        <th>
                            {{ trans('global.gender') }}
                        </th>
                        <th>
                            {{ trans('cruds.membership.fields.start_date') }}
                        </th>
                        <th>
                            {{ trans('cruds.membership.fields.end_date') }}
                        </th>
                        <th>
                            {{ trans('cruds.membership.fields.trainer') }}
                        </th>
                        <th>
                            {{ trans('cruds.membership.fields.service') }}
                        </th>
                        <th>
                            {{ trans('cruds.branch.title_singular') }}
                        </th>
                        @if (config('domains')[config('app.url')]['sports_option'] == true)
                        <th>
                            {{ trans('global.sport') }}
                        </th>
                        @endif
                        <th>
                            {{ trans('global.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.membership.fields.sales_by') }}
                        </th>
                        <th>
                            {{ trans('global.remaining_sessions') }}
                        </th>
                        <th>
                            {{ trans('global.last_attendance') }}
                        </th>
                        <th>
                            {{ trans('cruds.membership.fields.created_at') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- <div class="modal fade" id="sendReminder" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <form action="{{ route('admin.reminder.expiredMemberships') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ trans('cruds.reminder.fields.action') . ' ' }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="member_status_id">{{ trans('cruds.status.title_singular') }}</label>
                            <select name="member_status_id" id="member_status_id" class="form-control">
                                <option>Select</option>
                                @foreach ($memberStatuses as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="due_date">{{ trans('global.due_date') }}</label>
                            <input type="date" name="due_date" id="due_date" class="form-control" value="">
                        </div>

                        <div class="form-group">
                            <label for="member_ids">{{ trans('cruds.member.title_singular') }}</label>
                            <div style="padding-bottom: 4px">
                                <span class="btn btn-info btn-xs select-all"
                                    style="border-radius: 0">{{ trans('global.select_all') }}</span>
                                <span class="btn btn-info btn-xs deselect-all"
                                    style="border-radius: 0">{{ trans('global.deselect_all') }}</span>
                            </div>
                            <select name="member_ids[]" id="member_ids" class="form-control select2" multiple="">
                                @foreach ($members as $member_id => $member_name)
                                    <option value="{{ $member_id }}">{{ $member_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="notes">{{ trans('cruds.lead.fields.notes') }}</label>
                            <textarea name="notes" id="notes" rows="7" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times-circle"></i>
                            Close</button>
                        <button type="submit" class="btn btn-success"><i class="fa fa-check-circle"></i> Confirm</button>
                    </div>
                </div>
            </div>
        </form>
    </div> --}}

@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            $("#lockers").css('display', 'none');
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

            let dtOverrideGlobals = {
                buttons: dtButtons,
                processing: true,
                serverSide: true,
                retrieve: true,
                searching:true,
                aaSorting: [],
                ajax: "{!! route('admin.memberships.index', request()->all()) !!}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                    // {
                    //     data: 'member_code',
                    //     name: 'member_code'
                    // },
                    {
                        data: 'member_name',
                        name: 'member.member_code'
                    },
                    // {
                    //     data: 'member_phone',
                    //     name: 'member.phone'
                    // },
                    {
                        data: 'member_gender',
                        name: 'member.gender'
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
                        data: 'trainer_name',
                        name: 'trainer.name'
                    },
                    {
                        data: 'service_pricelist_name',
                        name: 'service_pricelist.name'
                    },
                    {
                        data: 'branch_name',
                        name: 'branch_name'
                    },
                    @if (config('domains')[config('app.url')]['sports_option'] == true)
                    {
                        data: 'sport',
                        name: 'sport.name',
                        searchable: false
                    },
                    @endif
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'sales_by_name',
                        name: 'sales_by.name'
                    },
                    {
                        data: 'remaining_sessions',
                        name: 'remaining_sessions'
                    },
                    {
                        data: 'last_attendance',
                        name: 'last_attendance'
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
                pageLength: 20,
            };
            let table = $('.datatable-Membership').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

            // function getCount (){
            //   $("#membershipsCounter").text(table.ajax.json().recordsTotal);
            //}

            // setTimeout(getCount, 5000)

        });
    </script>
@endsection
