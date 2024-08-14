@extends('layouts.admin')
@section('content')
    <div class="card shadow-sm">
        <div class="card-header font-weight-bold bg-default text-white">
            <i class="fa fa-calendar"></i> {{ trans('global.calendar') }}
        </div>
        <div class="card-body pb-3">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="datee font-weight-bold"></h4>
                </div>
                <div class="col-md-6">
                    <div class="digital-time text-success">
                        <h4 class="font-weight-bold hours">00</h4>
                        <h4>:</h4>
                        <h4 class="font-weight-bold minutes">00</h4>
                        <h4>:</h4>
                        <h4 class="font-weight-bold seconds">00</h4>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @switch(auth()->user()->roles[0]->title)
        @case('Super Admin')
            @include('dashboard.super_admin')
        @break

        @case('Partner')
            @include('dashboard.super_admin')
        @break

        @case('Admin')
            @include('dashboard.admin')
        @break

        @case('Developer')
            @include('dashboard.admin')
        @break

        @case('Accountant')
            @include('dashboard.accountant')
        @break

        @case('Sales')
            @include('dashboard.sales')
        @break

        @case('Trainer')
            @include('dashboard.trainer')
        @break

        @case('Receptionist')
            @include('dashboard.receptionist')
        @break

        @case('Super Visor')
            @include('dashboard.supervisor')
        @break

        {{-- @case('Fitness Manager')
            @include('dashboard.fitness_manager')
        @break --}}
        @case('Sales manager')
            @include('dashboard.sales_manager')
        @break

        @case('Sales Director')
            @include('dashboard.sales_director')
        @break

        @case('Fitness Manager')
            @include('admin.memberships.assign_coach')
            @include('dashboard.fitnessManager')
        @break

        @default
            @include('dashboard.default')
    @endswitch
@endsection
@section('scripts')
    <script>
        setInterval(setDigitalClock, 1000);

        function setDigitalClock() {
            const today = new Date();
            const timeOptions = {
                minimumIntegerDigits: 2,
                useGrouping: false,
            };
            const dateOptions = {
                weekday: 'long',
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            };

            let getSeconds = today.getSeconds().toLocaleString("en-US", timeOptions);
            let getMinutes = today.getMinutes().toLocaleString("en-US", timeOptions);
            let getHours = today.getHours().toLocaleString("en-US", timeOptions);

            let getDate = today.toLocaleDateString("en-US", dateOptions);

            if (getHours > 12) {
                getHours = (getHours - 12).toLocaleString("en-US", timeOptions);;
            }

            const seconds = document.querySelector(".seconds");
            const minutes = document.querySelector(".minutes");
            const hours = document.querySelector(".hours");
            const date = document.querySelector(".datee");

            setTime(seconds, getSeconds);
            setTime(minutes, getMinutes);
            setTime(hours, getHours);
            setTime(date, getDate);
        }

        function setTime(element, operator) {
            element.innerHTML = operator;
        }
        setDigitalClock();
    </script>
    
@endsection
