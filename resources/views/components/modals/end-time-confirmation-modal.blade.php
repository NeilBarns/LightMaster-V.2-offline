<div id="endTimeModal-{{ $device->DeviceID }}" class="ui small modal">
    <div class="header">Confirm End Time</div>
    <div class="content">
        <p>Are you sure you want to manually end the time for this device?</p>
    </div>
    <div class="actions">
        <button class="ui small negative button cancel-button" data-id="{{ $device->DeviceID }}">Cancel</button>
        <button class="ui small positive button confirm-end-time-button"
            data-id="{{ $device->DeviceID }}">Confirm</button>
    </div>
</div>