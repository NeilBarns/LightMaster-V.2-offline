@php
use App\Enums\PermissionsEnum;
@endphp

<div class="h-full w-full flex flex-col bg-color-default !bg-[#FFECAE]">
    <div class="grow-0 h-16">
        <div class="flex h-full items-center px-4">
            {{-- <img class="h-10 mr-3" src="{{ asset('imgs/isabela-state-university-logo.png') }}" alt="logo"> --}}
            <img class="h-[25px]" src="{{ asset('imgs/LightMaster.png') }}" alt="logo">

            <button id="sidebar-hide-toggle" class="absolute left-64 text-white p-1 rounded-full shadow-md hover:bg-gray-400 hover:shadow-lg transition-all duration-300">
                <img src="{{ asset('imgs/arrow-left-s-line.png') }}" alt="Bell icon" 
                            class="w-6 h-6 !block">
            </button>
        </div>
    </div>
    <div class="flex-1 left-nav w-full">
        <div class="uk-width-1-2@s uk-width-2-5@m !w-full">
            <ul class="uk-nav-default" uk-nav>
                {{-- @can(PermissionsEnum::VIEW_DASHBOARD)
                <li id="dashboard-menu-item" style="display: none"
                    class="{{ Request::is('dashboard') ? 'active' : '' }}">
                    <a href="/dashboard">
                        <img src="{{ asset('imgs/dashboard.png') }}" alt="Dashboard">
                        Dashboard
                    </a>
                </li>
                @endcan --}}
                @can([PermissionsEnum::ALL_ACCESS_TO_DEVICE, PermissionsEnum::CAN_VIEW_DEVICES])
                <li id="vehicle-management-menu-item-parent" style="display: block"
                    class="{{ Request::is('device*') ? 'active' : '' }}">
                    <a href="/device">
                        <img src="{{ asset('imgs/microchip.png') }}" alt="Students">
                        Device Management
                    </a>
                </li>
                @endcan
                @can([PermissionsEnum::ALL_ACCESS_TO_REPORTS, PermissionsEnum::CAN_VIEW_ACTIVITY_LOGS_REPORTS,
                PermissionsEnum::CAN_VIEW_FINANCIAL_REPORTS])
                <li id="reports-menu-item-parent" style="display: block"
                    class="{{ Request::is('reports') ? 'active' : '' }}">
                    <a href="#">
                        <img src="{{ asset('imgs/reports.png') }}" alt="Reports">
                        Reports
                    </a>
                    <ul class="uk-nav-sub">
                        @can([PermissionsEnum::ALL_ACCESS_TO_REPORTS, PermissionsEnum::CAN_VIEW_ACTIVITY_LOGS_REPORTS])
                        <li class="{{ Request::is('*activity*') ? 'active' : '' }}">
                            <a href="/activity-logs">Activity Logs</a>
                        </li>
                        @endcan
                        @can([PermissionsEnum::ALL_ACCESS_TO_REPORTS, PermissionsEnum::CAN_VIEW_FINANCIAL_REPORTS])
                        <li class="{{ Request::is('*finance*') ? 'active' : '' }}">
                            <a href="/reports/finance">Action Reports</a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan
                @can([PermissionsEnum::ALL_ACCESS_TO_USERS])
                <li id="settings-menu-item-parent" style="display: block" class="">
                    <a href="#">
                        <img src="{{ asset('imgs/setting.png') }}" alt="Settings">
                        User Management
                    </a>
                    <ul class="uk-nav-sub">
                        {{-- <li class="">
                            <a href="">System Configuration</a>
                        </li> --}}
                        <li class="{{ Request::is('*user*') ? 'active' : '' }}">
                            <a href="/manage-users">Manage Users</a>
                        </li>
                        <li class="{{ Request::is('*role*') ? 'active' : '' }}">
                            <a href="/manage-roles">Manage Roles</a>
                        </li>
                    </ul>
                </li>
                @endcan
            </ul>
        </div>
    </div>
</div>