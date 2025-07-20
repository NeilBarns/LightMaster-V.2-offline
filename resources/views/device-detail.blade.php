@php
use App\Enums\PermissionsEnum;
@endphp

@extends('components.layout')

@section('page-title')
@parent
<div class="flex justify-center align-middle">
    <button class="ui icon button" onclick="window.location='{{ route('devicemanagement') }}'">
        <i class="arrow left icon"></i>
    </button>
    <span class="self-center ml-2">Device Management > Device Details</span>
</div>
@endsection

@section('content')
<div class="flex flex-col h-full px-5 py-7 overflow-y-auto overflow-x-hidden">
    <div class="ui stackable equal width grid">
        <div class="row">
            <div class="column">
                <div class="ui icon message">
                    <img src="{{ asset('imgs/bulb.png') }}" alt="icon" class="ui image w-14 h-14 mr-4">
                    <div class="content">
                        <div class="header">
                            LightMaster Controller
                        </div>
                        <p>Manages all connected components. Configure the device settings and preferences here.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="column !pt-0 !pr-0 !pb-0">
                <div class="flex flex-col justify-center h-full w-full">
                    <div class="flex items-center">
                        <h2 id="deviceNameDisplay" data-id="{{ $device->DeviceID }}"
                            class="lblDeviceName ui header" >
                            {{ $device->DeviceName }}
                        </h2>
                        @can([PermissionsEnum::CAN_EDIT_DEVICE_NAME, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                        <button id="editDeviceNameButton" data-id="{{ $device->DeviceID }}"
                            class="btnSaveName ml-2">
                            <i class="pencil icon"></i>
                        </button>
                        @endcan
                    </div>
                    <!-- Hidden input and save button -->
                    <div id="editDeviceNameSection" data-id="{{ $device->DeviceID }}"
                         class="divDeviceName ui input items-center mt-2 w-1/2 !hidden" >
                        <input type="text" id="deviceNameInput" data-id="{{ $device->DeviceID }}"
                            class="txtDeviceName ui small input !mr-2" 
                            value="{{ $device->DeviceName }}">
                        <button id="saveDeviceNameButton" data-id="{{ $device->DeviceID }}"
                            class="btnSaveNameHdn ui small blue button ml-2">
                            Save
                        </button>
                    </div>

                    <span class="text-sm text-gray-500 mt-2 ml-1">{{ $device->SerialNumber }}</span>
                    <span class="text-sm text-gray-500 mt-1 ml-1">{{ $device->deviceStatus->Status }}</span>
                </div>
            </div>
            <div class="column">
                <div class="flex flex-col justify-center h-full w-full">
                    <span class="text-sm text-gray-500">Added date: {{ $device->created_at->format('m/d/Y') }}</span>
                    <span class="text-sm text-gray-500 mt-1">Operation date: {{ $device->OperationDate ?
                        $device->OperationDate->format('m/d/Y') : 'N/A' }}</span>
                    <span class="text-sm text-gray-500 mt-1">IP Address: {{ $device->IPAddress ? $device->IPAddress :
                        'N/A' }}</span>
                </div>
            </div>
            <div class="column">
                @if($device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PENDING)
                @can([PermissionsEnum::CAN_DEPLOY_DEVICE, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                <form action="{{ route('device.deploy', $device->DeviceID) }}" method="POST" class="!float-right">
                    @csrf
                    <button type="submit" id="btnDeploy"
                        class="btnDeployDevice ui green small compact labeled icon button float-right !mt-2" 
                        {{
                            !$device->IsOnline ? 'disabled' : ''
                        }} 
                        data-id="{{ $device->DeviceID }}" 
                        @if(empty($baseTime) || $deviceTimes->isEmpty()) disabled @endif>
                        <i class="rocket icon"></i>
                        Deploy
                    </button>
                </form>
                @endcan
                @else
                @if ($device->deviceStatus->Status == App\Enums\DeviceStatusEnum::DISABLED)
                <div class="tooltip-container !float-right">
                    <form id="enable-form" action="{{ route('device.enable', $device->DeviceID) }}" method="POST">
                        @csrf
                        <button id="btnEnable" 
                            class="btnEnableDevice ui green small compact labeled icon button float-right !mt-2" 
                            data-id="{{ $device->DeviceID }}">
                            <i class="power off icon"></i>
                            Enable
                        </button>
                        @if ($device->deviceStatus->Status == App\Enums\DeviceStatusEnum::RUNNING ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PENDING)
                        <span class="tooltip-text !text-sm">Cannot use this function because the device is either 
                            running, on pause, or offline</span>
                        @endif
                    </form>
                </div>
                @else
                <div class="tooltip-container !float-right">
                    @can([PermissionsEnum::CAN_DISABLE_DEVICE, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                    <form id="disable-form" action="{{ route('device.disable', $device->DeviceID) }}" method="POST">
                        @csrf
                        <button id="btnDisable" 
                            class="btnDisableDevice ui small compact labeled icon button float-right !mt-2" {{
                            ($device->deviceStatus->Status
                            == App\Enums\DeviceStatusEnum::RUNNING ||
                            $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PENDING ||
                            $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PAUSE ||
                            $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::STARTFREE ||
                            $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PENDINGEXCHANGE) ? 'disabled' : ''
                            }} 
                            data-id="{{ $device->DeviceID }}">
                            <i class="power off icon"></i>
                            Disable
                        </button>
                        @if ($device->deviceStatus->Status == App\Enums\DeviceStatusEnum::RUNNING ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PENDING ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PAUSE ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::STARTFREE)
                        <span class="tooltip-text !text-sm">Cannot use this function because the device is either 
                            running, on pause, or offline</span>
                        @endif
                    </form>
                    @endcan
                </div>
                @endif
                @endif

                @can([PermissionsEnum::CAN_DELETE_DEVICE, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                <div class="tooltip-container !float-right">
                    <button id="btnDeleteDevice" data-id="{{ $device->DeviceID }}"
                        class="ui red small compact labeled icon button float-right !mt-2" {{
                        ($device->deviceStatus->Status
                        ==
                        App\Enums\DeviceStatusEnum::RUNNING ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PAUSE ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::STARTFREE ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PENDINGEXCHANGE) ? 'disabled' : '' }}>
                        <i class="trash alternate icon"></i>
                        Delete
                    </button>
                    @if ($device->deviceStatus->Status == App\Enums\DeviceStatusEnum::RUNNING ||
                    $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PAUSE ||
                    $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::STARTFREE)
                    <span class="tooltip-text !text-sm">Cannot use this function because the device is either 
                        running, on pause, or offline</span>
                    @endif
                </div>
                @endcan
                @if ($device->deviceStatus->Status != App\Enums\DeviceStatusEnum::DISABLED)
                @can([PermissionsEnum::CAN_TRIGGER_FREE_LIGHT, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                <div class="tooltip-container !float-right">
                    <button id="btnFreeLight" data-id="{{ $device->DeviceID }}"
                        class="btnStartFreeLight ui green small compact labeled icon button float-right !mt-2
                        {{ $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::STARTFREE ? '!hidden' : '!block' }}"
                        {{ ($device->deviceStatus->Status ==
                        App\Enums\DeviceStatusEnum::RUNNING ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PAUSE ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PENDING ||
                        !$device->IsOnline ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PENDINGEXCHANGE) ? 'disabled' : '' }}
                        data-id="{{ $device->DeviceID }}">
                        <i class="lightbulb icon"></i>
                        Start Free light
                    </button>
                    <button id="btnStopFreeLight" data-id="{{ $device->DeviceID }}"
                        class="btnStopFreeLight ui red small compact labeled icon button float-right !mt-2
                        {{ $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::STARTFREE ? '!block' : '!hidden' }}"
                        {{ ($device->deviceStatus->Status ==
                        App\Enums\DeviceStatusEnum::RUNNING ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PAUSE ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PENDING ||
                        !$device->IsOnline) ? 'disabled' : '' }}
                        data-id="{{ $device->DeviceID }}">
                        <i class="lightbulb icon"></i>
                        Stop Free light
                    </button>
                    @if ($device->deviceStatus->Status == App\Enums\DeviceStatusEnum::RUNNING ||
                    $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PAUSE)
                    <span class="tooltip-text !text-sm">Cannot use this function because the device is either 
                        running, on pause, or offline</span>
                    @endif
                </div>
                @endcan
                @endif

                @if ($device->deviceStatus->Status != App\Enums\DeviceStatusEnum::DISABLED)
                <div class="tooltip-container !float-right">
                    <button id="btnTestLight" 
                        class="btnTestLight ui orange small compact labeled icon button float-right !mt-2" {{
                        ($device->deviceStatus->Status ==
                        App\Enums\DeviceStatusEnum::RUNNING ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PAUSE ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::STARTFREE ||
                        !$device->IsOnline ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PENDINGEXCHANGE) ? 'disabled' : '' }}
                        data-id="{{ $device->DeviceID }}">
                        <i class="quidditch icon"></i>
                        Test light
                    </button>
                    @if ($device->deviceStatus->Status == App\Enums\DeviceStatusEnum::RUNNING ||
                    $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PAUSE||
                    $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::STARTFREE)
                    <span class="tooltip-text !text-sm">Cannot use this function because the device is either 
                        running, on pause, or offline</span>
                    @endif
                </div>
                @endif

                @can([PermissionsEnum::CAN_EXCHANGE_DEVICE, PermissionsEnum::ALL_ACCESS_TO_DEVICE])

                @if ($device->deviceStatus->Status != App\Enums\DeviceStatusEnum::PENDINGEXCHANGE)
                <div class="tooltip-container !float-right">
                    <button id="btnNodeExchange" 
                        class="btnNodeExchange ui small compact labeled icon button float-right !mt-2 teal" {{
                        ($device->deviceStatus->Status ==
                        App\Enums\DeviceStatusEnum::RUNNING ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PAUSE ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::STARTFREE) ? 'disabled' : '' }}
                        data-id="{{ $device->DeviceID }}">
                        <i class="exchange alternate icon"></i>
                        Node exchange
                    </button>
                    @if ($device->deviceStatus->Status == App\Enums\DeviceStatusEnum::RUNNING ||
                    $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PAUSE||
                    $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::STARTFREE)
                    <span class="tooltip-text !text-sm">Cannot use this function because the device is either 
                        running, or on pause.</span>
                    @endif
                </div>
                @endif

                @if ($device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PENDINGEXCHANGE)
                <div class="tooltip-container !float-right">
                    <button id="btnCancelNodeExchange" 
                        class="btnCancelNodeExchange ui small compact labeled icon button float-right !mt-2 red" {{
                        ($device->deviceStatus->Status ==
                        App\Enums\DeviceStatusEnum::RUNNING ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PAUSE ||
                        $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::STARTFREE) ? 'disabled' : '' }}
                        data-id="{{ $device->DeviceID }}">
                        <i class="exchange alternate icon"></i>
                        Cancel Node exchange
                    </button>
                    @if ($device->deviceStatus->Status == App\Enums\DeviceStatusEnum::RUNNING ||
                    $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PAUSE||
                    $device->deviceStatus->Status == App\Enums\DeviceStatusEnum::STARTFREE)
                    <span class="tooltip-text !text-sm">Cannot use this function because the device is either 
                        running, on pause, or offline</span>
                    @endif
                </div>
                @endif


                @endcan

            </div>
        </div>
        @if($device->deviceStatus->Status == App\Enums\DeviceStatusEnum::PENDING)
        <div class="row">
            <div class="column">
                <div id="pendingConfigurationMessage" class="ui visible orange small message">
                    <i class="exclamation circle icon"></i>
                    Please complete the Base Time Configuration and add at least one Time Increment to validate the
                    device for deployment.
                </div>
            </div>
        </div>
        @endif
        <div class="ui divider"></div>
        <div class="row">
            <div class="column">
                <h5 class="ui header">Base Time Configuration</h5>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <form class="ui form" method="POST">
                    @csrf
                    <div class="ui three column stackable grid">
                        <div class="four wide column">
                            <div class="field">
                                <label>Base start time</label>
                            </div>
                            <div class="ui fluid small right labeled input">
                                <input type="number" name="base_time" id="txt_base_time" 
                                    class="txtBaseTime"
                                    data-id="{{ $device->DeviceID }}" 
                                    value="{{ $baseTime ? $baseTime->Time : '' }}" placeholder="60" required>
                                <div class="ui basic label">
                                    minutes
                                </div>
                            </div>
                        </div>
                        <div class="four wide column">
                            <div class="field">
                                <label>Base start rate</label>
                            </div>
                            <div class="ui fluid small right labeled input">
                                <input type="number" step="0.01" name="base_rate" id="txt_base_rate"
                                    class="txtBaseRate"
                                    data-id="{{ $device->DeviceID }}" 
                                    value="{{ $baseTime ? $baseTime->Rate : '' }}" placeholder="30.00" required>
                                <div class="ui basic label">
                                    PHP
                                </div>
                            </div>
                        </div>
                        @can([PermissionsEnum::CAN_EDIT_DEVICE_BASE_TIME, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                        <div class="three wide column">

                            <div class="field">
                                <label class="invisible">search</label>
                            </div>
                            <button id="saveBaseTime" type="button" 
                                class="btnSaveBaseTime ui fluid small blue button"
                                data-id="{{ $device->DeviceID }}" >
                                Save
                            </button>
                        </div>
                        @endcan

                    </div>
                    <input type="hidden" name="device_id" value="{{ $device->DeviceID }}">
                </form>
            </div>
        </div>

        <div class="ui divider"></div>
        <div class="row">
            <div class="column">
                <h5 class="ui header">Open Time Configuration</h5>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <form class="ui form" method="POST">
                    @csrf
                    <div class="ui three column stackable grid">
                        <div class="four wide column">
                            <div class="field">
                                <label>Open time increment</label>
                            </div>
                            <div class="ui fluid small right labeled input">
                                <input type="number" name="open_time" id="txt_open_time"
                                    class="txtOpenTime"
                                    data-id="{{ $device->DeviceID }}" 
                                    value="{{ $openTime ? $openTime->Time : '' }}" placeholder="60" required>
                                <div class="ui basic label">
                                    minutes
                                </div>
                            </div>
                        </div>
                        <div class="four wide column">
                            <div class="field">
                                <label>Open time increment rate</label>
                            </div>
                            <div class="ui fluid small right labeled input">
                                <input type="number" step="0.01" name="open_time_rate" id="txt_open_time_rate"
                                    class="txtOpenRate"
                                    data-id="{{ $device->DeviceID }}" 
                                    value="{{ $openTime ? $openTime->Rate : '' }}" placeholder="30.00" required>
                                <div class="ui basic label">
                                    PHP
                                </div>
                            </div>
                        </div>
                        @can([PermissionsEnum::CAN_EDIT_DEVICE_BASE_TIME, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                        <div class="three wide column">

                            <div class="field">
                                <label class="invisible">search</label>
                            </div>
                            <button id="saveOpenTime" type="button" 
                                class="btnSaveOpenTime ui fluid small blue button"
                                data-id="{{ $device->DeviceID }}">
                                Save
                            </button>
                        </div>
                        @endcan

                    </div>
                    <input type="hidden" name="device_id" value="{{ $device->DeviceID }}">
                </form>
            </div>
        </div>

        <div class="ui divider"></div>
        <div class="row">
            <div class="column">
                <h5 class="ui header">Time Increments</h5>
            </div>
        </div>

        <div class="row">
            <div class="column">
                <div class="ui cards">
                    @foreach ($deviceTimes as $deviceTime)
                    <div class="ui card w-1/3 m-2">
                        <a id="increment-notification-banner-{{ $deviceTime->DeviceTimeID }}"
                            class="bannerIncrementStatus ui gray tag label !text-black increment-notification-banner {{ $deviceTime->Active ? '!hidden' : '!block' }}"
                            data-id="{{ $deviceTime->DeviceTimeID }}">
                            Disabled
                        </a>
                        <div class="content flex justify-between items-center">
                            <div class="flex flex-col">
                                <span class="font-semibold">{{ convertMinutesToHoursAndMinutes($deviceTime->Time)
                                    }}</span>
                                <span class="text-gray-500">PHP {{ $deviceTime->Rate }}</span>
                            </div>
                            <div class="flex items-center flex-grow justify-end">
                                @can([PermissionsEnum::CAN_EDIT_DEVICE_INCREMENTS,
                                PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                                <button class="ui icon button !bg-transparent !border-none p-2 edit-increment-button"
                                    data-id="{{ $deviceTime->DeviceTimeID }}" data-time="{{ $deviceTime->Time }}"
                                    data-rate="{{ $deviceTime->Rate }}" title="Edit">
                                    <img src="{{ asset('imgs/edit.png') }}" alt="edit" class="w-5 h-5">
                                </button>
                                @endcan
                                @can([PermissionsEnum::CAN_DISABLE_DEVICE_INCREMENTS,
                                PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                                <button id="btnDisableIncrement"
                                    class="btnUpdateIncrementStatus ui icon button !bg-transparent !border-none p-2"
                                    data-id="{{ $deviceTime->DeviceTimeID }}"
                                    data-device-id="{{ $deviceTime->DeviceID }}"
                                    data-status="{{ $deviceTime->Active ? '1' : '0' }}"
                                    title="{{ $deviceTime->Active ? 'Disable' : 'Enable' }}">
                                    <img 
                                        src="{{ $deviceTime->Active ? asset('imgs/disable.png') : asset('imgs/enable.png') }}" 
                                        data-enable-url="{{ asset('imgs/enable.png') }}" 
                                        data-disable-url="{{ asset('imgs/disable.png') }}" 
                                        data-id="{{ $deviceTime->DeviceTimeID }}" 
                                        alt="{{ $deviceTime->Active ? 'disable' : 'enable' }}" 
                                        class="w-5 h-5">
                                </button>
                                @endcan
                                @can([PermissionsEnum::CAN_DISABLE_DEVICE_INCREMENTS,
                                PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                                <button class="ui icon button !bg-transparent !border-none p-2 delete-increment-button"
                                    data-id="{{ $deviceTime->DeviceTimeID }}" title="Delete">
                                    <img src="{{ asset('imgs/delete.png') }}" alt="delete" class="w-5 h-5">
                                </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @can([PermissionsEnum::CAN_ADD_DEVICE_INCREMENTS,
                    PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                    <div class="ui card w-1/3 !h-24 m-2 !bg-teal-100">
                        <button id="addIncrementButton" class="ui small w-full h-full">
                            <div class="flex justify-center align-middle">
                                <img src="{{ asset('imgs/plus.png') }}" alt="add" class="w-10 h-10 mr-3">
                                <span class="self-center font-bold">Add Increment</span>
                            </div>
                        </button>
                    </div>
                    @endcan

                </div>
            </div>
        </div>

        {{-- @can([PermissionsEnum::CAN_EDIT_WATCHDOG_INTERVAL, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
        <div class="ui divider"></div>
        <div class="row">
            <div class="column">
                <h5 class="ui header">Watchdog Configuration</h5>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <div class="ui icon message warning">
                    <div class="content">
                        <div class="header">
                            What is <i>Watchdog</i>?
                        </div>
                        <p>This process involves the device performing self-maintenance and periodic checks at regular
                            time intervals. It ensures the device's proper functioning by continuously monitoring its
                            status and making necessary adjustments.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <form class="ui form" method="POST">
                    @csrf
                    <div class="ui three column stackable grid">
                        <div class="four wide column">
                            <div class="field">
                                <label>Watchdog interval</label>
                            </div>
                            <div class="ui fluid small right labeled input">
                                <input type="number" name="txt_watchdogInterval" id="txt_watchdogInterval"
                                    value="{{ $device->WatchdogInterval ? $device->WatchdogInterval : 0 }}"
                                    placeholder="60" required>
                                <div class="ui basic label">
                                    minutes
                                </div>
                            </div>
                        </div>

                        <div class="three wide column">

                            <div class="field">
                                <label class="invisible">search</label>
                            </div>
                            <button id="saveWatchdogInterval" type="button"
                                class="ui fluid small blue button">Save</button>
                        </div>


                    </div>
                    <input type="hidden" name="device_id" value="{{ $device->DeviceID }}">
                </form>
            </div>
        </div>
        @endcan --}}


        @can([PermissionsEnum::CAN_EDIT_REMAINING_TIME_INTERVAL, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
        <div class="ui divider"></div>
        <div class="row">
            <div class="column">
                <h5 class="ui header">Remaining time reminder notification</h5>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <div class="ui icon message warning">
                    <div class="content">
                        <div class="header">

                        </div>
                        <p>A notification that the customer only has N minutes before the transaction time ends. Set to
                            0 to if notification is not needed.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <form class="ui form" method="POST">
                    @csrf
                    <div class="ui three column stackable grid">
                        <div class="four wide column">
                            <div class="field">
                                <label>Remaining time notification</label>
                            </div>
                            <div class="ui fluid small right labeled input">
                                <input type="number" name="txt_remainingTime" id="txt_remainingTime"
                                    class="txtRemainingTime"
                                    data-id="{{ $device->DeviceID }}"
                                    value="{{ $device->RemainingTimeNotification ? $device->RemainingTimeNotification : 0 }}"
                                    placeholder="60" required>
                                <div class="ui basic label">
                                    minutes
                                </div>
                            </div>
                        </div>
                        <div class="three wide column">

                            <div class="field">
                                <label class="invisible">search</label>
                            </div>
                            <button id="saveRemainingTime" type="button"
                                class="btnSaveRemainingTime ui fluid small blue button"
                                data-id="{{ $device->DeviceID }}">
                                Save
                            </button>
                        </div>


                    </div>
                    <input type="hidden" name="device_id" value="{{ $device->DeviceID }}">
                </form>
            </div>
        </div>
        @endcan

        <div class="ui divider"></div>
        <div class="row">
            <div class="column">
                <h5 class="ui header">Free light configuration</h5>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <form class="ui form" method="POST">
                    @csrf
                    <div class="ui three column stackable grid">
                        <div class="four wide column">
                            <div class="field">
                                <label>Free light time limit</label>
                            </div>
                            <div class="ui fluid small right labeled input">
                                <input type="number" name="txt_freeTimeLimit" id="txt_freeTimeLimit"
                                    class="txtFreeTimeLimit"
                                    data-id="{{ $device->DeviceID }}"
                                    value="{{ $freeTimeLimit ? $freeTimeLimit->Time : '' }}" placeholder="1" required>
                                <div class="ui basic label">
                                    minutes
                                </div>
                            </div>
                        </div>
                        @can([PermissionsEnum::CAN_EDIT_DEVICE_BASE_TIME, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                        <div class="three wide column">
                            <div class="field">
                                <label class="invisible">search</label>
                            </div>
                            <button id="saveFreeTimeLimit" type="button"
                                class="btnFreeTimeLimit ui fluid small blue button"
                                data-id="{{ $device->DeviceID }}">
                                Save
                            </button>
                        </div>
                        @endcan

                    </div>
                    <input type="hidden" name="device_id" value="{{ $device->DeviceID }}">
                </form>
            </div>
        </div>

        <div class="ui divider"></div>
        <div class="row">
            <div class="column">
                <h5 class="ui header">Emergency feature</h5>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <div class="ui icon message warning">
                    <div class="content">
                        <div class="header">

                        </div>
                        <p>The passkey to be used by the LightNodes to enable emergency manual override.</p>
                    </div>
                </div>
            </div>
        </div>
        @can([PermissionsEnum::CAN_VIEW_EMERGENCY_PASSKEY, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
        <div class="row">
            <div class="column">
                <form class="ui form" method="POST">
                    @csrf
                    <div class="ui three column stackable grid">
                        <div class="four wide column">
                            <div class="field">
                                <label>Node emergency passkey</label>
                            </div>
                            <div class="ui fluid small input">
                                <input type="input" name="txt_emergencyPass" id="txt_emergencyPass"
                                    class="txtEmergencyPass ui small input !mr-2"
                                    data-id="{{ $device->DeviceID }}"
                                    value="{{ $device->EmergencyPasskey ? $device->EmergencyPasskey : '' }}" required>
                            </div>
                        </div>
                        @can([PermissionsEnum::CAN_EDIT_EMERGENCY_PASSKEY, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                        <div class="three wide column">
                            <div class="field">
                                <label class="invisible">search</label>
                            </div>
                            <button id="saveEmergencyPass" type="button"
                                class="btnEmergencyPass ui fluid small blue button"
                                data-id="{{ $device->DeviceID }}">
                                Save
                            </button>
                        </div>
                        @endcan

                    </div>
                    <input type="hidden" name="device_id" value="{{ $device->DeviceID }}">
                </form>
            </div>
        </div>
        @endcan
        @can([PermissionsEnum::CAN_VIEW_DEVICE_SPECIFIC_RATE_USAGE_REPORT,
        PermissionsEnum::ALL_ACCESS_TO_DEVICE])
        <div class="ui divider"></div>
        <div class="row">
            <div class="column">
                <h5 class="ui header">Rate and Usage Reports</h5>
            </div>
        </div>

        <div class="row">
            <div class="column">
                <div class="flex space-x-1">
                    <button id="monthlyButton"
                        class="btnMonthlyDeviceLevel px-4 py-2 bg-blue-500 text-white rounded-l text-sm hover:bg-blue-600 focus:outline-none"
                        data-id="{{ $device->DeviceID }}">
                        Monthly
                    </button>
                    <button id="dailyButton"
                        class="btnDailyDeviceLevel px-4 py-2 bg-gray-200 text-gray-800 rounded-r text-sm hover:bg-gray-300 focus:outline-none"
                        data-id="{{ $device->DeviceID }}">
                        Daily
                    </button>
                </div>

                <div class="text-center">
                    <h3 id="chartTitle">Monthly Rate and Usage</h3>
                    <p id="chartSubtitle" class="text-muted text-gray-400">For the current year of {{ date('Y') }}</p>
                    <canvas id="canvasDeviceLevelRateAndUsage" 
                    data-id="{{ $device->DeviceID }}" width="400" height="160"></canvas>
                </div>
            </div>

        </div>
        @endcan

        @can([PermissionsEnum::CAN_VIEW_DEVICE_SPECIFIC_TIME_TRANSACTION_REPORT,
        PermissionsEnum::ALL_ACCESS_TO_DEVICE])
        <div class="ui divider"></div>
        <div class="row">
            <div class="column">
                <h5 class="ui header">Time Transactions for {{ \Carbon\Carbon::today()->subDays(2)->format('F j, Y') }}
                    to {{ \Carbon\Carbon::today()->addDays(0)->format('F j, Y') }}
                </h5>
            </div>
        </div>

        <div class="row">
            <div class="column">
                <div class="flex space-x-1">
                    <button id="overviewButton"
                        class="btnDeviceLevelOverview px-4 py-2 bg-blue-500 text-white rounded-l text-sm hover:bg-blue-600 focus:outline-none"
                        data-id="{{ $device->DeviceID }}">
                            Overview
                    </button>
                    <button id="detailedButton"
                        class="btnDeviceLevelDetailed px-4 py-2 bg-gray-200 text-gray-800 rounded-r text-sm hover:bg-gray-300 focus:outline-none"
                        data-id="{{ $device->DeviceID }}">
                            Detailed
                    </button>
                </div>
            </div>
        </div>

        <div class="divDeviceLevelOverview row !block"
             data-id="{{ $device->DeviceID }}">
             <div class="column">
                <div id="grdDeviceLevelOverview" 
                     class="grdDeviceLevelOverview ag-theme-alpine" style="height: 500px; width: 100%; display: block;"
                     data-id="{{ $device->DeviceID }}"></div>
             </div>
        </div>

        <div class="divDeviceLevelDetailed row !hidden"
             data-id="{{ $device->DeviceID }}">
             <div class="column">
                <div id="grdDeviceLevelDetailed" 
                     class="grdDeviceLevelDetailed ag-theme-alpine" style="height: 500px; width: 100%; display: block;"
                     data-id="{{ $device->DeviceID }}"></div>
             </div>
        </div>
        @endcan

    </div>
</div>

<div id="sessionDetailsModal" class="ui modal">
    <div class="header">Time Transaction Details</div>
    <div class="content">
        <div id="grdSessionDetails" 
             class="grdSessionDetails ag-theme-alpine" 
             style="height: 300px; width: 100%; overflow-y: auto;"></div>
    </div>
    <div class="actions">
        <div class="ui button" onclick="hideSessionDetailsModal()">Close</div>
    </div>
</div>



<div id="reasonModal" class="ui modal">
    <div class="header">Reason</div>
    <div class="content">
        <p id="reasonContent"></p>
    </div>
    <div class="actions">
        <button class="ui button" onclick="$('#reasonModal').modal('hide')">Close</button>
    </div>
</div>

<x-modals.node-exchange-modal :device="$device" />
<x-modals.add-increment-modal :device="$device" />
<x-modals.delete-increment-confirmation-modal />
<x-modals.delete-device-confirmation-modal />
<x-modals.free-light-reason-modal :device="$device" />

@endsection