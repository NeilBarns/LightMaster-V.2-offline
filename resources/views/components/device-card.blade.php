@php
use App\Enums\DeviceStatusEnum;
use App\Enums\PermissionsEnum;
use App\Models\DeviceTimeTransactionsResponse;
use App\Events\DeviceTransactionUpdates;


$statusClass = '';
$isDisabled = false;
$isInactive = false;
$isPending = false;
$isRunning = false;
$isPaused = false;
$isFree = false;

switch ($device->deviceStatus->Status) {
case DeviceStatusEnum::PENDING:
$statusClass = 'grey';
$isDisabled = true;
$isPending = true;
break;
case DeviceStatusEnum::RUNNING:
$statusClass = 'green';
$isRunning = true;
break;
case DeviceStatusEnum::INACTIVE:
$statusClass = 'yellow';
$isInactive = true;
break;
case DeviceStatusEnum::DISABLED:
$statusClass = 'red';
$isDisabled = true;
break;
case DeviceStatusEnum::PAUSE:
$statusClass = 'lightgray';
$isPaused = true;
break;
case DeviceStatusEnum::STARTFREE:
$statusClass = 'orange';
$isFree = true;
break;
case DeviceStatusEnum::PENDINGDELETE:
$statusClass = 'black';
$isDisabled = true;
break;
case DeviceStatusEnum::DELETED:
$statusClass = 'black';
$isDisabled = true;
case DeviceStatusEnum::PENDINGEXCHANGE:
$statusClass = 'teal';
$isDisabled = true;
break;
}
$increments = $device->increments;
@endphp

