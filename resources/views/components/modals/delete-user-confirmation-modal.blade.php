<div id="deleteUserConfirmationModal" class="ui modal">
    <div class="header">Delete Confirmation</div>
    <div class="content">
        <div class="ui error message">
            <div class="header">
                Are you sure you want to delete this user?
            </div>
            <p>This action will remove the user and all associated roles. Please confirm to proceed with the
                deletion.</p>
        </div>
    </div>
    <div class="actions">
        <button class="ui small button" id="btnCancelDelete">Cancel</button>
        <button class="ui small red button" id="btnConfirmDeleteUser">Delete</button>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        let deleteId = null;
        
        const confirmDeleteButton = document.getElementById('btnConfirmDeleteUser');
        const cancelDeleteButton = document.getElementById('btnCancelDelete');
        const deleteUserConfirmationModal = document.getElementById('deleteUserConfirmationModal');

        // Ensure the button to trigger delete modal exists and is accessible
        const deleteUserButton = document.getElementById('btnDeleteUser');

        // Attach click event to the delete button if it exists
        if (deleteUserButton) {
            deleteUserButton.addEventListener('click', function() {
                deleteId = this.getAttribute('data-id');
                $(deleteUserConfirmationModal).modal('show');
            });
        }

        confirmDeleteButton.addEventListener('click', function () {
            $(deleteUserConfirmationModal).modal('hide');
            ShowLoading();
            fetch('/user/delete/' + deleteId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    HideLoading();
                   sessionStorage.setItem('toastMessage', JSON.stringify({message: 'User deleted successfully!', type: 'success'}));
                   window.location.href = '{{ route('manage-users') }}'
                } else {
                    HideLoading();
                    showToast('Failed to delete user.', 'error');
                }
            })
            .catch(error => {
                HideLoading();
                showToast('Error: ' + error.message, 'error');
            });
        });

        cancelDeleteButton.addEventListener('click', function () {
            $(deleteUserConfirmationModal).modal('hide');
        });
    });
</script>