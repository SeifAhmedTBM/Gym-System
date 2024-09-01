<style>
    .new-icon {
        padding: 16px;
    }

    .d-active {

        background: #5ab5b9 !important;
        border-radius: 50%;
        margin: 0 80px;
        width: 80px;
    }

    .c-sidebar-nav-item:hover {
        background: #b6931c !important border-radius: 50%;
        margin: 0 80px;
    }

    .c-sidebar-nav-item {
        border-radius: 50%;
        margin: 0 80px;
        width: 80px;
    }

    a {
        color: #ffffff !important;
        text-decoration: none !important;
    }
</style>

<div id="sidebar" class="c-sidebar c-sidebar-fixed c-sidebar-lg-show">

    <div class="c-sidebar-brand d-md-down-none">
        <a class="c-sidebar-brand-full h4" href="#">
            @if (isset(App\Models\Setting::first()->menu_logo))
                <img src="{{ asset('images/' . App\Models\Setting::first()->menu_logo) }}" width="150" alt=""
                    style="margin-top: 25px;">
            @endif
        </a>
    </div>
    <br>
    <br>
    <ul class="c-sidebar-nav">
        <li class="c-sidebar-nav-item {{ request()->is('admin') ? 'd-active' : '' }}" style="text-align: center;"
            data-coreui-toggle="tooltip" data-coreui-placement="top" title="Dashboard">
            <a href="{{ route('admin.home') }}">
                <img src="{{ asset('images/dashboard/speedometer.png') }}" width="80" height="80" alt=""
                    class="new-icon">
            </a>

        </li>
        <br>
        <h6 style="color:white;text-align:center;width: 95%;font-weight: bold;">Dashboard</h6>

        <br><br>
        <li class="c-sidebar-nav-item {{ request()->is('admin/hot-keys*') ? 'd-active' : '' }}"
            style="text-align: center;" data-coreui-toggle="tooltip" data-coreui-placement="top" title="Hot keys">
            <a href="{{ route('admin.hot-keys') }}">
                <img src="{{ asset('images/dashboard/23.png') }}" width="80" height="80" alt=""
                    class="new-icon">
            </a>
        </li>
        <br>
        <h6 style="color:white;text-align:center;width: 95%;font-weight: bold;">Hot keys</h6>
        <br><br>
        @can('operations_access')
            <li class="c-sidebar-nav-item {{ request()->is('admin/operations') ? 'd-active' : '' }}"
                style="text-align: center;" data-coreui-toggle="tooltip" data-coreui-placement="top" title="Gym Operations">
                <a href="{{ route('admin.operations') }}">
                    <img src="{{ asset('images/dashboard/gym.png') }}" width="80" height="80" alt=""
                        class="new-icon">
                </a>
            </li>
            <br>
            <h6 style="color:white;text-align:center;width: 95%;font-weight: bold;">Gym Operations</h6>
            <br><br>
        @endcan
        @can('master_data_access')
            <li class="c-sidebar-nav-item {{ request()->is('admin/master') ? 'd-active' : '' }}"
                style="text-align: center;" data-coreui-toggle="tooltip" data-coreui-placement="top" title="Settings">
                <a href="{{ route('admin.master-data') }}">
                    <img src="{{ asset('images/dashboard/24.png') }}" width="80" height="80" alt=""
                        class="new-icon">
                </a>
            </li>
            <br>
            <h6 style="color:white;text-align:center;width: 95%;font-weight: bold;">Settings</h6>
            <br><br>
        @endcan
        @can('hr_management_access')
            <li class="c-sidebar-nav-item {{ request()->is('admin/hr') ? 'd-active' : '' }}" style="text-align: center;">
                <a href="{{ route('admin.hr') }}" data-coreui-toggle="tooltip" data-coreui-placement="top" title="HR">
                    <img src="{{ asset('images/dashboard/hr-logo.png') }}" width="80" height="80" alt=""
                        class="new-icon">
                </a>
            </li>
            <br>
            <h6 style="color:white;text-align:center;width: 95%;font-weight: bold;">HR</h6>
            <br><br>
        @endcan
        @can('reports_access')
            <li class="c-sidebar-nav-item {{ request()->is('admin/reports') ? 'd-active' : '' }}"
                style="text-align: center;">
                <a href="{{ route('admin.reports') }}" data-coreui-toggle="tooltip" data-coreui-placement="top"
                    title="Reports">
                    <img src="{{ asset('images/dashboard/Report.png') }}" width="80" height="80" alt=""
                        class="new-icon">
                </a>
            </li>
            <br>
            <h6 style="color:white;text-align:center;width: 95%;font-weight: bold;">Reports</h6>
            <br><br>
        @endcan
        @canany(['task_access', 'all_tasks_access'])
            <li class="c-sidebar-nav-item {{ request()->is('admin/tasks') ? 'd-active' : '' }}"
                style="text-align: center;">
                <a href="{{ route('admin.task-management') }}" data-coreui-toggle="tooltip" data-coreui-placement="top"
                    title="tasks">
                    <img src="{{ asset('images/dashboard/tasks_icons.png') }}" width="80" height="80" alt=""
                        class="new-icon">
                </a>
            </li>
            <br>
            <h6 style="color:white;text-align:center;width: 95%;font-weight: bold;">Task Management</h6>
            <br>
            <br>
        @endcan
        @can('mobile_app_access')
            <li class="c-sidebar-nav-item {{ request()->is('admin/mobile') ? 'd-active' : '' }}"
                style="text-align: center;">
                <a href="{{ route('admin.mobile') }}"  data-coreui-toggle="tooltip" data-coreui-placement="top"
                title="Mobile App">
                    <img src="{{ asset('images/dashboard/smartphone.png') }}" width="80" height="80" alt=""
                        class="new-icon">
                </a>
            </li>
            <br>
            <h6 style="color:white;text-align:center;width: 95%;font-weight: bold;">Mobile App</h6>
            <br>
            <br>
        @endcan

        @can('zoom_access')
            <li class="c-sidebar-nav-item {{ request()->is('admin/zoom') ? 'd-active' : '' }}"
                style="text-align: center;">
                <a href="{{ route('admin.zoom.index') }}"  data-coreui-toggle="tooltip" data-coreui-placement="top"
                title="Zoom Meeting">
                    <img src="{{ asset('images/dashboard/zoom.png') }}" width="80" height="80" alt=""
                        class="new-icon">
                </a>
            </li>
            <br>
            <h6 style="color:white;text-align:center;width: 95%;font-weight: bold;">Zoom Meeting</h6>
            <br>
            <br>
        @endcan

        {{-- <li class="c-sidebar-nav-item" style="text-align: center;">
            <img src="{{ asset('images/dashboard/22.png') }}" width="80" height="80" alt=""
                class="new-icon">
        </li> --}}

    </ul>

</div>
