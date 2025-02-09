<div id="nodeExchangeModal" class="ui modal">
    <div class="header">Node Exchange</div>
    <div class="content">
        <div class="ui error message">
            <div class="header">
                Are you sure you want to exchange this device?
            </div>
            <p>This action will delete and reset this device to its factory settings and remove all associated data. Data on this node will then be transferred to the node with the given serial number. Please confirm
                to proceed with the exchange.</p>
        </div>
        <form class="ui form" id="nodeExchangeForm" action="{{ route('device.exchange', ['id' => $device->DeviceID]) }}"
            method="POST">
            @csrf
            <div class="field">
                <label>Node Exchange Serial Number</label>
                <input type="text" class="ui fluid small input" id="nodeSerialNumber" name="serialNumber"
                    placeholder="Serial number of the node replacement" required></textarea>
            </div>
            <div class="field">
                <label>Reason for node exchange</label>
                <textarea class="ui fluid small input" id="nodeExchangeReason" name="reason"
                    placeholder="Enter reason for node exchange" required></textarea>
            </div>
            <input type="hidden" name="device_id" value="{{ $device->DeviceID }}">
        </form>
    </div>
    <div class="actions">
        <button class="ui small button" id="nodeExchangeCancelButton">Cancel</button>
        <button class="ui small button primary" id="nodeExchangeSaveButton">Save</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const nodeExchangeModal = document.getElementById('nodeExchangeModal');
    const nodeExchangeForm = document.getElementById('nodeExchangeForm');
    const nodeExchangeSaveButton = document.getElementById('nodeExchangeSaveButton');
    const nodeExchangeCancelButton = document.getElementById('nodeExchangeCancelButton');

    nodeExchangeSaveButton.addEventListener('click', function () {
        // Check if the form is valid
        if (!nodeExchangeForm.checkValidity()) {
            nodeExchangeForm.reportValidity();
            return;
        }

        $(nodeExchangeModal).modal('hide');
        ShowLoading();

        // Optionally submit via AJAX if you want to handle submission without page reload
        const formData = new FormData(nodeExchangeForm);

        fetch(nodeExchangeForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.success) {
                 window.location.href = '/device';
            } else {
                showToast(data.message || "Possible network error occurred. Please see logs for more info.", 'error');
            }
            HideLoading();
        })
        .catch(error => {
            HideLoading();
            console.error('Error:', error);
            showToast('An error occurred. Please try again.');
        });
    });

    nodeExchangeCancelButton.addEventListener('click', function () {
        $(nodeExchangeModal).modal('hide');
    });
});


</script>