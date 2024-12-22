<div id="freeLightModal" class="ui modal">
    <div class="header">Free Light Reason</div>
    <div class="content">
        <form class="ui form" id="freeLightForm" action="{{ route('device.free', ['id' => $device->DeviceID]) }}"
            method="POST">
            @csrf
            <div class="field">
                <label>Reason for Free Light</label>
                <textarea class="ui fluid small input" id="freeLightReason" name="reason"
                    placeholder="Enter reason for free light" required></textarea>
            </div>
            <input type="hidden" name="device_id" value="{{ $device->DeviceID }}">
        </form>
    </div>
    <div class="actions">
        <button class="ui small button" id="freeLightCancelButton">Cancel</button>
        <button class="ui small button primary" id="freeLightSaveButton">Save</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const freeLightModal = document.getElementById('freeLightModal');
    const freeLightForm = document.getElementById('freeLightForm');
    const freeLightSaveButton = document.getElementById('freeLightSaveButton');
    const freeLightCancelButton = document.getElementById('freeLightCancelButton');

    freeLightSaveButton.addEventListener('click', function () {
        // Check if the form is valid
        if (!freeLightForm.checkValidity()) {
            freeLightForm.reportValidity();
            return;
        }

        showLoading();

        // Optionally submit via AJAX if you want to handle submission without page reload
        const formData = new FormData(freeLightForm);

        fetch(freeLightForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            console.log(data);
            if (data.success) {
                $(freeLightModal).modal('hide');
                 window.location.href = '/device';
            } else {
                showToast('Failed to activate free light. Please try again.');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showToast('An error occurred. Please try again.');
        });
    });

    freeLightCancelButton.addEventListener('click', function () {
        $(freeLightModal).modal('hide');
    });
});


</script>