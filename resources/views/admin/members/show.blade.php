@extends('layouts.admin')
@section('content')
    @if (date('m-d', strtotime($member->dob)) == date('m-d'))
        <div class="alert alert-success text-center font-weight-bold mb-4">
            <h3 class="m-0 font-weight-bold">
                {{ trans('global.today_is_my_birthday', ['member' => $member->name]) }} <i class="fas fa-birthday-cake"></i>
            </h3>
        </div>
    @endif

    @if ($member_blocked)
        <div class="alert alert-danger text-center font-weight-bold mb-4">
            <h3 class="m-0 font-weight-bold">
                <i class="fa fa-times"></i> Blocked Member
            </h3>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            @if (Session::has('user_invalid'))
                <div class="alert alert-danger font-weight-bold text-center">
                    {{ session('user_invalid') }}
                </div>
            @endif

            @if (Session::has('attended'))
                <div class="alert alert-success font-weight-bold text-center">
                    {{ session('attended') }}
                </div>
            @endif

            @if (Session::has('cannot_attend'))
                <div class="alert alert-danger font-weight-bold text-center">
                    {{ session('cannot_attend') }}
                </div>
            @endif

            @if (Session::has('wrong_time'))
                <div class="alert alert-danger font-weight-bold text-center">
                    {{ session('wrong_time') }}
                </div>
            @endif

            @if (Session::has('membership_dont_have_main_service'))
                <div class="alert alert-danger font-weight-bold text-center">
                    {{ session('membership_dont_have_main_service') }}
                </div>
            @endif
        </div>
    </div>

    @if (isset($main_membership->freezeRequests) && $main_membership->freezeRequests->count())
        <div class="form-group">
            <div class="alert alert-danger text-center">
                <i class="fa fa-exclamation-circle"></i>
                This member in frozen mode !
            </div>
        </div>
    @endif

    @if (isset($last_membership) && $last_membership->status == 'expired')
        <div class="form-group">
            <div class="alert alert-danger text-center">
                <i class="fa fa-exclamation-circle"></i>
                {{ trans('global.membership_is_expired') }} ..
                @can('renew_membership')
                    <a href="{{ route('admin.membership.renew', $last_membership->id) }}" class="btn btn-success btn-xs">
                        <i class="fa fa-plus-circle"></i> {{ trans('cruds.membership.fields.renew') }}
                    </a>
                @endcan
            </div>
        </div>
    @endif

    @if ($invoices->sum('rest') > 0)
        <div class="form-group">
            <div class="alert alert-warning text-center">
                <i class="fa fa-exclamation-circle"></i>
                Due Payments : {{ $invoices_without_refunds->sum('rest') }} EGP
            </div>
        </div>
    @endif

    <div class="form-group row">
        <div class="col-md-2">
            <div class="dropdown">
                <a class="btn btn-primary btn-block dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-toggle="dropdown" aria-expanded="false">
                    {{ trans('global.action') }}
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    @can('member_edit')
                        <a href="{{ route('admin.members.edit', $member->id) }}" class="dropdown-item">
                            <i class="fa fa-edit"></i> &nbsp; {{ trans('global.edit') }}
                        </a>
                    @endcan

                    @can('add_membership')
                        <a href="{{ route('admin.member.addMembership', $member->id) }}" class="dropdown-item"><i
                                class="fa fa-plus-circle"></i> &nbsp; {{ trans('global.add') }}
                            {{ trans('cruds.membership.title_singular') }}
                        </a>
                    @endcan

                    @can('add_notes')
                        <a href="{{ route('admin.note.create', $member->id) }}" class="dropdown-item">
                            <i class="fas fa-plus"></i> &nbsp; {{ trans('cruds.lead.fields.notes') }}
                        </a>
                    @endcan

                    @can('take_action')
                        <button type="button" data-toggle="modal" data-target="#sendMessage"
                            onclick="sendMessage({{ $member->id }})" class="dropdown-item"><i class="fa fa-paper-plane"></i>
                            &nbsp; {{ trans('global.send_sms') }}
                        </button>
                    @endcan

                    @can('edit_card_number')
                        <a class="dropdown-item" href="{{ route('admin.cardNumber.edit', $member->id) }}">
                            <i class="fa fa-edit"></i> &nbsp;
                            {{ trans('global.edit') . ' ' . trans('cruds.member.fields.card_number') }}
                        </a>
                    @endcan

                    @can('member_requests_create')
                        <a class="dropdown-item" data-member-id="{{ $member->id }}" data-toggle="modal"
                            data-target="#memberRequestModal" onclick="createMemberRequest(this)" href="javascript:void(0)">
                            <i class="fas fa-hand-paper"></i> &nbsp; {{ trans('global.member_request') }}
                        </a>
                    @endcan

                    @if (auth()->user()->roles[0]->title != 'Sales' || auth()->user()->roles[0]->title != 'Sales Manager')
                        <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal"
                            data-target="#trainer_reminder">
                            <i class="fa fa-phone"></i> &nbsp; Add Trainer Reminder
                        </a>
                    @endif

                    @can('block_member')
                        <form action="{{ route('admin.member.block', $member->id) }}" method="POST"
                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button type="submit" class="dropdown-item">
                                <i class="fa fa-times"></i> &nbsp; Block
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>

        <div class="col-md-2 " style="@if (config('domains')[config('app.url')]['profile_attendance'] == false) display:none; @endif">
            @if (\App\Models\Setting::first()->has_lockers)
                <button class="btn btn-success btn-block" onclick="takeThisMemberAttendanceLocker()">
                    {{ trans('global.take_attendance') }}
                </button>
            @else
                <button class="btn btn-success btn-block" onclick="takeThisMemberAttendance()">
                    <i class="fas fa-fingerprint"></i> {{ trans('global.take_attendance') }}
                </button>
            @endif
        </div>

        @can('invite_member')
            @if (isset($main_membership) && $main_membership->invitations_count < $main_membership->service_pricelist->invitation)
                <div class="col-md-2">
                    <button class="btn btn-info btn-block" data-target="#invitation" data-toggle="modal">
                        <i class="fa fa-plus-circle"></i> {{ trans('global.invite') }} (
                        {{ $main_membership->invitations_count . ' / ' . $main_membership->service_pricelist->invitation }})
                    </button>
                </div>
            @endif
        @endcan
        

        @isset($main_membership)
            @foreach ($main_membership->service_pricelist->serviceOptionsPricelist as $service_option)
                <div class="col-md-2 form-group">
                    <button class="btn btn-primary btn-block" type="button"
                        {{ $main_membership->membership_service_options()->where('service_option_pricelist_id', $service_option->service_option_id)->count() == $service_option->count? 'disabled': '' }}
                        data-membership-id="{{ $main_membership->id }}" data-service={{ $service_option->service_option_id }}
                        id="counterBtn" onclick="submitService(this)"><i class="fa fa-plus"></i>

                        {{ $service_option->service_option->name ?? '' }}
                        ({{ $main_membership->membership_service_options()->where('service_option_pricelist_id', $service_option->service_option_id)->count() }}
                        / {{ $service_option->count }})
                    </button>
                </div>
            @endforeach
        @endisset
    </div>

    <div class="form-group" style=" @if (config('domains')[config('app.url')]['profile_attendance'] == false) display:none; @endif">
        @include('partials.profile_attendance')
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="nav nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="basic_data-tab" data-toggle="pill" href="#basic_data" role="tab"
                            aria-controls="basic_data" aria-selected="true">
                            <i class="fa fa-user"></i> {{ trans('global.basic_data') }}
                        </a>

                        <a class="nav-link" id="invoices-tab" data-toggle="pill" href="#invoices" role="tab"
                            aria-controls="invoices" aria-selected="false">
                            <i class="fas fa-file-invoice-dollar"></i> {{ trans('cruds.invoice.title') }}
                        </a>
                        {{-- @if (config('domains')[config('app.url')]['add_to_class_in_invoice'] == false) --}}
                        <a class="nav-link" id="memberships-tab" data-toggle="pill" href="#memberships" role="tab"
                            aria-controls="memberships" aria-selected="false">
                            <i class="fa fa-address-book"></i> {{ trans('cruds.membership.title') }}
                        </a>
                        {{-- @endif --}}
                        @if (config('domains')[config('app.url')]['add_to_class_in_invoice'] == true)
                            <a class="nav-link" id="membership-schedules-tab" data-toggle="pill"
                                href="#membershipSchedules" role="tab" aria-controls="membershipSchedules"
                                aria-selected="false">
                                <i class="fa fa-address-book"></i> Membership Schedules
                            </a>

                            <a class="nav-link" id="session_attendances-tab" data-toggle="pill"
                                href="#session_attendances" role="tab" aria-controls="session_attendances"
                                aria-selected="false">
                                <i class="fas fa-fingerprint"></i> Session {{ trans('global.attendances') }}
                            </a>
                        @endif

                        <a class="nav-link" id="notes-tab" data-toggle="pill" href="#member_notes" role="tab"
                            aria-controls="notes" aria-selected="false">
                            <i class="fa fa-file"></i> {{ trans('global.notes') }}
                        </a>

                        <a class="nav-link" id="freezesRequests-tab" data-toggle="pill" href="#freezesRequests"
                            role="tab" aria-controls="freezesRequests" aria-selected="false">
                            <i class="fas fa-snowflake"></i> {{ trans('cruds.freezeRequest.title') }}
                        </a>

                        @if (config('domains')[config('app.url')]['add_to_class_in_invoice'] == false)
                            <a class="nav-link" id="attendances-tab" data-toggle="pill" href="#attendances"
                                role="tab" aria-controls="attendances" aria-selected="false">
                                <i class="fas fa-fingerprint"></i> {{ trans('global.attendances') }}
                            </a>
                        @endif

                        <a class="nav-link" id="invitations-tab" data-toggle="pill" href="#invitations" role="tab"
                            aria-controls="invitations" aria-selected="false">
                            <i class="fa fa-plus-circle"></i> {{ trans('global.invitations') }}
                        </a>
                        @can('sales_reminders')
                            <a class="nav-link" id="sales-reminder-tab" data-toggle="pill" href="#sales_reminders"
                                role="tab" aria-controls="sales-reminder" aria-selected="false">
                                <i class="fa fa-bell"></i> Sales Reminders
                            </a>
                        @endcan

                        @can('trainer_reminders')
                            <a class="nav-link" id="trainer-reminder-tab" data-toggle="pill" href="#trainer_reminders"
                                role="tab" aria-controls="trainer-reminders" aria-selected="false">
                                <i class="fa fa-bell"></i> Trainer Reminders
                            </a>
                        @endcan

                        <a class="nav-link" id="memberRequests-tab" data-toggle="pill" href="#memberRequests"
                            role="tab" aria-controls="memberRequests" aria-selected="false">
                            <i class="fa fa-question-circle"></i> {{ trans('global.member_requests') }}
                        </a>

                        <a class="nav-link" id="popMessages-tab" data-toggle="pill" href="#popMessages" role="tab"
                            aria-controls="popMessages" aria-selected="false">
                            <i class="fas fa-comment"></i> {{ trans('global.pop_messages') }}
                        </a>

                        {{-- <a class="nav-link" id="messages-tab" data-toggle="pill" href="#messages" role="tab"
                            aria-controls="messages" aria-selected="false">
                            <i class="fa fa-envelope"></i> {{ trans('global.messages') }}
                        </a> --}}

                        {{-- <a class="nav-link" id="freeSessions-tab" data-toggle="pill" href="#freeSessions" role="tab"
                            aria-controls="freeSessions" aria-selected="false">
                            <i class="fa fa-gift"></i> {{ trans('cruds.pricelist.fields.free_sessions') }}
                        </a> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-user"></i> {{ $member->name }}</h4>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="v-pills-tabContent">
                        @include('admin.members.tabs.basic_data')
                        @include('admin.members.tabs.attendances')
                        @include('admin.members.tabs.session_attendances')
                        @can('sales_reminders')
                            @include('admin.members.tabs.sales_reminders')
                        @endcan
                        @can('trainer_reminders')
                            @include('admin.members.tabs.trainer_reminders')
                        @endcan
                        @include('admin.members.tabs.memberships')
                        @include('admin.members.tabs.membership_schedules')
                        @include('admin.members.tabs.invoices')
                        @include('admin.members.tabs.invitations')
                        @include('admin.members.tabs.free_sessions')
                        @include('admin.members.tabs.notes')
                        @include('admin.members.tabs.messages')
                        @include('admin.members.tabs.member_requests')
                        @include('admin.members.tabs.pop_messages')
                        @include('admin.members.tabs.freeze_requests')
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="updateRequestStatus" tabindex="-1" aria-labelledby="updateRequestStatusLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateRequestStatusLabel">{{ trans('global.update_request_status') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    {!! Form::open(['method' => 'PUT', 'id' => 'updateRequestStatusForm']) !!}
                    <div class="modal-body">
                        <h3 class="text-center text-primary font-weight-bold my-3">
                            {{ trans('global.update_request_status?') }}
                        </h3>
                    </div>
                    <input type="hidden" name="status" id="request_status">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                            <i class="fa fa-times-circle"></i> {{ trans('global.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-check-circle"></i> {{ trans('global.submit') }}
                        </button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>

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
                            {{-- <div class="form-group">
                        <label  class="required">{{ trans('cruds.status.title_singular') }}</label>
                        <select name="status_id" id="status_id" class="form-control" required>
                            <option>{{ trans('global.pleaseSelect') }}</option>
                            @foreach ($statuses as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div> --}}
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

        @if ($member->pop_messages->count() == 1)
            <!-- Modal -->
            <div class="modal fade" id="popMessageModal" tabindex="-1" aria-labelledby="popMessageModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="popMessageModalLabel">{{ trans('global.create') }}
                                {{ trans('global.pop_messages') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        {!! Form::open([
                            'method' => 'POST',
                            'url' => route('admin.popMessageReply.store', $member->pop_messages()->first()->id),
                        ]) !!}
                        <div class="modal-body">
                            <div class="form-group">
                                {!! Form::label('message', trans('global.message'), ['class' => 'required']) !!}
                                <h4>{{ $member->pop_messages()->first()->message }}</h4>
                            </div>
                            <div class="form-group">
                                {!! Form::label('reply', trans('global.reply'), ['class' => 'required']) !!}
                                {!! Form::textarea('reply', old('reply'), ['class' => 'form-control', 'rows' => 3]) !!}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">
                                <i class="fa fa-times-circle"></i> {{ trans('global.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> {{ trans('global.reply') }}
                            </button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        @endif

        {{-- assign reminder  --}}
        <div class="modal fade" id="assignReminder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Assign Reminder</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="" method="post" class="assignForm">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <select name="user_id" class="form-control">
                                    <option value="{{ null }}">Please Select</option>
                                    @foreach (App\Models\User::whereRelation('roles', 'title', 'Sales')->pluck('name', 'id') as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
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
        {{-- assign reminder  --}}

        @livewire('search-global-member')
        {{-- trainer reminder  --}}
        <div class="modal fade" id="trainer_reminder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Trainer Reminder</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.take-trainer-reminder', [$member->id]) }}" method="post"
                        class="trainer_reminder_form">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info font-weight-bold">
                                <i class="fa fa-exclamation-circle"></i> {{ trans('global.if_empty_due_date') }} .
                            </div>

                            @livewire('trainer-reminder-actions')

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
        {{-- trainer reminder  --}}
    @endsection

    @section('scripts')
    @if ($member->pop_messages->count() == 1)
        <script>
            $('#popMessageModal').modal('show');
        </script>
    @endif

    <script>
        function takeThisMemberAttendance() {
            let member_code = '{{ $member->member_code }}';
            $("#member_code").val(member_code);
            $("#card_number").val(member_code);
            getMember();
        }

        function takeThisMemberAttendanceLocker() {
            let member_code = '{{ $member->member_code }}';
            $("#member_code").val(member_code);
            $("#card_number").val(member_code);
            getMember();
        }

        function takeMemberAction(id) {
            var id = id;
            var url = '{{ route('admin.reminders.takeLeadAction', ':id') }}';
            url = url.replace(':id', id);
            $(".modalForm2").attr('action', url);
        }

        function formSubmit() {
            $('modalForm2').submit();
        }

        function formSubmit() {
            $('modalForm3').submit();
        }


        function submitService(button) {
            let membership_id = $(button).data('membership-id');
            let service_option_pricelist_id = $(button).data('service');
            var url =
                "{{ route('admin.addMembershipServiceOption', [':membership_id', ':service_option_pricelist_id']) }}";
            url = url.replace(':membership_id', membership_id);
            url = url.replace(':service_option_pricelist_id', service_option_pricelist_id);
            $.ajax({
                method: 'GET',
                url: url,
                beforeSend: function() {
                    $("#counterBtn").attr('disabled', 'disabled');
                },
                success: function(response) {
                    $(".inbody-count").html(response.new_counter);
                    if (parseInt($(".service-count").html()) == parseInt($(".inbody-count").html())) {
                        $("#counterBtn").attr('disabled', 'disabled');
                    }
                },
                complete: function() {
                    if (parseInt($(".service-count").html()) == parseInt($(".inbody-count").html())) {
                        $("#counterBtn").attr('disabled', 'disabled');
                    } else {
                        $("#counterBtn").removeAttr('disabled');
                    }
                    location.reload();
                }
            });
        }

        function selectLead(divElement, leadName) {
            let lead_id = $(divElement).data('id');
            $("#lead_id").val(lead_id);
            $("#search_lead").val(leadName);
            $(".leadsDiv").each(function() {
                $(this).remove();
            })
        }

        function assignReminder(id, user_id)
        {
            var id = id;
            var user_id = user_id;
            var url = '{{ route('admin.reminder.assign', ':id') }}';
            url = url.replace(':id', id);
            $('select[name="user_id"]').val(user_id);
            $(".assignForm").attr('action', url);
        }

        function formSubmit() {
            $('.assignForm').submit();
        }
    </script>
@endsection
