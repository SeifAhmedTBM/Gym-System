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
                            <img width="250" src="{{ asset('images/' . $img->login_logo) }}" class="mb-2"
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
                                    <div class="row">
                                        <div class="col-md-4 text-center imageDiv">
                                            @if ($membership->member->photo)
                                                <div style="width:250px;height:250px; background:url('{{ $membership->member->photo->url }}') center center/cover no-repeat; "
                                                    class="rounded-circle"></div>
                                            @else
                                                <img style="width:250px;height:250px" src="{{ asset('images/user.png') }}"
                                                    alt="Member Photo" class="rounded-circle">
                                            @endif
                                        </div>
                                        <div class="col-md-8">
                                            <ul class="list-group shadow-sm">
                                                <li class="list-group-item">Name :

                                                    <b class="member-name">{{ $membership->member->name }}</b> -
                                                    Code : (<b
                                                        class="member-name">{{ $membership->member->member_code }}</b>)
                                                    -
                                                    Contact : (<b
                                                        class="member-phone">{{ $membership->member->phone }}</b>)
                                                </li>

                                                <li class="list-group-item">Main Membership :
                                                    <b
                                                        class="end-date">{{ $membership->service_pricelist->name }}</b>
                                                </li>
                                                @if (config('domains')[config('app.url')]['sessions_count'] == true)
                                                    @foreach ($memberships as $membership)
                                                        @if ($membership->service_pricelist->service->service_type->session_type == 'sessions')
                                                            @if ($membership->status != 'expired')
                                                                <li class="list-group-item">Sessions :
                                                                    <b class="member-phone">{{ $membership->attendances->count() }}
                                                                        /
                                                                        {{ $membership->service_pricelist->session_count }}
                                                                        (
                                                                        @if ($membership->service_pricelist->session_count - $membership->attendances->count() > 0)
                                                                            {{ $membership->service_pricelist->session_count - $membership->attendances->count() }}
                                                                            Sessions Left
                                                                        @else
                                                                            0
                                                                        @endif )
                                                                    </b>
                                                                </li>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @endif

                                                <li class="list-group-item">Status :
                                                    <span
                                                        class="badge badge-{{ App\Models\Membership::STATUS[$membership->status] }}">
                                                        {{ $membership->status }}
                                                    </span>
                                                </li>

                                                <li class="list-group-item">Start date / End date :
                                                    <b
                                                        class="end-date">{{ date('Y-m-d', strtotime($membership->end_date)) }}</b>
                                                </li>

                                                @if (config('domains')[config('app.url')]['due_payment'] == true)
                                                    <li class="list-group-item">Due Payment :
                                                        <b class="member-phone">{{ $membership->invoice->net_amount - $membership->payments->sum('amount') }}
                                                            LE</b>
                                                    </li>
                                                @endif
                                                {{-- <li class="list-group-item">Paid Amount : 
                                                <b class="member-phone">
                                                    {{ $membership->invoice->payments->sum('amount') }} EGP
                                                </b>
                                            </li>
                                            <li class="list-group-item">Net Amount : 
                                                <b class="member-phone">
                                                    {{ $membership->invoice->net_amount - $membership->invoice->payments->sum('amount') }} EGP
                                                </b>
                                            </li> --}}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h4>{{ trans('cruds.membership.title') }}</h4>
                            <div class="row">
                                @foreach ($memberships as $membership)
                                    @if ($membership->service_pricelist->service->service_type->name != 'Group Sessions')
                                        @if ($membership->service_pricelist->service->service_type->session_type == 'non_sessions')
                                            @if ($membership->status != 'expired')
                                                <div class="col-md-3">
                                                    <form action="{{ route('attendance.take') }}" method="post">
                                                        @csrf
                                                        @method('POST')
                                                        <input type="hidden" name="membership_id" value="{{ $membership->id }}">
                                                        <input type="hidden" name="member_id" value="{{ $membership->member->id }}">
                                                        <button class="btn btn-info font-weight-bold btn-block" onclick="letMeIn()"
                                                            {{ $membership->status == 'expired' ? 'disabled' : '' }}>
                                                            {{ $membership->service_pricelist->service->name }} <br>
                                                            End : {{ date('Y-m-d', strtotime($membership->end_date)) }}
                                                            <br>
                                                            Status : {{ $membership->status }} <br>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        @else
                                            <div class="col-md-3">
                                                <form action="{{ route('attendance.take') }}" method="post">
                                                    @csrf
                                                    @method('POST')
                                                    <input type="hidden" name="membership_id" value="{{ $membership->id }}">
                                                    <input type="hidden" name="member_id" value="{{ $membership->member->id }}">
                                                    <button class="btn btn-info font-weight-bold btn-block"  onclick="letMeIn()"
                                                        {{ $membership->status == 'expired' ? 'disabled' : '' }}>
                                                        {{ $membership->service_pricelist->service->name }} <br>
                                                        End : {{ date('Y-m-d', strtotime($membership->end_date)) }} <br>
                                                        Status : {{ $membership->status }} <br>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                                @if (!is_null($main_group_session))
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h5 class="font-weight-bold my-3">
                                                {{ trans('global.sessions') }} -
                                                {{ $main_group_session->service_pricelist->name }}
                                            </h5>

                                            <div class="row">
                                                @forelse ($schedules as $schedule)
                                                    <div class="col-md-4  mb-3">
                                                        {!! Form::open(['method' => 'POST', 'action' => 'Admin\AttendanceController@takeAttendance']) !!}
                                                        <input type="hidden" name="membership_id"  value="{{ $main_group_session->id }}">
                                                        <input type="hidden" name="member_id" value="{{ $main_group_session->member->id }}">
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
                                                        {!! Form::close() !!}
                                                    </div>
                                                @empty
                                                    <div class="col-md-12">

                                                        <h5 class="text-danger font-weight-bold">
                                                            {{ trans('global.no_sessions_available') }}
                                                        </h5>
                                                        <a href="{{ \URL::previous() }}" class="btn btn-danger">
                                                            <i class="fa fa-arrow-circle-left"></i>
                                                            {{ trans('global.back') }}
                                                        </a>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function letMeIn() {
            $.ajax({
                url: "http://192.168.1.110:8080",
                type: 'GET',
                dataType: 'json', // added data type
                success: function(res) {
                    console.log(res);
                    alert(res);
                }
            });
        }
    </script>
@endsection