<div id="device-card-{{ $device->DeviceID }}" class="device-card ui card !h-[350px] !w-[330px] !mr-6 "
    data-device-id="{{ $device->DeviceID }}" data-remaining-time-notification="{{ $device->RemainingTimeNotification }}">
    <a id="notification-banner-{{ $device->DeviceID }}"
        class="ui yellow inverted tag label notification-banner">Extended
        time</a>
    <div class="content !max-h-72">
        <div class="header mb-2">
            <span class="text-base">{{ $device->DeviceName }}</span>
            @can([PermissionsEnum::CAN_VIEW_DEVICE_DETAILS, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
            <div class="w-7 h-7 float-right">
                <form action="{{ route('device.detail', $device->DeviceID) }}" method="get" class="inline">
                    @csrf
                    <button title="View device details" type="submit" class="w-9 h-9 p-1 bg-transparent border-none">
                        <img src="{{ asset('imgs/view.png') }}" alt="edit" class="w-full h-full object-contain">
                    </button>
                </form>
            </div>
            <div class="iconDeviceLoading w-7 h-7 float-right mr-2 !hidden"
                 data-id="{{ $device->DeviceID }}" title="Fetching device status...">
                <img src="{{ asset('imgs/loading-small.gif') }}" alt="edit" class="w-full h-full object-contain">
            </div>
            <div class="iconDevicePendingNetwork networkControls w-7 h-7 float-right mr-2 !block cursor-pointer"
                 data-id="{{ $device->DeviceID }}" title="Fetching device status...">
                <img src="{{ asset('imgs/pending-network.png') }}" alt="edit" class="w-full h-full object-contain">
            </div>
            <div class="iconDeviceOnline networkControls w-7 h-7 float-right mr-2 !hidden cursor-pointer"
                 data-id="{{ $device->DeviceID }}" title="Online">
                <img src="{{ asset('imgs/online.gif') }}" alt="edit" class="w-full h-full object-contain">
            </div>
            <div class="iconDeviceOffline networkControls w-7 h-7 float-right mr-2 !hidden cursor-pointer" 
                 data-id="{{ $device->DeviceID }}" title="Offline">
                <img src="{{ asset('imgs/offline.png') }}" alt="edit" class="w-full h-full object-contain">
            </div>
            @endcan
        </div>
        <a id="device-sync-{{ $device->DeviceID }}"
            class="ui gray tag label !text-black float-right !hidden">
            Syncing...
        </a>

        <a id="device-status" 
            class="deviceStatus ui {{ $statusClass }} ribbon label z-50" 
            data-id="{{ $device->DeviceID }}" 
            data-device-status-id={{$device->deviceStatus->DeviceStatusID}}>
            @if ($device->deviceStatus->Status == DeviceStatusEnum::PAUSE)
            {{-- {{ "Paused: " . convertSecondsToTimeFormat($remainingTime) }} --}}
            Paused
            @else
            {{ $device->deviceStatus->Status }}
            @endif
        </a>

        <div class="ui divider"></div>
        <div class="description">

            <p id="lblStartTime-{{ $device->DeviceID }}">Start time: --:--:--</p>
            <p id="lblEndTime-{{ $device->DeviceID }}">End time: --:--:--</p>
            <p id="lblTotalTime-{{ $device->DeviceID }}">Total time: 0 hr 0 mins
            <p id="lblTotalRate-{{ $device->DeviceID }}">Total charge/rate: PHP 0.00</p></p>
            <p id="lblRemainingTime-{{ $device->DeviceID }}" class="remaining-time" data-id="{{ $device->DeviceID }}">Time spent: 00:00:00</p>
            <p id="lblBareRemainingTime-{{ $device->DeviceID }}" class="bare-remaining-time !hidden" data-id="{{ $device->DeviceID }}">0</p>

        </div>
    </div>
    <div class="extra content">
        <div class="ui two column grid">
            <div class="row">
                <div class="column">
                    @can([PermissionsEnum::CAN_CONTROL_DEVICE_TIME, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                    <button id="btnEndTime"
                        class="btnEndTime ui fluid small button red !hidden disabled"
                        data-id="{{ $device->DeviceID }}">
                        End time
                    </button>
                    <div id="startItems"
                        class="startItems ui small floating dropdown labeled icon button disabled !block"
                        data-id="{{ $device->DeviceID }}">
                        <i class="dropdown icon"></i>
                        Start Time
                        <div class="menu">
                            @foreach ($device->device_times->sortByDesc('TimeTypeID') as $deviceTime)
                                @if ($deviceTime['TimeTypeID'] === 3)
                                    <div data-id="{{ $device->DeviceID }}" 
                                        class="btnOpenTime item" 
                                        data-type="open" 
                                        data-value="{{ $deviceTime['Time'] }}" 
                                        data-rate="{{ $deviceTime['Rate'] }}">
                                            Open time
                                    </div>
                                @elseif ($deviceTime['TimeTypeID'] === 1)
                                    <div class="item" data-type="rated" data-value="{{ $deviceTime['Time'] }}" data-rate="{{ $deviceTime['Rate'] }}">
                                        Start {{ convertMinutesToHoursAndMinutes($deviceTime['Time']) }}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endcan
                </div>
                <div class="column">
                    @can([PermissionsEnum::CAN_CONTROL_DEVICE_TIME, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                    <button class="ui fluid small button pause-time-button disabled !block" data-id="{{ $device->DeviceID }}">
                        Pause time
                    </button>
                    <button class="ui fluid small button green resume-time-button disabled !hidden" data-id="{{ $device->DeviceID }}">
                        Resume time
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        <div class="one two column stackable grid">
            <div class="column">
                @can([PermissionsEnum::CAN_CONTROL_DEVICE_TIME, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                @if ($increments->count() == 1)
                <button data-id="{{ $device->DeviceID }}" class="ui small button extend-time-single-button disabled" 
                    data-time="{{ $increments->first()->Time }}" data-rate="{{ $increments->first()->Rate }}">
                    Extend {{convertMinutesToHoursAndMinutes($increments->first()->Time) }}</button>
                @elseif ($increments->count() > 1)
                <div id="extendItems"
                    class="extend-items-dropdown ui small floating dropdown labeled icon button disabled"
                    data-id="{{ $device->DeviceID }}">
                    <i class="dropdown icon"></i>
                    Extend Time
                    <div class="menu">
                        @foreach ($increments as $increment)
                        <div class="item" data-value="{{ $increment->Time }}" data-rate="{{ $increment->Rate }}">
                            {{ convertMinutesToHoursAndMinutes($increment->Time) }}
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @endcan
            </div>
        </div>
    </div>
</div>
<x-modals.end-time-confirmation-modal :device="$device" />