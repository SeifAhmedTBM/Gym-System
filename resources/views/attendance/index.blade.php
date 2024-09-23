@extends('layouts.attendance')
@section('styles')
    @if (App\Models\Setting::first() && App\Models\Setting::first()->login_background != null)
        <style>
            body {
                background: url("{{ asset('images/' . App\Models\Setting::first()->login_background) }}") center center/cover no-repeat fixed;
                height: 100vh;
            }

        </style>
    @else
        <style>
            body {
                background: url("{{ asset('images/s1.jpg') }}") center center/cover no-repeat fixed;
                height: 100vh;
            }

        </style>
    @endif
@endsection
@section('content')
    <div id="particles"></div>
    <div id="bg"></div>
    <div class="row mx-0" style="z-index: 99999;position: relative;">
        @if ($img = App\Models\Setting::first())
            <div class="col-md-12 mt-5 pt-5 text-center" style="z-index: 99999;position: relative;">
                <a href="{{ route('admin.home') }}">
                    <img width="300" src="{{ asset('images/' . $img->login_logo) }}" class="mb-5"
                        alt="Login Logo">
                </a>
            </div>
        @endif
        <div class="col-md-6 mx-auto">
            <div class="card bg-transparent text-white border-0">
                <div class="card-header text-center font-weight-bold border-0 shadow-none">
                    <h3><i class="fas fa-fingerprint"></i> {{ trans('global.login') }}</h3>
                </div>
                <div class="card-body">

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

                    @if (Session::has('wrong_time'))
                        <div class="alert alert-danger font-weight-bold text-center">
                            {{ session('wrong_time') }}
                        </div>
                    @endif

                    {!! Form::open(['method' => 'GET', 'action' => 'Admin\AttendanceController@getMembershipDetails']) !!}
                    {!! Form::label('membership_id', trans('cruds.lead.fields.member_code')) !!}

                    <div class="input-group">
                        <div class="input-group-prepend ">
                            <select name="member_branch_id" id="member_branch_id" class="form-control form-control-lg shadow-none border-0" style="color: black !important;">
                                @foreach (\App\Models\Branch::pluck('member_prefix','id') as $id => $entry)
                                    <option value="{{ $id }}" {{ Auth()->user()->employee &&  Auth()->user()->employee->branch_id == $id ? 'selected' : '' }}>{{ $entry }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="text" name="membership_id" id="membership_id"
                            class="form-control form-control-lg shadow-none" placeholder="Enter Member code / Card Number"
                            autofocus oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>

                        {{-- {!! Form::number('membership_id', null, ['class' => 'form-control form-control-lg shadow-none', 'placeholder' => 'Enter Member code / Card Number', 'id' => 'membership_id','autofocus'=>'true']) !!} --}}

                        <div class="input-group-append">
                            <button class="btn btn-warning text-white" onclick="checkMasterCards()"
                                @if (App\Models\Setting::first() && App\Models\Setting::first()->color != null) style="background:{{ App\Models\Setting::first()->color }};border:{{ App\Models\Setting::first()->color }}" @endif
                                type="submit">
                                {{ trans('cruds.membershipAttendance.fields.sign_in') }}
                            </button>
                        </div>
                    </div>

                    <small class="text-danger font-weight-bold" id="response-message"></small>
                    {!! Form::close() !!}

                    <div class="row  mt-5">
                    <div class="col-12 ">
                        <a href="{{ route('admin.home') }}" class="btn btn-lg btn-success mx-auto"><i class="fa fa-arrow-left"></i> {{ trans('global.back') }} {{ trans('global.to') }} {{ trans('global.dashboard') }}</a>
                    </div>
                </div>
                </div>


            </div>
        </div>
    </div>
   
@endsection
@section('scripts')
    <script>
        function checkMasterCards() {
            var membership_id = $("#membership_id").val();
            var myarray = '{{ $master_cards }}';
            // var myarray = [
            //     '0008347691',
            //     '0008299982',
            //     '0008315713',
            //     '0008299602',
            //     '0008299603',
            //     '0008299601',
            //     '0008299604',
            //     '0008273480',
            //     '0008273478',
            //     '0008273479',
            //     '0008315712',
            //     '0008315711',
            //     '0008287037',
            //     '0008287038',
            //     '0008316203',
            //     '0008267658',
            //     '0008287033'
            // ];
            if (jQuery.inArray(membership_id, myarray) !== -1) {
                $.ajax({
                    url: "http://192.168.1.110:8080",
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        console.log(res);
                        alert(res);
                    }
                });
            }
        }
    </script>
@endsection
