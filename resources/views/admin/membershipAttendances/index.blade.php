@extends('layouts.admin')
@section('content')
    @can('membership_attendance_create')
        <div class="row form-group">
            <div class="col-lg-9">
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                    {{ trans('global.app_csvImport') }}
                </button>
                @include('admin_includes.filters', [
                    'columns' => [
                        'name' => ['label' => 'Membership' , 'type' => 'select' , 'data' => $service_pricelists, 'related_to' => 'membership.service_pricelist'],
                        'member_code'   => ['label' => 'Member Code', 'type'    => 'number' , 'related_to'  => 'membership.member'],
                        'phone'         => ['label' => 'Phone'      , 'type'    => 'number' , 'related_to'  => 'membership.member'],
                        'locker'        => ['label' => 'Locker'      , 'type'    => 'number' ],
                        'branch_id'     => ['label' => 'Branch', 'type' => 'select', 'data' => $branches],
                        'created_at'    => ['label' => 'Created at' , 'type'    => 'date'   , 'from_and_to' => true]
                    ],
                    'route' => 'admin.membership-attendances.index'
                ])

                @can('export_membership_attendances')
                    <a href="{{ route('admin.membership_attendances.export',request()->all()) }}" class="btn btn-info">
                        <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                    </a>
                @endcan
                
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-body" style="text-align:center">
                        <h2 class="text-center">{{ trans('global.attendances') }}</h2>
                        <h2 class="text-center">{{ $counter }}</h2>
                        <small class="text-center text-danger"> Attendance of Current Month</small>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-header">
            <h5>{{ trans('cruds.membershipAttendance.title_singular') }} {{ trans('global.list') }}</h5>
        </div>

        <div class="card-body">
            <table
                class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-MembershipAttendance">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.membershipAttendance.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.membershipAttendance.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.membershipAttendance.fields.membership') }}
                        </th>
                        <th>
                            {{ trans('cruds.branch.title_singular') }}
                        </th>
                        <th>
                            {{ trans('global.trainer') }}
                        </th>

                        <th>
                            {{ trans('cruds.membershipAttendance.fields.sign_in') }}
                        </th>

                        <th>
                            {{ trans('cruds.membershipAttendance.fields.sign_out') }}
                        </th>

                        <th>
                           Locker
                        </th>

                        <th>
                            {{ trans('cruds.status.title_singular') }}
                        </th>
                        <th>
                            {{ trans('global.created_at') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editSigninAndSignoutModal" tabindex="-1" aria-labelledby="editSigninAndSignoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSigninAndSignoutModalLabel">{{ trans('global.edit') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(['method' => 'PUT', 'id' => 'editSigninAndSignoutForm']) !!}
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-4 form-group">
                            {!! Form::label('sign_in', trans('cruds.membershipAttendance.fields.sign_in')) !!}
                            {!! Form::time('sign_in', null, ['class' => 'form-control', 'id' => 'edit_sign_in']) !!}
                        </div>
                        <div class="col-md-4 form-group">
                            {!! Form::label('sign_out', trans('cruds.membershipAttendance.fields.sign_out')) !!}
                            {!! Form::time('sign_out', null, ['class' => 'form-control', 'id' => 'edit_sign_out']) !!}
                        </div>
                        <div class="col-md-4 form-group">
                            {!! Form::label('locker', trans('cruds.membershipAttendance.fields.locker')) !!}
                            {!! Form::text('locker', null, ['class' => 'form-control', 'id' => 'edit_locker']) !!}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        {{ trans('global.close') }}
                    </button>
                    <button type="submit" class="btn btn-success">{{ trans('global.update') }}</button>
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
            @can('membership_attendance_delete')
                let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
                let deleteButton = {
                text: deleteButtonTrans,
                url: "{{ route('admin.membership-attendances.massDestroy') }}",
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
                ajax: "{!! route('admin.membership-attendances.index',request()->all()) !!}",
                columns: [{
                        data: 'placeholder',
                        name: 'placeholder'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    },
                  
                    {
                        data: 'membership_member',
                        name: 'membership.member.name'
                    },
                    {
                        data: 'membership',
                        name: 'membership'
                    },
                    {
                        data: 'branch_name',
                        name: 'branch.name'
                    },
                    {
                        data: 'trainer',
                        name: 'trainer'
                    },
                   
                    {
                        data: 'sign_in',
                        name: 'sign_in'
                    },
                   
                    {
                        data: 'sign_out',
                        name: 'sign_out'
                    },

                    {
                        data: 'locker',
                        name: 'locker'
                    },
                   
                    {
                        data: 'status',
                        name: 'status'
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
            let table = $('.datatable-MembershipAttendance').DataTable(dtOverrideGlobals);
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });

        });


        function editSigninAndSignout(button){ 
            let sign_in     = $(button).data('sign-in');
            let sign_out    = $(button).data('sign-out');
            let locker      = $(button).data('locker');
            let formURL     = $(button).data('update');
            let getURL      = $(button).data('get-url');

            $("#editSigninAndSignoutForm").attr('action', formURL);
            $.ajax({
                method : "GET",
                url : getURL,
                success: function (response) {
                    $("#edit_sign_in").val(response.sign_in);
                    $("#edit_sign_out").val(response.sign_out);
                    $("#edit_locker").val(response.membership_attendances.locker);
                }
            })
        }

    </script>
@endsection
