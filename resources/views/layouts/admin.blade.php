<!DOCTYPE html>
<html dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\Setting::first()->name ?? trans('panel.site_title') }}
        {{ isset(explode('.',request()->route()->getName())[1])? ' - ' .ucfirst(explode('.',request()->route()->getName())[1]): '' }}
    </title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/select/1.3.0/css/select.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"
        rel="stylesheet" />
    {{-- <link href="https://unpkg.com/@coreui/coreui@3.2/dist/css/coreui.min.css" rel="stylesheet" /> --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/dark.css') }}"> --}}
    <link href="{{ asset('css/coreui.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/dark.css') }}" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.5.0/css/perfect-scrollbar.min.css"
        rel="stylesheet" />
    <link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

    @livewireStyles
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap" rel="stylesheet">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    @yield('styles')
    <style>
        *:not(i) {
            font-family: 'Poppins', sans-serif;
        }

        .select2-container--default .select2-selection--single {
            height: 35px !important;
            padding-top: 3px !important;
            border: 1px solid #e8e9ec;
        }

        .datatable thead th {
            position: sticky;
            top: 0;
        }

        select[readonly] {
            pointer-events: none;
        }

        input.form-control {
            color: white !important;
        }
    </style>
</head>

<style>
    .c-sidebar .c-sidebar-nav-dropdown-toggle:hover {
        background-color: {{ \App\Models\Setting::first()->color ?? '#9e3230' }} !important;
    }

    .c-sidebar .c-sidebar-nav-link:hover {
        background-color: {{ \App\Models\Setting::first()->color ?? '#9e3230' }} !important;
    }

    .c-sidebar .c-sidebar-nav-link.c-active {
        background-color: {{ \App\Models\Setting::first()->color ?? '#9e3230' }} !important;
    }

    .c-sidebar-nav-dropdown-items {
        background-color: #b3b3b342 !important;
    }

    .dataTables_scrollBody {
        min-height: 60vh !important;
    }

    * {
        /* font-size: 13px!important; */
        /* font-weight: bold; */
    }

    .select2-search {
        background-color: #2f303a;
    }

    .select2-search input {
        background-color: #2f303a;
    }

    .select2-results {
        background-color: #2f303a;
    }

    .select2-search input {
        background-color: #181924;
        color: white;
    }

    .select2-container--default .select2-selection--single {
        background-color: #2f303a;
        border: 1px solid #aaa;
        border-radius: 4px;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #2f303a;
    }

    .select2-search input {
        background-color: #2e2f38;
        color: white;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #d4d4d4;
        line-height: 28px;
    }

    .c-header {
        position: relative;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-direction: row;
        flex-direction: row;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        -ms-flex-negative: 0;
        flex-shrink: 0;
        min-height: 56px;
        background: #131313;
        border-bottom: 0px solid #2f2f2f;
    }

    .dropzone {
        min-height: 150px;
        border: 2px solid rgb(255 255 255 / 30%);
        background: #24252f;
        padding: 20px 20px;
    }

    .dropzone .dz-preview.dz-image-preview {
        background: #24252f;
    }

    .c-header .c-header-toggler {
        color: rgb(255 255 255 / 50%);
        border-color: rgb(255 255 255 / 10%);
    }

    .c-header .c-header-nav .c-header-nav-btn,
    .c-header .c-header-nav .c-header-nav-link {
        color: rgb(255 255 255 / 60%);
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_processing,
    .dataTables_wrapper .dataTables_paginate {
        color: #fff;
    }

    .c-sidebar .c-sidebar-brand,
    .c-sidebar .c-sidebar-header {
        background-color: rgb(19 19 19);
    }

    .table.dataTable tbody tr {
        background-color: rgba(0, 0, 21, .05);
    }

    .form-control:disabled,
    .form-control[readonly] {
        background-color: rgba(0, 0, 21, .05) !important;
    }

    .select2-selection--multiple {
        background-color: rgba(0, 0, 21, .05) !important;
    }

    .select2-selection__choice {
        background-color: rgba(42, 42, 60, 0.241) !important;
        color: #fff;
    }

    .c-sidebar-nav-item:hover {
        background-color: #5ab5b9 !important;
        box-shadow: 0 0 20px #5ab5b9;
        border-radius: 50% !important;
        margin: 0 80px !important;
        width: 80px !important;
    }

    .card-header {
        background-color: #5ab5b9 !important;
        text-align: left !important;
        padding: 15px !important;
    }

    .table-info,
    .table-info>td,
    .table-info>th {
        color: #e9e9e9;
    }

    .table-info tbody+tbody,
    .table-info td,
    .table-info th,
    .table-info thead th {
        border-color: #e5e5e5;
    }

    .form-control:disabled,
    .form-control[readonly] {
        background-color: rgb(0 0 0 / 49%) !important;
    }
</style>

<body class="dark-theme">
    {{-- <div id='stars'></div>
<div id='stars2'></div>
<div id='stars3'></div> --}}
    @include('partials.menu')
    <div class="c-wrapper">
        <header class="c-header c-header-fixed px-3" style="height: 110px;">
            <button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar"
                data-class="c-sidebar-show">
                <i class="fas fa-fw fa-bars"></i>
            </button>

            {{-- <a class="c-header-brand d-lg-none" href="#">{{ trans('panel.site_title') }}</a> --}}

            <button class="c-header-toggler mfs-3 d-md-down-none" type="button" responsive="true">
                <i class="fas fa-fw fa-bars"></i>
            </button>


            {{-- <a href="{{ route('admin.home') }}">
              <img src="{{ asset('images/zfitness.png') }}" width="110" height="90" alt="fitness"
                  style="margin-right: 50px;margin-top:10px;">
           </a> --}}

            @livewire('search-global-member')

            @livewire('loading-memberships')

            <ul class="c-header-nav {{ app()->isLocale('ar') ? 'mr-auto' : 'ml-auto' }}">
                @php
                      $auth = Auth()->user()->employee;
                      if ($auth) 
                      {
                        $today_attendance = App\Models\EmployeeAttendance::whereEmployeeId($auth->id)  
                                            ->whereDate('created_at',date('Y-m-d'))
                                            ->first();
                      }
                @endphp
                @if ($auth && $today_attendance && $today_attendance->clock_out == NULL)
                  <div class="c-header-nav-item">
                    <small>Signed in : {{ $today_attendance->clock_in }}</small>
                  </div>
                @endif

                <li class="c-header-nav-item">
                @if ($auth)
                  @if ($today_attendance && $today_attendance->clock_out == NULL)
                    <form action="{{ route('admin.employee-sign-in-out') }}" method="post" class="c-header-nav-link">
                      @csrf
                      @method('PUT')
                        <button class="btn btn-danger btn-sm text-white">
                          <i class="fas fa-door-open"></i> &nbsp; Sign out 
                        </button>
                    </form>
                  @elseif (!$today_attendance)
                    <form action="{{ route('admin.employee-sign-in-out') }}" method="post" class="c-header-nav-link">
                      @csrf
                      @method('PUT')
                        <button class="btn btn-warning btn-sm text-white">
                          <i class="fas fa-door-closed"></i> &nbsp; Sign in
                        </button>
                    </form>
                  @elseif ($today_attendance && $today_attendance->clock_out)
                      <span class="badge badge-info">
                        You worked today {{ $today_attendance->work_time }} Hour/s 
                      </span>
                  @endif
                @endif
              </li>

                <li class="c-header-nav-item dropdown ">
                    <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user"></i>&nbsp; {{ ucwords(Auth()->user()->name) }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href='{{ route('profile.password.edit') }}' class="dropdown-item">
                            <i class="fa fa-refresh"></i>&nbsp; {{ trans('global.change_password') }}
                        </a>

                        <a class=" dropdown-item  " href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                    document.getElementById('logoutform').submit();">
                            <i class="fa fa-sign-out"></i>&nbsp; {{ trans('global.logout') }}
                        </a>
                    </div>
                </li>
                {{-- @if (count(config('panel.available_languages', [])) > 1)
                    <li class="c-header-nav-item dropdown d-md-down-none">
                        <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            {{ strtoupper(app()->getLocale()) }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            @foreach (config('panel.available_languages') as $langLocale => $langName)
                                <a class="dropdown-item" href="{{ url()->current() }}?change_language={{ $langLocale }}">{{ strtoupper($langLocale) }} ({{ $langName }})</a>
                            @endforeach
                        </div>
                    </li>
                @endif --}}



                {{-- <ul class="c-header-nav ml-auto">
                    <li class="c-header-nav-item dropdown notifications-menu">
                        <a href="#" class="c-header-nav-link" data-toggle="dropdown">
                            <i class="far fa-bell"></i>
                            @php($alertsCount = \Auth::user()->userUserAlerts()->where('read', false)->count())
                                @if ($alertsCount > 0)
                                    <span class="badge badge-warning navbar-badge">
                                        {{ $alertsCount }}
                                    </span>
                                @endif
                        </a>
                          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="overflow-y: scroll!important;height:70vh!important">
                            @if (count(
        $alerts = \Auth::user()->userUserAlerts()->withPivot('read')->orderBy('created_at', 'ASC')->get()->reverse(),
    ) > 0)
                                @foreach ($alerts as $alert)
                                <a href="{{ $alert->alert_link ? $alert->alert_link : "#" }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                    <div class="dropdown-item pb-4 d-inline-block text-primary">
                                            @if ($alert->pivot->read === 0) <strong> @endif
                                                {{ $alert->alert_text }}
                                            @if ($alert->pivot->read === 0) </strong> @endif
                                        <h6 class="text-muted pt-2">
                                          <i class="fa fa-clock"></i> {{ $alert->created_at->toFormattedDateString()}}
                                        </h6>
                                    </div>
                                  </a>
                                @endforeach
                            @else
                                <div class="text-center">
                                    {{ trans('global.no_alerts') }}
                                </div>
                            @endif
                        </div>
                    </li>
                    
                </ul> --}}

            </ul>

        </header>

        <div class="c-body">
            <main class="c-main">
                <div class="container-fluid">
                    @if (session('message'))
                        <div class="row mb-2">
                            <div class="col-lg-12">
                                <div class="alert alert-success" role="alert">{{ session('message') }}</div>
                            </div>
                        </div>
                    @endif
                    @if ($errors->count() > 0)
                        <div class="alert alert-danger show flex items-center mb-2" role="alert">
                            <i data-feather="alert-octagon" class="w-6 h-6 mr-2"></i>
                            <ul class="list-unstyled">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </main>
            <form id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </div>
    </div>

    @include('attendance.invitation_form')

    <!-- Modal -->
    <div class="modal fade" id="has_locker" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <span id="MemberName"></span>
                    </h5>
                </div>
                <form action="{{ route('admin.take.attend') }}" method="post">
                    @csrf
                    <div class="modal-body">

                        <div class="frozen d-none">
                            <div class="alert alert-danger text-center">
                                <i class="fa fa-exclamation-cirlce"></i>
                                <h5><b>This member in frozen mode !</b> <br>
                                    By Clicking on confirm will break freeze.</h5>
                            </div>
                            <div class="form-group row">
                                <input type="hidden" name="member_id" id="member_id">
                                <input type="hidden" name="membership_id" id="membership_id">
                                <input type="hidden" name="freeze_id" id="freeze_id">
                                <div class="col-md-4">
                                    <label for="current_date">Current Date ( Today )</label>
                                    <h4 id="current_date">{{ date('Y-m-d') }}</h4>
                                </div>
                                <div class="col-md-4">
                                    <label for="start_date">{{ 'Freeze ' . trans('global.start_date') }}</label>
                                    <h4 id="start_date"></h4>
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date">{{ 'Freeze ' . trans('global.end_date') }}</label>
                                    <h4 id="end_date"></h4>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row d-none" id="expiredModal">
                            <div class="col-12 text-center">
                                <i class="fa fa-exclamation-circle fa-5x text-danger"></i>
                                <h4 class="text-danger">{{ trans('global.do_you_want_to_take_attendance') }}</h4>
                                <input type="hidden" name="member_id">
                                <input type="hidden" name="membership_id">
                                <input type="hidden" name="card_number">
                            </div>
                        </div>

                        <div class="locker d-none">
                            <input type="hidden" name="card_number" id="card_number" />
                            <label for="locker">{{ trans('global.locker') }}</label>
                            <input type="text" class="form-control" name="locker" id="locker"
                                placeholder="Ex: 1,2,3">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" data-dismiss="modal">{{ trans('global.close') }}</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i>
                            {{ trans('global.confirm') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Expired Modal --}}

    {{-- <div class="modal fade" id="expiredModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
                <span>{{ trans('global.expired_membership') }}</span>
            </h5>
          </div>
          <form action="{{ route('admin.take.attend') }}" method="post">
            @csrf
            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-12 text-center">
                      <h4>{{ trans('global.do_you_want_to_take_attendance') }}</h4> 
                      <input type="hidden" name="member_id" >
                      <input type="hidden" name="membership_id">          
                      <input type="hidden" name="card_number">          
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-danger" data-dismiss="modal">{{ trans('global.close') }}</button>
              <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {{ trans('global.confirm') }}</button>
            </div>
          </form>
        </div>
      </div>
    </div> --}}


    {{-- <!-- Default dropup button -->
    <div class="btn-group dropup" style="position: fixed;right:30px;bottom:40px">
      <button type="button" class="btn btn-lg shadow-none btn-pill btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-cog fa-spin fa-lg"></i>
      </button>
      <div class="dropdown-menu">
        @can('settings_access')
          <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
            <i class="fa fa-cog fa-spin"></i> &nbsp; {{ trans('global.settings') }}
          </a>
        @endcan
        @can('member_create')
          <a class="dropdown-item" href="{{ route('admin.members.create') }}">
            <i class="far fa-user"></i> &nbsp; {{ trans('global.add') }} {{ trans('cruds.member.title_singular') }}
          </a>
        @endcan
        @can('lead_create')
          <a class="dropdown-item" href="{{ route('admin.leads.create') }}">
            <i class="far fa-user"></i> &nbsp; {{ trans('global.add') }} {{ trans('cruds.lead.title_singular') }}
          </a>
        @endcan
        @can('membership_create')
          <a class="dropdown-item" href="{{ route('admin.memberships.create') }}">
            <i class="fas fa-fingerprint"></i> &nbsp; {{ trans('global.add') }} {{ trans('cruds.membership.title_singular') }}
          </a>
        @endcan
        @can('expense_create')
          <a class="dropdown-item" href="{{ route('admin.expenses.create') }}">
            <i class="fa fa-money-bill"></i> &nbsp; {{ trans('global.add') }} {{ trans('cruds.expense.title_singular') }}
          </a>
        @endcan
        @can('view_daily_report')
          <a class="dropdown-item" href="{{ route('admin.reports.daily.report') }}">
            <i class="fa fa-file"></i> &nbsp; {{ trans('global.daily_report') }}
          </a>
        @endcan
        @can('due_payments_report')
          <a class="dropdown-item" href="{{ route('admin.reports.due-payments-report') }}">
            <i class="fa fa-file"></i> &nbsp; {{ trans('global.due_payments_report') }}
          </a>
        @endcan
      </div>
    </div> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.5.0/perfect-scrollbar.min.js"></script>
    <script src="https://unpkg.com/@coreui/coreui@3.2.2/dist/js/coreui.bundle.min.js"></script>
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.2.2/js/dataTables.fixedHeader.min.js"></script>
    <script src="//cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.0/js/dataTables.select.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/16.0.0/classic/ckeditor.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/16.0.0/classic/translations/de.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
    <script src="https://unpkg.com/@yaireo/tagify"></script>
    <script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
    @livewireScripts
    <script src="{{ asset('js/main.js') }}"></script>

    <script>
        $(function() {
            let copyButtonTrans = '{{ trans('global.datatables.copy') }}'
            let csvButtonTrans = '{{ trans('global.datatables.csv') }}'
            let excelButtonTrans = '{{ trans('global.datatables.excel') }}'
            let pdfButtonTrans = '{{ trans('global.datatables.pdf') }}'
            let printButtonTrans = '{{ trans('global.datatables.print') }}'
            let colvisButtonTrans = '{{ trans('global.datatables.colvis') }}'
            let selectAllButtonTrans = '{{ trans('global.select_all') }}'
            let selectNoneButtonTrans = '{{ trans('global.deselect_all') }}'

            let languages = {
                'en': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/English.json',
                'ar': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/Arabic.json'
            };

            $.extend(true, $.fn.dataTable.Buttons.defaults.dom.button, {
                className: 'btn'
            })
            $.extend(true, $.fn.dataTable.defaults, {
                language: {
                    url: languages['{{ app()->getLocale() }}']
                },
                columnDefs: [{
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0
                }, {
                    orderable: false,
                    searchable: false,
                    targets: -1
                }],
                select: {
                    style: 'multi+shift',
                    selector: 'td:first-child'
                },
                order: [],
                scrollX: true,
                pageLength: 100,
                dom: 'lBfrtip<"actions">',
                buttons: [{
                        extend: 'selectAll',
                        className: 'btn-primary',
                        text: selectAllButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        },
                        action: function(e, dt) {
                            e.preventDefault()
                            dt.rows().deselect();
                            dt.rows({
                                search: 'applied'
                            }).select();
                        }
                    },
                    {
                        extend: 'selectNone',
                        className: 'btn-primary',
                        text: selectNoneButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'copy',
                        className: 'btn-default',
                        text: copyButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn-default',
                        text: csvButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn-default',
                        text: excelButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'btn-default',
                        text: pdfButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn-default',
                        text: printButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'colvis',
                        className: 'btn-default',
                        text: colvisButtonTrans,
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ]
            });

            $.fn.dataTable.ext.classes.sPageButton = '';
        });
    </script>
    <script>
        $(document).ready(function() {
            $(".notifications-menu").on('click', function() {
                if (!$(this).hasClass('open')) {
                    $('.notifications-menu .label-warning').hide();
                    $.get('/admin/user-alerts/read');
                }
            });
        });
    </script>

    <script>
        $('.zero-configuration').dataTable({
            "pageLength": 25
        });

        $("form").on("submit", function() {
            $(this).find(":submit").prop("disabled", true);
        });

        $('button').on('click', function() {
            $(this).find(':button').prop('disabled', true);
        });
    </script>

    <script>
        function getMember() {
            var member_code = $('#member_code').val();
            var member_branch_id = $('#member_branch_id').val();
            var url = "{{ route('admin.getMember') }}";
            $.ajax({
                method: 'POST',
                url: url,
                _token: $('meta[name="csrf-token"]').attr('content'),
                data: {
                    member_code: member_code,
                    member_branch_id: member_branch_id,
                    _token: _token
                },
                success: function(data) {
                    // console.log(data.member);
                    $('#MemberName').text(data.member.name + ' - ' + data.membership.service_pricelist.name);
                    $("input[name=membership_id]").val(data.membership.id);
                    $("input[name=member_id]").val(data.member.id);
                    $("input[name=card_number]").val(data.member.member_code);

                    // if (data.membership.status == 'expired') 
                    // {
                    //     $('#has_locker').modal('show');
                    //     $('#expiredModal').removeClass('d-none');
                    // }

                    if (data.freeze != null) {
                        $('#has_locker').modal('show');
                        $('#freeze_id').val(data.freeze.id);
                        $('#start_date').text(data.freeze.start_date);
                        $('#end_date').text(data.freeze.end_date);
                        $('.frozen').removeClass('d-none');
                    }

                    if (
                        "{{ isset(\App\Models\Setting::first()->has_lockers) && \App\Models\Setting::first()->has_lockers == true }}") {
                        $('.locker').removeClass('d-none');
                        $('.locker').attr('required', true);
                    }

                    $('#member_code').removeClass('is-invalid').addClass('is-valid');

                    @if (isset(\App\Models\Setting::first()->has_lockers) && \App\Models\Setting::first()->has_lockers == true)
                        $(document).ready(function() {
                            $('#has_locker').modal('show');
                        });
                    @endif

                    if (data.freeze == null && {{ isset(\App\Models\Setting::first()->has_lockers) }} && data
                        .membership.status != 'expired') {
                        $("#attend_branch_id").val(data.member.branch_id);
                        // $('#memberAttendanceForm').submit();
                    }
                },
                error: function(error) {
                    $('#member_code').removeClass('is-valid').addClass('is-invalid');
                },
            })
        }
    </script>


    @include('sweetalert::alert', ['cdn' => 'https://cdn.jsdelivr.net/npm/sweetalert2@9'])

    @yield('scripts')

    <script>
        if (document.body.classList.contains('dark-theme')) {
            var element = document.getElementById('btn-dark-theme');
            if (typeof(element) != 'undefined' && element != null) {
                document.getElementById('btn-dark-theme').checked = true;
            }
        } else {
            var element = document.getElementById('btn-light-theme');
            if (typeof(element) != 'undefined' && element != null) {
                document.getElementById('btn-light-theme').checked = true;
            }
        }

        function handleThemeChange(src) {
            var event = document.createEvent('Event');
            event.initEvent('themeChange', true, true);

            if (src.value === 'light') {
                document.body.classList.remove('dark-theme');
            }
            if (src.value === 'dark') {
                document.body.classList.add('dark-theme');
            }
            document.body.dispatchEvent(event);
        }
    </script>
</body>

</html>
