@extends('layouts.admin')
@section('content')
    @if ($today_birthdays->isNotEmpty())
        <div class="alert alert-success font-weight-bold mb-4">
            <h4> <i class="fas fa-birthday-cake"></i> {{ trans('global.today_birthdays') }} : </h4>
            <ul class="m-0">
                @foreach ($today_birthdays as $member)
                    <li>
                        <a class="text-decoration-none alert-link" href="{{ route('admin.members.show', $member->id) }}">
                            {{ $member->name }} ( {{ $member->dob }} )
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="form-group row">
        <div class="col-lg-9">
            @can('member_create')
                <a class="btn btn-success" href="{{ route('admin.members.create') }}">
                    {{ trans('global.add') }} {{ trans('cruds.member.title_singular') }}
                </a>
            @endcan

            @can('member_import')
                <button class="btn btn-warning" data-toggle="modal" data-target="#importMembers">
                    <i class="fa fa-upload"></i> {{ trans('global.transfer_sales_data') }}
                </button>
            @endcan

            @can('export_members')
                <a href="{{ route('admin.members.export',request()->all()) }}" class="btn btn-info">
                    <i class="fa fa-download"></i> {{ trans('global.export_excel') }}
                </a>
            @endcan
            
            @can('member_filter')
                @include('admin_includes.filters', [
                    'columns' => [
                        'name'          => ['label' => 'Name', 'type' => 'text'],
                        'phone'         => ['label' => 'Phone', 'type' => 'number'],
                        'member_code'   => ['label' => 'Member Code', 'type' => 'number'],
                        'national'      => ['label' => 'National ID', 'type' => 'number'],
                        'status_id'     => ['label' => 'Status', 'type' => 'select', 'data' => $statuses],
                        'source_id'     => ['label' => 'Source', 'type' => 'select', 'data' => $sources],
                        'branch_id'     => ['label' => 'Branch', 'type' => 'select', 'data' => $branches],
                        'gender'        => ['label' => 'Gender', 'type' => 'select', 'data' => ['male' => 'Male', 'female' => 'Female']],
                        'sales_by_id'   => ['label' => 'Sales By', 'type' => 'select', 'data' => $sales],
                        'created_at'    => ['label' => 'Created at', 'type' => 'date', 'from_and_to' => true]
                    ],
                        'route' => 'admin.members.index'
                    ])
            @endcan

            @can('member_import')
                @include('admin.members.import-members-modal',['route' => 'admin.import.leads_and_members'])
            @endcan
        </div>
        @can('member_counter')
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center">{{ trans('cruds.member.title_singular') }}</h2>
                        <h2 class="text-center">{{ $members }}</h2>
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
            {{ trans('cruds.member.title_singular') }} {{ trans('global.list') }}
        </div>

        <div class="card-body">
            <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Member">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.member.fields.photo') }}
                        </th>
                      
                        <th>
                            {{ trans('cruds.member.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.branch.title_singular') }}
                        </th>
                        <th>
                            {{ trans('cruds.member.fields.source') }}
                        </th>
                        <th>
                            {{ trans('cruds.member.fields.notes') }}
                        </th>
                        <th>
                            {{ trans('cruds.member.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.member.fields.sales_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.refund.fields.created_by') }}
                        </th>
                        <th>
                            {{ trans('cruds.member.fields.created_at') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

     {{-- members actions modal --}}
    <div class="modal fade" id="takeMemberAction" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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

    {{-- Send SMS modal --}}
    <div class="modal fade" id="sendMessage" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('global.send_sms') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" class="modalForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="message">{{ trans('global.message') }}</label>
                            <textarea name="message" id="message" rows="7" class="form-control"></textarea>
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


    <!-- Modal -->
    <div class="modal fade" id="memberRequestModal" tabindex="-1" aria-labelledby="memberRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="memberRequestModalLabel">{{ trans('global.create') }} {{ trans('global.member_request') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(['method' => 'POST', 'url' => route('admin.member-requests.store')]) !!}
                <div class="modal-body">
                    <input type="hidden" name="member_id" id="member_id_input">
                    <div class="form-group">
                        {!! Form::label('subject', trans('global.subject'), ['class' => 'required']) !!}
                        {!! Form::text('subject', old('subject'), ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('comment', trans('global.comment'), ['class' => 'required']) !!}
                        {!! Form::textarea('comment', old('comment'), ['class' => 'form-control', 'rows' => 3]) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                        <i class="fa fa-times-circle"></i> {{ trans('global.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check-circle"></i> {{ trans('global.create') }}
                    </button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    @include('admin.members.transfer_to_branch')
@endsection
@section('scripts')
    @parent
    <script>
        $(function() {
            let dtOverrideGlobals = {
                buttons: [],
                processing: true,
                serverSide: true,
                retrieve: true,
                responsive: true,
                searching:true,
                aaSorting: [],
                ajax: "{!! route('admin.members.index',request()->all()) !!}",
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
                        name: 'member_code',
                    },
                    {
                        data: 'branch_name',
                        name: 'branch.name'
                    },
                    {
                        data: 'source_name',
                        name: 'source.name'
                    },
                    {
                        data: 'notes',
                        name: 'notes'
                    },
                    {
                        data: 'status_name',
                        name: 'status.name'
                    },
                    {
                        data: 'sales_by_name',
                        name: 'sales_by_name'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
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
                pageLength: 25,
            };
            let table = $('.datatable-Member').DataTable(dtOverrideGlobals);
            
            
            $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e) {
                $($.fn.dataTable.tables(true)).DataTable()
                    .columns.adjust();
            });
            
        });
        
    </script>
    
    <script>
        function takeMemberAction(id) {
            var id = id;
            var url = "{{ route('admin.reminders.takeMemberAction', ':id') }}";
            url = url.replace(':id', id);
            $(".modalForm2").attr('action', url);
        }

        function formSubmit() {
            $('modalForm2').submit();
        }

        function sendMessage(id) {
            var id = id;
            var url = "{{ route('admin.member.sendSms', ':id') }}";
            url = url.replace(':id', id);
            $(".modalForm").attr('action', url);
        }

        function formSubmit() {
            $('modalForm').submit();
        }

        function createMemberRequest(button) {
            let member_id = $(button).data('member-id');
            $("#member_id_input").val(member_id);
        }

        function transfer_to_branch(id)
        {
            var id = id;
            var url = "{{ route('admin.members.transfer-to-branch',':id') }}",
            url = url.replace(':id',id);
            $.ajax({
                method:'GET',
                url:url,
                success:function(response)
                {
                    var route = "{{ route('admin.members.store-transfer-to-branch',':member_id') }}";
                    route = route.replace(':member_id',response.id);
                    
                    $('select[name="from_branch"]').val(response.branch_id);
                    $('form').attr('action',route)
                }
            });
        }
    </script>
@endsection
