@extends('layouts.attendance')
@section('styles')
    <style>
        body {
            background: url("{{ asset('images/s1.jpg') }}") center center/cover no-repeat fixed;
            height: 100vh;
        }

        .list-group-item {
            font-size: 20px !important;
        }

        .modal-backdrop {
            z-index:99998 !important;
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
                        <h5><i class="fa fa-list"></i> {{ trans('global.membership_details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="member_details">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <h5 class="font-weight-bold my-3 text-left">
                                        {{ trans('global.details') }}
                                    </h5>

                                    {{-- Personal Details  --}}

                                    <div class="row">
                                        <div class="col-md-4 mx-auto">
                                            @if ($main_membership->member->photo)
                                                <div style="width:250px;height:250px; background:url('{{ $main_membership->member->photo->url }}') center center/cover no-repeat; "
                                                    class="rounded-circle"></div>
                                            @else
                                                <img style="width:250px;height:250px" src="{{ asset('images/user.png') }}"
                                                    alt="Member Photo" class="rounded-circle">
                                            @endif
                                            <div class="form-group m-3">
                                                <h3 class="font-weight-bold"> {{ $main_membership->member->name ?? '-' }}</h3>
                                                <h3 class="font-weight-bold">{{ \App\Models\Setting::first()->member_prefix.$main_membership->member->member_code ?? '-' }}</h3>
                                                <h3 class="font-weight-bold">{{ $main_membership->member->phone ?? '-' }}</h3>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <ul class="list-group shadow-sm font-weight-bold">
                                                <li class="list-group-item">
                                                    {{ trans('global.main_membership') }} : {{ $main_membership->service_pricelist->name ?? '-' }}
                                                </li>
                                                <li class="list-group-item">
                                                    {{ trans('global.status') }} : 
                                                    <span class="badge badge-{{ \App\Models\Membership::STATUS[$main_membership->status] }}">
                                                        {{ \App\Models\Membership::SELECT_STATUS[$main_membership->status] }}
                                                    </span>
                                                </li>
                                                <li class="list-group-item">
                                                    {{ trans('global.start_date') }} : {{ $main_membership->start_date }}
                                                </li>
                                                <li class="list-group-item">
                                                    {{ trans('global.end_date') }} : {{ $main_membership->end_date }}
                                                </li>
                                                <li class="list-group-item">{{ trans('global.sessions') }} :
                                                    {{ $main_membership->attendances->count() }}
                                                        /
                                                        {{ $main_membership->service_pricelist->session_count }}
                                                        ( {{ $main_membership->service_pricelist->session_count - $main_membership->attendances->count() > 0 ? $main_membership->service_pricelist->session_count - $main_membership->attendances->count() : 0 }})
                                                </li>
                                                <li class="list-group-item">
                                                    {{ trans('global.due_payment') }} : {{ $main_membership->invoice->rest ?? '-' }}
                                                </li>
                                                <li class="list-group-item">
                                                    {{ trans('cruds.freezeRequest.title') }} : {{ $main_membership->freezeRequests->count() ?? '-' }}
                                                </li>
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
                                    @foreach ($member->memberships as $membership)
                                        @switch($membership->service_pricelist->service->service_type->session_type)
                                            @case('non_sessions')
                                                <h5>Memberships ( Non Session)</h5>
                                                <div class="form-group row">
                                                    <div class="col-md-3">
                                                        <form action="{{ route('attendance.take') }}" method="post" id="form{{ $membership->id }}">
                                                            @csrf
                                                            @method('POST')
                                                            <input type="hidden" name="membership_id" value="{{ $membership->id }}">
                                                            <input type="hidden" name="member_id" value="{{ $membership->member->id }}">
                                                            <button class="btn btn-success font-weight-bold btn-block" type="button" onclick="checkFreeze(this)" data-membership-id="{{ $membership->id }}"
                                                                {{ $membership->status == 'expired' || $membership->status == 'refunded' ? 'disabled' : '' }}>
                                                                {{ $membership->service_pricelist->name }} <br>
                                                                End : {{ date('Y-m-d', strtotime($membership->end_date)) }}
                                                                <br>
                                                                Status : {{ $membership->status }} <br>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                @break
                                            @case('sessions')
                                                    <h5>Memberships ( Sessions )</h5>
                                                    <div class="form-group row">
                                                        <div class="col-md-3">
                                                            <form action="{{ route('attendance.take') }}" method="post" id="form{{$membership->id}}">
                                                                @csrf
                                                                @method('POST')
                                                                <input type="hidden" name="membership_id" value="{{ $membership->id }}">
                                                                <input type="hidden" name="member_id" value="{{ $membership->member->id }}">
                                                                <button class="btn btn-info font-weight-bold btn-block" type="button" onclick="checkFreeze(this)" data-membership-id="{{ $membership->id }}"
                                                                    {{ $membership->status == 'expired' || $membership->status == 'refunded' ? 'disabled' : '' }}>
                                                                    {{ $membership->service_pricelist->name }} <br>
                                                                    End : {{ date('Y-m-d', strtotime($membership->end_date)) }}
                                                                    <br>
                                                                    Status : {{ $membership->status }} <br>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @break
                                            @case('group_sessions')
                                                    <h5>Memberships ( Group Sessions )</h5>
                                                    <div class="form-group row">
                                                        @foreach ($schedules as $schedule)
                                                            <div class="col-md-3">
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
                                                                        {{ $membership->service_pricelist->name ?? '-' }} <br>
                                                                        {{ $schedule->session->name }} <br>
                                                                        ({{ $schedule->trainer->name }})
                                                                        <br>
                                                                        ( {{ date('g:i A', strtotime($schedule->timeslot->from)) }} )
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        @endforeach
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
@endsection
@section('scripts')
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
                }
            });
        }

        function checkFreeze(button)
        {
            let membership_id = $(button).data('membership-id');
            var url = "{{ route('admin.checkFreeze',':membership_id') }}";
            url = url.replace(':membership_id',membership_id);
            $.ajax({
                method:'GET',
                url:url,
                success: function(response)
                {
                    $('#membership_id').val(membership_id);
                    if (response.freeze != null) {
                        $('.frozen').removeClass('d-none');
                    }
                    if (response.freeze != null || response.attend != null) 
                    {
                        // $('#freezeModal').modal('show');
                        $('#has_locker').modal('show');
                        $('#start_date').text(response.freeze.start_date);
                        $('#end_date').text(response.freeze.end_date);
                        $('#membership').text(response.freeze.membership.service_pricelist.name);
                        $('#freeze_id').val(response.freeze.id);
                        $('#membership_id').val(response.freeze.membership_id);
                    }else if ({{$setting}})
                    {
                        // $('#membership_id').val(membership_id);
                        $('#has_locker').modal('show');
                    }else{
                        $(`#form${membership_id}`).submit();
                        $(':button').attr('disabled',true);
                    }                    
                }
            });
        }
    </script>
@endsection
