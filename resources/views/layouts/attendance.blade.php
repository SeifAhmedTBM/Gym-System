<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> --}}
    <title>{{ trans('panel.site_title') }}</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/@coreui/coreui@3.2/dist/css/coreui.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/login.css') }}" rel="stylesheet" />
    @livewireStyles
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
    @yield('styles')
    <style>
    body {
        font-family: 'Lato', sans-serif !important;
    }
    #particles{
        width: 100% !important;
        height: 100%;
        background-color: #3a3a3a7c;
        background-image: url('');
        background-size: cover;
        background-position: 50% 50%;
        background-repeat: no-repeat;
        position:fixed;
        z-index : 2;
    }

    #bg {
        width: 100% !important;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.657);
        background-image: url('');
        background-size: cover;
        background-position: 50% 50%;
        background-repeat: no-repeat;
        position:fixed;
        z-index : 9;
    }
    </style>
</head>

<body class="header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden login-page">
    @yield('content')
     <!-- Modal -->
    {{-- <div class="modal fade" id="freezeModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
     aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 99999">
    <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ trans('global.freezes') }} <span class="badge badge-info p-2" id="membership"></span></h5>
                </div>
                <form action="{{ route('admin.freeze-attend.take') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="freeze_id" id="freeze_id">
                        <input type="hidden" name="membership_id" id="membership_id">
                        <div class="form-group">
                            <div class="alert alert-info text-center">
                                <h5>
                                    <i class="fa fa-exclamation-circle"></i>
                                    Membership is frozen You want to cancel freeze request?
                                </h5>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                            <label for="start_date">{{ trans('global.start_date') }}</label>
                            <h4 id="start_date"></h4>
                            </div>
                            <div class="col-md-6">
                                <label for="end_date">{{ trans('global.end_date') }}</label>
                                <h4 id="end_date"></h4>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="current_date">{{ trans('global.date') }}</label>
                                <h4 id="current_date">{{ date('Y-m-d') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" data-dismiss="modal">{{ trans('global.cancel') }}</button>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i>
                            Yes, Attend</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

    <!-- Modal -->
    <div class="modal fade" id="has_locker" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 99999">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('global.locker') }}</h5>
                </div>
                <form action="{{ route('attendance.take') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="frozen d-none">
                            <div class="alert alert-danger text-center">
                            <i class="fa fa-exclamation-cirlce"></i>
                                <h5><b>This member in frozen mode !</b> <br>
                                By Clicking on confirm will break freeze.</h5>
                            </div>
                            <div class="form-group row">
                                <input type="hidden" name="member_id" id="member_id" value="{{ $member->id ?? NULL }}">
                                <input type="hidden" name="membership_id" id="membership_id">
                                <input type="hidden" name="freeze_id" id="freeze_id">
                                <div class="col-md-4">
                                    <label for="current_date">Current Date ( Today )</label>
                                    <h4 id="current_date">{{ date('Y-m-d') }}</h4>
                                </div>
                                <div class="col-md-4">
                                    <label for="start_date">{{ 'Freeze '. trans('global.start_date') }}</label>
                                    <h4 id="start_date"></h4>
                                </div>
                                <div class="col-md-4">
                                        <label for="end_date">{{ 'Freeze '.trans('global.end_date') }}</label>
                                        <h4 id="end_date"></h4>
                                </div>
                            </div>
                        </div>
        
                        <div class="locker d-none">
                            <input type="hidden" name="card_number" id="card_number"/>
                            <label for="locker">{{ trans('global.locker') }}</label>
                            <input type="text" class="form-control" name="locker" id="locker" placeholder="Ex: 1,2,3" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {{ trans('global.confirm') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="free_session"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 99999">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('cruds.pricelist.fields.free_sessions') }}</h5>
                </div>
                <form action="{{ route('admin.take.freeSession') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="membership_id" value="{{ $main_membership->id ?? '' }}">
                        <div class="form-group alert alert-info">
                            <h4 class="text-center">
                                Do you want to take a free session?
                            </h4>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">{{ trans('global.notes') }}</label>
                            <textarea name="notes" id="notes" cols="30" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {{ trans('global.yes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('attendance.invitation_form')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>
    <script src="{{ asset('js/particles.js') }}"></script>
    <script>
        particlesJS.load('particles', 'particles.json', function() {
            console.log('callback - particles.js config loaded');
        });

        $("form").on("submit", function () {
            $(this).find(":submit").prop("disabled", true);
        });
    </script>
    @livewireScripts
    @yield('scripts')
    <script>
        function selectLead(divElement, leadName) {
            let lead_id = $(divElement).data('id');
            $("#lead_id").val(lead_id);
            $("#search_lead").val(leadName);
            $(".leadsDiv").each(function() {
                $(this).remove();
            })
        }
    </script>
</body>

</html>