<div id="deleteDeviceConfirmationModal" class="ui modal">
    <div class="header">Delete Confirmation</div>
    <div class="content">
        <div class="ui error message">
            <div class="header">
                Are you sure you want to delete this device?
            </div>
            <p>This action will reset the device to its factory settings and remove all associated data. Please confirm
                to proceed with the deletion.</p>
        </div>
    </div>
    <div class="actions">
        <button class="ui small button" id="btnCancel">Cancel</button>
        <button class="ui small red button" id="btnConfirmDelete">Delete</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let deleteId = null;
        
        const confirmDeleteButton = document.getElementById('btnConfirmDelete');
        const cancelDeleteButton = document.getElementById('btnCancel');
        const deleteDeviceConfirmationModal = document.getElementById('deleteDeviceConfirmationModal');

        // Ensure the button to trigger delete modal exists and is accessible
        const deleteDeviceButton = document.getElementById('btnDeleteDevice');

        // Attach click event to the delete button if it exists
        if (deleteDeviceButton) {
            deleteDeviceButton.addEventListener('click', function() {
                deleteId = this.getAttribute('data-id');
                $(deleteDeviceConfirmationModal).modal('show');
            });
        }

        confirmDeleteButton.addEventListener('click', function () {
            $(deleteDeviceConfirmationModal).modal('hide');
            ShowLoading();
            fetch('/api/device/' + deleteId + '/request/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.success) {
                   HideLoading();
                   sessionStorage.setItem('toastMessage', JSON.stringify({message: 'Request to delete the device sent successfully!', type: 'success'}));
                   window.location.href = '{{ route('devicemanagement') }}'
                } else {
                    HideLoading();
                    showToast(data.message || "Failed to delete device.", 'error');
                }
            })
            .catch(error => {
                HideLoading();
                console.error('Error:', error);
            });
        });

        cancelDeleteButton.addEventListener('click', function () {
            $(deleteDeviceConfirmationModal).modal('hide');
        });
    });
</script>