@props(['device',
'baseTime',
'openTime',
'increments',
'totalTime',
'totalRate',
'startTime',
'endTime',
'remainingTime',
'usedTime',
'remTimeNotif',
'isOpenTime'])

@php
use App\Enums\DeviceStatusEnum;
use App\Enums\PermissionsEnum;

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
$statusClass = 'black';
$isFree = true;
break;
}
$increments = $device->increments;
@endphp
{{-- !h-[350px] !w-[330px] --}}
<div id="device-card-{{ $device->DeviceID }}" class="ui card !h-[340px] !w-[340px] !mr-6"
    data-device-id="{{ $device->DeviceID }}" data-remaining-time="{{ $remainingTime }}" data-used-time="{{ $usedTime }}"
    data-remainingTimeNotif="{{ $device->RemainingTimeNotification }}">
    <div id="device-sync-{{ $device->DeviceID }}"
        class="syncScreen h-full absolute top-0 w-full z-10 justify-center !hidden">
        <p class=" text-2xl">Syncing...</p>
    </div>
    <a id="notification-banner-{{ $device->DeviceID }}"
        class="ui yellow inverted tag label notification-banner">Extended
        time</a>
    <div class="content !max-h-72">
        <div class="header mb-2">
            <span class="text-sm">{{ $device->ExternalDeviceName }}</span>
            @can([PermissionsEnum::CAN_VIEW_DEVICE_DETAILS, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
            <div class="w-7 h-7 float-right">
                <form action="{{ route('device.detail', $device->DeviceID) }}" method="get" class="inline">
                    @csrf
                    <button title="View device details" type="submit" class="w-9 h-9 p-1 bg-transparent border-none">
                        <img src="{{ asset('imgs/view.png') }}" alt="edit" class="w-full h-full object-contain">
                    </button>
                </form>
            </div>
            @endcan
        </div>

        <a id="device-status-{{ $device->DeviceID }}" class="ui {{ $statusClass }} ribbon label">
            @if ($device->deviceStatus->Status == DeviceStatusEnum::PAUSE)
                {{ "Paused: " . convertSecondsToTimeFormat($usedTime) . ' used' }}
            @else
                {{ $device->deviceStatus->Status }}
            @endif
        </a>

        <div class="ui divider"></div>
        <div class="description">

            @if ($isRunning || $isPaused)
                <p id="lblStartTime-{{ $device->DeviceID }}" class="text-sm">Start time: {{ $startTime ?
                    convertTo12HourFormat(\Carbon\Carbon::parse($startTime)->format('H:i:s')) :'N/A' }}
                </p>
            @else
            <p id="lblStartTime-{{ $device->DeviceID }}" class="text-sm">Start time: --:--:--</p>
            @endif

            @if ($isRunning || $isPaused)
            @if ($isOpenTime)
            <p id="lblEndTime-{{ $device->DeviceID }}" class="font-bold text-sm">End time: ∞</p>
            @else
                <p id="lblEndTime-{{ $device->DeviceID }}" class="text-sm">End time: {{ $endTime ?
                    convertTo12HourFormat(\Carbon\Carbon::parse($endTime)->format('H:i:s'))
                    : 'N/A'}}
                </p>
            @endif
            @else
                <p id="lblEndTime-{{ $device->DeviceID }}" class="text-sm">End time: --:--:--</p>
            @endif

            @if ($isRunning || $isPaused)
            @if ($isOpenTime)
            <p id="lblTotalTime-{{ $device->DeviceID }}" class="font-bold text-sm">Total time: ∞</p>
            @else
                <p id="lblTotalTime-{{ $device->DeviceID }}" class="text-sm">Total time: {{ convertMinutesToHoursAndMinutes($totalTime) }}</p>
            @endif
            @else
                <p id="lblTotalTime-{{ $device->DeviceID }}" class="text-sm">Total time: 0 hr 0 mins</p>
            @endif

            @if ($isRunning || $isPaused)
                <p id="lblTotalRate-{{ $device->DeviceID }}" class="text-sm">Total charge/rate: PHP {{ $totalRate }}</p>
            @else
                <p id="lblTotalRate-{{ $device->DeviceID }}" class="text-sm">Total charge/rate: PHP 0.00</p>
            @endif

            <div class="remaining-time-container" data-device-id="{{ $device->DeviceID }}"
                data-remaining-time="{{ $remainingTime }}"
                data-device-status="{{ strtolower($device->deviceStatus->Status) }}" data-isOpenTime="{{ $isOpenTime }}"
                data-openTime="{{ $openTime ? $openTime->Time : 0}}"
                data-openRate="{{ $openTime ? $openTime->Rate : 0}}">
                <p class="remaining-time text-sm">Remaining time: {{ gmdate('H:i:s', $remainingTime) }}</p>
                <p class="bare-remaining-time !hidden">{{ $remainingTime }}</p>
            </div>

        </div>
    </div>
    <div class="extra content">
        <div class="ui two column grid">
            <div class="row">
                <div class="column">
                    @can([PermissionsEnum::CAN_CONTROL_DEVICE_TIME, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                    <button id="btnEndTime"
                        class="btnEndTime !text-sm ui fluid small button start-time-button red {{ ($isRunning || $isPaused) ? '!block' : '!hidden' }}"
                        data-id="{{ $device->DeviceID }}" {{ ($isDisabled || $isFree) ? 'disabled' : '' }}>
                        End time
                    </button>
                    <div id="startItems"
                        class="startItems !text-sm ui small floating dropdown labeled icon button {{
                        ($isDisabled || $isFree) ? 'disabled' : '' }} {{ ($isRunning || $isPaused) ? '!hidden' : '!block' }}"
                        data-id="{{ $device->DeviceID }}">
                        <i class="dropdown icon"></i>
                        Start Time
                        <div class="menu">
                            <div class="item" data-type="open" data-value="{{ $openTime ? $openTime->Time : 0 }}"
                                data-rate="{{ $openTime ? $openTime->Rate : 0 }}">
                                Open time
                            </div>
                            <div class="item" data-type="rated" data-value="{{ $baseTime ? $baseTime->Time : 0 }}"
                                data-rate="{{ $baseTime ? $baseTime->Rate : 0 }}">
                                Start {{ $baseTime ? convertMinutesToHoursAndMinutes($baseTime->Time) : "Time" }}
                            </div>
                        </div>
                    </div>
                    @endcan
                </div>
                <div class="column">
                    @can([PermissionsEnum::CAN_CONTROL_DEVICE_TIME, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                    @php
                    $buttonClass = 'ui fluid small button pause-time-button';
                    $buttonText = 'Pause time';
                    $isButtonDisabled = ($isDisabled || $isInactive);

                    if ($isOpenTime && $isPaused)
                    {
                    $isButtonDisabled = false;
                    }
                    else if ($isOpenTime && !$isPaused) {
                    $isButtonDisabled = true;
                    }

                    if (!$isButtonDisabled && $isPaused) {
                    $buttonClass .= ' green';
                    $buttonText = 'Resume';
                    }
                    @endphp
                    <button class="{{ $buttonClass }} !text-sm" data-id="{{ $device->DeviceID }}" {{ $isButtonDisabled || $isFree
                        ? 'disabled' : '' }}>
                        {{ $buttonText }}
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        <div class="one two column stackable grid">
            <div class="column">
                @can([PermissionsEnum::CAN_CONTROL_DEVICE_TIME, PermissionsEnum::ALL_ACCESS_TO_DEVICE])
                @if ($increments->count() == 1)
                <button data-id="{{ $device->DeviceID }}" data-time="{{ $increments->first()->Time }}"
                    data-rate="{{ $increments->first()->Rate }}" class="ui small button extend-time-single-button !text-sm" {{
                    ($isDisabled || !$isRunning || $isOpenTime) ? 'disabled' : '' }}>Extend {{
                    convertMinutesToHoursAndMinutes($increments->first()->Time) }}</button>
                @elseif ($increments->count() > 1)
                <div id="extendItems"
                    class="ui small !text-sm floating dropdown labeled icon button {{ ($isDisabled || !$isRunning || $isOpenTime) ? 'disabled' : '' }}"
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deviceId = "{{ $device->DeviceID }}";

        const startButton = document.querySelector('.start-time-button[data-id="{{ $device->DeviceID }}"]');
        const startTimeCollection = document.querySelector('.startItems[data-id="{{ $device->DeviceID }}"]');
        // const endButton = document.getElementById('btnEndTime');
        const endButton = document.querySelector(`.btnEndTime[data-id="${deviceId}"]`);
        const pauseButton = document.querySelector('.pause-time-button[data-id="{{ $device->DeviceID }}"]');
        const singleExtendButton = document.querySelector('.extend-time-single-button[data-id="{{ $device->DeviceID }}"]');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            function updateStatusRibbon(deviceId, newStatus) {
                const statusRibbon = document.getElementById(`device-status-${deviceId}`);
                const statusClasses = {
                    'Pending Configuration': 'grey',
                    'Running': 'green',
                    'Inactive': 'yellow',
                    'Disabled': 'red',
                    'Pause' : 'lightgray'
                };
                statusRibbon.className = `ui ${statusClasses[newStatus]} ribbon label`;
                statusRibbon.textContent = newStatus;
            }

            function convertMinutesToHoursAndMinutes(minutes) {
                const hours = Math.floor(minutes / 60);
                const remainingMinutes = minutes % 60;

                if (hours > 0 && remainingMinutes > 0) {
                    return `${hours} hr(s) ${remainingMinutes} min(s)`;
                } else if (hours > 0) {
                    return `${hours} hr(s)`;
                } else {
                    return `${remainingMinutes} min(s)`;
                }
            }

            function convertSecondsToTimeFormat(seconds) {
                
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const remainingSeconds = seconds % 60;

                let timeString = '';

                if (hours > 0) {
                    timeString += `${hours} hr(s) `;
                }
                if (minutes > 0) {
                    timeString += `${minutes} min(s) `;
                }
                timeString += `${remainingSeconds} sec(s)`;

                return timeString;
            }

            function formatTime(date) {
                const hours = date.getHours().toString().padStart(2, '0');
                const minutes = date.getMinutes().toString().padStart(2, '0');
                const seconds = date.getSeconds().toString().padStart(2, '0');
                return `${hours}:${minutes}:${seconds}`;
            }

            function convertTo12HourFormat(timeString) {
                let [hours, minutes, seconds] = timeString.split(':').map(Number);
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;
                return `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')} ${ampm}`;
            }

            function updateUI(deviceId, startTime, endTime, totalTime, totalRate, reset = false) {
                const startTimeElement = document.getElementById(`lblStartTime-${deviceId}`);
                const endTimeElement = document.getElementById(`lblEndTime-${deviceId}`);
                const totalTimeElement = document.getElementById(`lblTotalTime-${deviceId}`);
                const totalRateElement = document.getElementById(`lblTotalRate-${deviceId}`);

                if (reset)
                {
                    startTimeElement.textContent = `Start time: --:--:--`;
                    endTimeElement.textContent = `End time: --:--:--`;
                    totalTimeElement.textContent = `Total time: 0 hr 0 mins`;
                    totalRateElement.textContent = `Total charge/rate: PHP 0.00`;
                }
                else
                {
                    startTimeElement.textContent = `Start time: ${startTime ? convertTo12HourFormat(formatTime(new Date(startTime))) : 'N/A'}`;
                    endTimeElement.textContent = `End time: ${endTime ? convertTo12HourFormat(formatTime(new Date(endTime))) : 'N/A'}`;
                    totalTimeElement.textContent = `Total time: ${convertMinutesToHoursAndMinutes(totalTime)}`;
                    totalRateElement.textContent = `Total charge/rate: PHP ${(Number(totalRate)).toFixed(2)}`;
                }
            }

            function showNotification(deviceId, message) {
                const banner = document.getElementById(`notification-banner-${deviceId}`);
                banner.textContent = message;
                banner.classList.add('show');
                setTimeout(() => {
                    banner.classList.remove('show');
                }, 3000);
            }

        if (singleExtendButton) {
            singleExtendButton.addEventListener('click', function() {
                const deviceId = this.getAttribute('data-id');
                const extendTime = this.getAttribute('data-time');
                const extendTimeRate = this.getAttribute('data-rate');

                showLoading();

                fetch(`/device-time/extend/${deviceId}`, {
                method: 'POST',
                headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        increment: extendTime,
                        rate: extendTimeRate
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        showToast("Error on device response. Please see logs for more info", 'error');
                        return response.text().then(text => { throw new Error(text); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        fetchActiveTransactions(); 
                        hideLoading();
                        showNotification(deviceId, 'Extended time');
                        updateUI(deviceId, new Date(data.startTime), new Date(data.endTime), data.totalTime, data.totalRate);
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        hideLoading();
                        showToast("Error on device response. Please see logs for more info", 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    showToast("Possible network error occured. Please see logs for more info", 'error');
                });
            });
        }

        if (endButton)
        {
            endButton.addEventListener('click', function () {
                const deviceId = this.getAttribute('data-id');
                $(`#endTimeModal-${deviceId}`).modal('show');
                    return;
            });
        }

        if (startButton)
        {
            startButton.addEventListener('click', function () {
                const deviceId = this.getAttribute('data-id');
                const action = this.textContent.trim().includes('Start') ? 'start' : 'end';

                if (action === 'end') {
                    $(`#endTimeModal-${deviceId}`).modal('show');
                    return;
                }

                showLoading();

                const route = action === 'start' ? `/device-time/start/${deviceId}` : `/device-time/end/${deviceId}/0`;
                showLoading();
                fetch(route, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        showToast("Error on device response. Please see logs for more info", 'error');
                        return response.text().then(text => { throw new Error(text); });
                    }
                    return response.json();
                })
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        if (action === 'start') {
                            fetchActiveTransactions(); location.reload();
                            this.textContent = 'End time';
                            this.classList.add('red');
                            const extendButtonMenu = document.querySelector(`.dropdown[data-id="${deviceId}"]`);
                            const extendButton = document.querySelector(`.extend-time-single-button[data-id="${deviceId}"]`);
                            const pauseButton = document.querySelector(`.pause-time-button[data-id="${deviceId}"]`);
                           
                            if (extendButtonMenu) {
                                extendButtonMenu.classList.remove('disabled');
                            }
                            if (extendButton) {
                                extendButton.classList.remove('disabled');
                                extendButton.removeAttribute('disabled'); 
                            }
                            if (pauseButton) {
                                pauseButton.classList.remove('disabled');
                                pauseButton.removeAttribute('disabled');
                            }

                            startButton.classList.add('red');

                            updateStatusRibbon(deviceId, 'Running');
                            updateUI(deviceId, data.startTime, data.endTime, data.totalTime, data.totalRate);
                        }
                    } else {
                        showToast("Error on device response. Please see logs for more info", 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    showToast("Possible network error occured. Please see logs for more info", 'error');
                });
            });
        }

        const startItems = document.querySelectorAll('#startItems .item');

        startItems.forEach(item => {
            if (!item.classList.contains('event-attached')) {
                
                item.addEventListener('click', function () {
                    
                    const deviceId = this.closest('.dropdown').getAttribute('data-id');
                    const timingType = this.getAttribute('data-type');
                    const baseTime = this.getAttribute('data-value');
                    const baseRate = this.getAttribute('data-rate');

                    showLoading();

                    if (timingType === 'rated')
                    {
                        const route = `/device-time/start/rated/${deviceId}`;
                        showLoading();
                        fetch(route, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({})
                        })
                        .then(response => {
                            if (!response.ok) {
                                showToast("Error on device response. Please see logs for more info", 'error');
                                return response.text().then(text => { throw new Error(text); });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                fetchActiveTransactions(); location.reload();

                                const btnEndButton = document.querySelector(`.btnEndTime[data-id="${deviceId}"]`);
                                const startTimeCollections = document.querySelector(`.startItems[data-id="${deviceId}"]`);

                                if (btnEndButton)
                                {
                                    btnEndButton.classList.remove('!hidden');
                                    btnEndButton.classList.add('!block');
                                }

                                startTimeCollections.classList.add('!hidden');
                                startTimeCollections.classList.remove('!block');

                                const extendButtonMenu = document.querySelector(`.dropdown[data-id="${deviceId}"]`);
                                const extendButton = document.querySelector(`.extend-time-single-button[data-id="${deviceId}"]`);
                                const pauseButton = document.querySelector(`.pause-time-button[data-id="${deviceId}"]`);

                                if (extendButtonMenu) {
                                    extendButtonMenu.classList.remove('disabled');
                                }
                                if (extendButton) {
                                    extendButton.classList.remove('disabled');
                                    extendButton.removeAttribute('disabled'); 
                                }
                                if (pauseButton) {
                                    pauseButton.classList.remove('disabled');
                                    pauseButton.removeAttribute('disabled');
                                }

                                updateStatusRibbon(deviceId, 'Running');
                                updateUI(deviceId, data.startTime, data.endTime, data.totalTime, data.totalRate);
                            } else {
                                showToast("Error on device response. Please see logs for more info", 'error');
                            }
                            hideLoading();
                        })
                        .catch(error => {
                            hideLoading();
                            showToast("Possible network error occured. Please see logs for more info", 'error');
                        });
                    }
                    else 
                    {
                        if (parseInt(baseTime) > 0)
                        {
                            const route = `/device-time/start/open/${deviceId}`;
                            showLoading();
                            fetch(route, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                },
                                body: JSON.stringify({})
                            })
                            .then(response => {
                                if (!response.ok) {
                                    showToast("Error on device response. Please see logs for more info", 'error');
                                    return response.text().then(text => { throw new Error(text); });
                                }
                                return response.json();
                            })
                            .then(data => {
                                hideLoading();
                                if (data.success) {
                                    fetchActiveTransactions(); location.reload();

                                    if (endButton)
                                    {
                                        endButton.classList.remove('!hidden');
                                        endButton.classList.add('!block');
                                    }

                                    startTimeCollection.classList.add('!hidden');
                                    startTimeCollection.classList.remove('!block');

                                    const extendButtonMenu = document.querySelector(`.dropdown[data-id="${deviceId}"]`);
                                    const extendButton = document.querySelector(`.extend-time-single-button[data-id="${deviceId}"]`);

                                    if (extendButtonMenu) {
                                        extendButtonMenu.classList.remove('disabled');
                                    }
                                    if (extendButton) {
                                        extendButton.classList.remove('disabled');
                                        extendButton.removeAttribute('disabled'); 
                                    }
                                
                                    updateStatusRibbon(deviceId, 'Running');
                                    updateUI(deviceId, data.startTime, data.endTime, data.totalTime, data.totalRate);
                                } else {
                                    showToast("Error on device response. Please see logs for more info", 'error');
                                }
                            })
                            .catch(error => {
                                hideLoading();
                                showToast("Possible network error occured. Please see logs for more info", 'error');
                            });
                        }
                        else {
                            showToast("No configuration for Open Time", 'error');
                            hideLoading();
                        }
                    }
                });
                item.classList.add('event-attached');
            }
        });

        if (pauseButton)
        {
            pauseButton.addEventListener('click', function () {
                const deviceId = this.getAttribute('data-id');
                const action = this.textContent.trim().includes('Pause') ? 'pause' : 'continue';
                let remainingTime = 0;

                const remainingTimeContainer = document.querySelector(`.remaining-time-container[data-device-id="${deviceId}"]`);
                if (remainingTimeContainer)
                {
                    const bareRemainingTimeElement = remainingTimeContainer.querySelector('.bare-remaining-time');
                    if (bareRemainingTimeElement) {
                        const bareRemainingTime = bareRemainingTimeElement.textContent;
                        if (bareRemainingTime)
                        {
                            remainingTime = bareRemainingTime;
                        }
                        
                    }
                }

                showLoading();

                const route = action === 'pause' ? `/device-time/pause/${deviceId}/${remainingTime}` : `/device-time/resume/${deviceId}`;

                fetch(route, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    if (!response.ok) {
                        showToast("Error on device response. Please see logs for more info", 'error');
                        return response.text().then(text => { throw new Error(text); });
                    }
                    return response.json();
                })
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        if (action === 'pause') {
                            fetchActiveTransactions(); location.reload();
                            this.textContent = 'Resume';
                            this.classList.add('green');
                            const extendButtonMenu = document.querySelector(`.dropdown[data-id="${deviceId}"]`);
                            const extendButton = document.querySelector(`.extend-time-single-button[data-id="${deviceId}"]`);
                            const startButton = document.querySelector(`.start-time-button[data-id="${deviceId}"]`);

                            if (extendButtonMenu) {
                                extendButtonMenu.classList.add('disabled');
                            }
                            if (extendButton) {
                                extendButton.classList.add('disabled');
                            }
                            updateStatusRibbon(deviceId, 'Paused: ' + convertSecondsToTimeFormat(data.remaining_time) + ' used');
                        }
                        else {
                            fetchActiveTransactions(); location.reload();
                            this.textContent = 'Pause time';
                            this.classList.remove('green');
                            const extendButtonMenu = document.querySelector(`.dropdown[data-id="${deviceId}"]`);
                            const extendButton = document.querySelector(`.extend-time-single-button[data-id="${deviceId}"]`);
                            const startButton = document.querySelector(`.start-time-button[data-id="${deviceId}"]`);

                            if (extendButtonMenu) {
                                extendButtonMenu.classList.remove('disabled');
                            }
                            if (extendButton) {
                                extendButton.classList.remove('disabled');
                            }

                            updateStatusRibbon(deviceId, 'Running');
                            updateUI(deviceId, data.startTime, data.endTime, data.totalTime, data.totalRate);
                        }
                    } else {
                        showToast("Error on device response. Please see logs for more info", 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    showToast("Possible network error occured. Please see logs for more info", 'error');
                });
            });
        }
        
        const extendItems = document.querySelectorAll('#extendItems .item');
        
        extendItems.forEach(item => {
            if (!item.classList.contains('event-attached')) {
                item.addEventListener('click', function () {
                    
                    const deviceId = this.closest('.dropdown').getAttribute('data-id');
                    const increment = this.getAttribute('data-value');
                    const rate = this.getAttribute('data-rate');
                    showLoading();
                    
                    const route = `/device-time/extend/${deviceId}`;
                    fetch(route, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            increment: increment,
                            rate: rate
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            showToast("Error on device response. Please see logs for more info", 'error');
                            return response.text().then(text => { throw new Error(text); });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            fetchActiveTransactions();
                            hideLoading();
                            showNotification(deviceId, 'Extended time');
                            updateUI(deviceId, new Date(data.startTime), new Date(data.endTime), data.totalTime, data.totalRate);
                            setTimeout(() => {
                            location.reload();
                        }, 1000);
                        } else {
                            hideLoading();
                            showToast("Error on device response. Please see logs for more info", 'error');
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        showToast("Possible network error occured. Please see logs for more info", 'error');
                    });
                });
                item.classList.add('event-attached');
            }
        });

        $(document).on('click', '.confirm-end-time-button', function () {
            const deviceId = $(this).data('id');
            let remainingTime = 0;
            showLoading();

            const remainingTimeContainer = document.querySelector(`.remaining-time-container[data-device-id="${deviceId}"]`);
            if (remainingTimeContainer)
            {
                const bareRemainingTimeElement = remainingTimeContainer.querySelector('.bare-remaining-time');
                if (bareRemainingTimeElement) {
                    const bareRemainingTime = bareRemainingTimeElement.textContent;
                    if (bareRemainingTime)
                    {
                        remainingTime = bareRemainingTime; 
                    }
                    
                }
            }

            const route = `/device-time/end/${deviceId}/${remainingTime}`;
            showLoading();
            fetch(route, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $(`#endTimeModal-${deviceId}`).modal('hide');
                    showLoading();
                    const startButton = document.querySelector(`.start-time-button[data-id="${deviceId}"]`);
                    const baseRate = startButton.getAttribute('data-rate');
                    const extendButtonMenu = document.querySelector(`.dropdown[data-id="${deviceId}"]`);
                    const extendButton = document.querySelector(`.extend-time-single-button[data-id="${deviceId}"]`);
                    const pauseButton = document.querySelector(`.pause-time-button[data-id="${deviceId}"]`);

                    if (extendButtonMenu) {
                        extendButtonMenu.classList.add('disabled');
                    }
                    if (extendButton) {
                        extendButton.classList.add('disabled');
                        extendButton.setAttribute('disabled', 'true');
                    }
                    if (pauseButton) {
                        pauseButton.classList.add('disabled');
                        pauseButton.setAttribute('disabled', 'true');
                        pauseButton.classList.remove('green');
                        pauseButton.textContent = 'Pause time';
                    }
                    
                    updateStatusRibbon(deviceId, 'Inactive');
                    fetchActiveTransactions(); 
                    location.reload();
                    //updateUI(deviceId, data.startTime, data.endTime, data.totalTime, data.totalRate, true);
                } else {
                    showToast("Error on device response. Please see logs for more info", 'error');
                }
            })
            .catch(error => {
                console.log(error);
                showToast("Possible network error occured. Please see logs for more info", 'error');
            });
            hideLoading();
        });

        // Handle cancel end time
        $(document).on('click', '.cancel-button', function () {
            const deviceId = $(this).data('id');
            $(`#endTimeModal-${deviceId}`).modal('hide');
        });
    });
</script>