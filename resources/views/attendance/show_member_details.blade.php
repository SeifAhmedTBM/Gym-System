@extends('layouts.attendance')
@section('styles')
    <style>
        body {
            background: url("{{ asset('images/s1.jpg') }}") center center/cover no-repeat fixed;
            height: 100vh;
        }

        #invitation {
            z-index:99999;
        }

        .list-group-item {
            font-size: 20px !important;
        }

        .modal-backdrop {
            z-index:99998 !important;
        }
     
        div.firstOneShowOnly:nth-of-type(3) {
            display:none;
        }
        div.firstOneShowOnly:nth-of-type(4) {
            display:none;
        }

    </style>
@endsection
@section('content')
    <div id="particles"></div>
    <div id="bg"></div>
    <div class="container">
        <div class="row" style="z-index: 99999;position: relative;">
            <div class="col-md-12 mx-auto">
                @if ($img = App\Models\Setting::first())
                    <div class="col-md-12 mt-3 mb-2 text-center" style="z-index: 99999;position: relative;">
                        <a href="{{ route('attendance_take.index') }}">
                            <img width="200" src="{{ asset('images/' . $img->login_logo) }}" class="mb-2"
                                alt="Login Logo">
                        </a>
                    </div>
                @endif
                <div class="card shadow-sm">
                    <div class="card-header font-weight-bold">
                        <div class="row">
                            <div class="col-md-10">
                                <h5><i class="fa fa-list"></i> {{ trans('global.membership_details') }}</h5>
                            </div>

                            <div class="col-md-2">
                                {{-- @if(isset($main_membership)) --}}
                                    @if (isset($main_membership) && $main_membership->invitations_count < $main_membership->service_pricelist->invitation)
                                        <button class="btn btn-info btn-block" data-target="#invitation" data-toggle="modal">
                                            <i class="fa fa-plus-circle"></i> {{ trans('global.invite') }}
                                        </button>
                                    @endif
                                {{-- @endif --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (Session::has('invitation'))
                            <div class="alert alert-success font-weight-bold text-center">
                                {{ session('invitation') }}
                            </div>
                        @endif
                        <div class="member_details">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <h5 class="font-weight-bold my-3 text-left">
                                        {{ trans('global.details') }}
                                    </h5>
                                    
                                    {{-- Personal Details  --}}
                                    <div class="row">
                                        <div class="col-md-4 mx-auto">
                                            
                                            @if (isset($main_membership)&&$main_membership->member->photo)
                                                <div style="width:250px;height:250px; background:url('{{ $main_membership->member->photo->url }}') center center/cover no-repeat; "
                                                    class="rounded-circle"></div>
                                            @else
                                                <img style="width:250px;height:250px" src="{{ asset('images/user.png') }}"
                                                    alt="Member Photo" class="rounded-circle">
                                            @endif
                                        </div>
                                        <div class="col-md-8">
                                            <ul class="list-group shadow-sm font-weight-bold">
                                                <li class="list-group-item">
                                                    {{ trans('global.name') }} : {{ $main_membership->member->name ?? '-' }} - Code : ( {{ \App\Models\Setting::first()->member_prefix.$main_membership->member->member_code ?? '-' }} ) - Contact : {{ $main_membership->member->phone ?? '-' }}
                                                </li>
                                                <li class="list-group-item">
                                                    {{ trans('global.last_attendance') }} : {!! date('Y-m-d  -  g:i A',strtotime($main_membership->last_attendance)) ?? '<span class="badge badge-danger">No attendance</span>' !!}
                                                </li>
                                                <li class="list-group-item">
                                                    {{ trans('cruds.lead.fields.sales_by') }} : {{ $main_membership->sales_by->name ?? '-' }}
                                                </li>
                                                @forelse ($active_memberships as $index => $active_membership)

                                                    <li class="list-group-item" style="background-color:{{$colors[$index]}}">
                                                        Membership #{{$index+1}} : {{ $active_membership->service_pricelist->name ?? '-' }} 
                                                        <span class="badge badge-{{ \App\Models\Membership::STATUS[$main_membership->status] }}">
                                                            {{ \App\Models\Membership::SELECT_STATUS[$main_membership->status] }}
                                                        </span>
                                                    </li>
                                                    <li class="list-group-item" style="background-color:{{$colors[$index]}}"> Start / End Date :
                                                        {{ $main_membership->start_date }} / {{ $main_membership->end_date }}
                                                    </li>
                                                    <li class="list-group-item" style="background-color:{{$colors[$index]}}">{{ trans('global.sessions') }} :
                                                        {{ $active_membership->attendances->count() }}
                                                            /
                                                            {{ $active_membership->service_pricelist->session_count }}
                                                            ( {{ $active_membership->service_pricelist->session_count - $active_membership->attendances->count() > 0 ? $active_membership->service_pricelist->session_count - $active_membership->attendances->count() : 0 }} Sessions Left) 
                                                    </li>
                                                @empty
                                                <li class="list-group-item" style="background-color:{{$colors[2]}}">
                                                    Membership #1 : {{ $main_membership->service_pricelist->name ?? '-' }} 
                                                    <span class="badge badge-{{ \App\Models\Membership::STATUS[$main_membership->status] }}">
                                                        {{ \App\Models\Membership::SELECT_STATUS[$main_membership->status] }}
                                                    </span>
                                                </li>
                                                <li class="list-group-item" style="background-color:{{$colors[2]}}"> Start / End Date :
                                                    {{ $main_membership->start_date }} / {{ $main_membership->end_date }}
                                                </li>
                                                <li class="list-group-item" style="background-color:{{$colors[2]}}">{{ trans('global.sessions') }} :
                                                    {{ $main_membership->attendances->count() }}
                                                        /
                                                        {{ $main_membership->service_pricelist->session_count }}
                                                        ( {{ $main_membership->service_pricelist->session_count - $main_membership->attendances->count() > 0 ? $main_membership->service_pricelist->session_count - $main_membership->attendances->count() : 0 }} Sessions Left) 
                                                </li>
                                                @endforelse
                                                
                                                
                                          
                                                @if ($main_membership->invitations_count < $main_membership->service_pricelist->invitation)
                                                    <li class="list-group-item">
                                                        {{ trans('cruds.pricelist.fields.invitation_count') }} : {{ $main_membership->invitations_count }} / {{ $main_membership->service_pricelist->invitation ?? 0 }}
                                                    </li>
                                                @endif
                                                
                                                {{-- <li class="list-group-item">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            {{ trans('cruds.pricelist.fields.free_sessions') }} : 
                                                            {{ $main_membership->free_sessions_count }} / 
                                                            {{ $main_membership->service_pricelist->free_sessions ?? 0 }}
                                                        </div>
                                                        @if ($main_membership->free_sessions_count < $main_membership->service_pricelist->free_sessions)
                                                        <div class="col-md-6">
                                                            <button class="btn btn-success btn-sm" type="button" data-toggle="modal" data-target="#free_session">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </li> --}}
                                                
                                                @if(config('domains')[config('app.url')]['due_payment'] ==  True)
                                                    <li class="list-group-item">
                                                        {{ trans('global.due_payment') }} : {{ $main_membership->invoice->rest ?? 0 }}
                                                    </li>
                                                @endif

                                                @if ($last_note)
                                                    <li class="list-group-item">
                                                        {{ trans('cruds.lead.fields.notes') }} : {{ $last_note->notes ?? 'No Notes Available' }}
                                                    </li>
                                                @endif
                                                {{-- <li class="list-group-item">
                                                    {{ trans('cruds.freezeRequest.title') }} : {{ $main_membership->freezeRequests->count() ?? '-' }}
                                                </li> --}}
                                                {{-- <li class="list-group-item">
                                                    {{ trans('cruds.freezeRequest.title') }} : {{ $freezeRequest->start_date . ' : ' . $freezeRequest->end_date}}
                                                </li> --}}
                                                @foreach ($main_membership->service_pricelist->serviceOptionsPricelist as $service_option)
                                                        <li class="list-group-item">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    {{ $service_option->service_option->name ?? '' }} : 
                                                            
                                                                    <span class="inbody-count">
                                                                        {{ $main_membership->membership_service_options()->where('service_option_pricelist_id',$service_option->service_option_id)->count() }}
                                                                    </span>
                                                                    / <span class="service-count">{{ $service_option->count }}</span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <button class="btn btn-success btn-sm" type="button" {{ $main_membership->membership_service_options()->where('service_option_pricelist_id',$service_option->service_option_id)->count() == $service_option->count ? 'disabled' : '' }} data-membership-id="{{ $main_membership->id }}" data-service={{ $service_option->service_option_id }} id="counterBtn" onclick="submitService(this)"><i class="fa fa-plus"></i></button>
                                                                </div>
                                                            </div>
                                                        </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    
                                
                                    {{-- Session  --}}
                                    @foreach ($active_memberships as $membership)
                                        @switch($membership->service_pricelist->service->service_type->session_type)
                                 
                                            @case('sessions')
                                                <div class="">
                                                    <h5>Memberships ( Sessions )</h5>
                                                    <div class="form-group row">
                                                        <div class="col-md-3 form-group">
                                                            <form action="{{ route('attendance.take') }}" method="post" id="form{{$membership->id}}">
                                                                @csrf
                                                                @method('POST')
                                                                <input type="hidden" name="membership_id" value="{{ $membership->id }}">
                                                                <input type="hidden" name="member_id" value="{{ $membership->member->id }}">
                                                                <button class="btn btn-info font-weight-bold btn-block" type="button" onclick="letMeIn();checkFreeze(this)" data-membership-id="{{ $membership->id }}"
                                                                    {{ $membership->status == 'expired' || $membership->status == 'refunded' ? 'disabled' : '' }}>
                                                                    {{ $membership->service_pricelist->name }} <br>
                                                                    End : {{ date('Y-m-d', strtotime($membership->end_date)) }}
                                                                    <br>
                                                                    Status : {{ $membership->status }} <br>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                @break
                                            @case('group_sessions')
                                                    <div class="">
                                                        <h5>Memberships ( Group Sessions )</h5>
                                                        <br>
                                                        <h4> <b> {{ $membership->service_pricelist->name ?? '-' }} </b></h4>
                                                        <div class="form-group row ">
                                                            @foreach ($schedules as $schedule)
                                                                <div class="col-md-4 form-group">
                                                                    <form action="{{ route('attendance.take') }}" method="post" id="form{{$membership->id}}">
                                                                        @csrf
                                                                        @method('POST')
                                                                        <input type="hidden" name="membership_id"  value="{{ $membership->id }}">
                                                                        <input type="hidden" name="member_id" value="{{ $membership->member->id }}">
                                                                        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                                                        <input type="hidden" name="trainer_id" value="{{ $schedule->trainer->id }}">
                                                                        
                                                                        <button class="btn btn-block font-weight-bold text-white shadow-sm"
                                                                            style="background:{{ $schedule->session->color }};"
                                                                            onclick="letMeIn()">
                                                                            {{ $schedule->session->name }} <br>
                                                                            ({{ $schedule->trainer->name }})
                                                                            <br>
                                                                            ( {{ date('g:i A', strtotime($schedule->timeslot->from)) }} )
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @break
                                            @default
                                        @endswitch
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($member->pop_messages->count() == 1)
        <!-- Modal -->
        <div class="modal fade" id="popMessageModal" tabindex="-1" aria-labelledby="popMessageModalLabel" aria-hidden="true" style="z-index:999999!important">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="popMessageModalLabel">{{ trans('global.create') }} {{ trans('global.pop_messages') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    {!! Form::open(['method' => 'POST', 'url' => route('admin.popMessageReply.store',$member->pop_messages()->first()->id)]) !!}
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
@endsection
@section('scripts')
    @if ($member->pop_messages->count() == 1)
        <script>
            $('#popMessageModal').modal('show');
        </script>
    @endif
    <script>
        function submitService(button)
        {
            let membership_id = $(button).data('membership-id');
            let service_option_pricelist_id = $(button).data('service');
            var url = "{{ route('admin.addMembershipServiceOption', [':membership_id', ':service_option_pricelist_id']) }}";
            url = url.replace(':membership_id', membership_id);
            url = url.replace(':service_option_pricelist_id', service_option_pricelist_id);
            $.ajax({
                method:'GET',
                url: url,
                beforeSend: function() {
                    $("#counterBtn").attr('disabled', 'disabled');
                },
                success: function(response) {
                    $(".inbody-count").html(response.new_counter);
                    if(parseInt($(".service-count").html()) == parseInt($(".inbody-count").html())) {
                        $("#counterBtn").attr('disabled', 'disabled');
                    }
                },
                complete: function() {
                    if(parseInt($(".service-count").html()) == parseInt($(".inbody-count").html())) {
                        $("#counterBtn").attr('disabled', 'disabled');
                    }else {
                        $("#counterBtn").removeAttr('disabled');
                    }
                    location.reload();
                }
            });
        }

        function checkFreeze(button)
        {
            let membership_id = $(button).data('membership-id');
            let settings = '{{$setting}}';
            var url = "{{ route('checkFreeze',':membership_id') }}";
            url = url.replace(':membership_id',membership_id);
            
            $.ajax({
                method:'GET',
                url:url,
                success: function(response)
                {
                    // console.log(response);
                    $('#membership_id').val(membership_id);
                    if (response.freeze != null) {
                        $('.frozen').removeClass('d-none');
                    }
                    if (response.freeze != null && response.attend != null) 
                    {
                        if (response.freeze != null) {
                            $('#start_date').text(response.freeze.start_date);
                            $('#end_date').text(response.freeze.end_date);
                            $('#membership').text(response.freeze.membership.service_pricelist.name);
                            $('#freeze_id').val(response.freeze.id);
                            $('#membership_id').val(response.freeze.membership_id);
                        }
                       
                        // if (parseInt(settings) == 1) {
                           
                        // }
                        $('.locker').removeClass('d-none');
                        $('#has_locker').modal('show');
                     
                   
                    }else if (parseInt(settings) == 1)
                    {
                        // alert(parseInt(settings));
                        // $('#membership_id').val(membership_id);
                        $('.locker').removeClass('d-none');
                        $('#has_locker').modal('show');
                    }else{
                        $(`#form${membership_id}`).submit();
                        $(':button').attr('disabled',true);
                    }                    
                }
            });
        }

        function letMeIn()
        {
            $.ajax({
                url: "http://192.168.1.110:8080",
                type: 'GET',
                dataType: 'json', // added data type
                success: function(res) 
                {
                    console.log(res);
                }
            });
        }
    </script>
@endsection
