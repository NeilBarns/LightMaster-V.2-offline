<div id="deleteRoleConfirmationModal" class="ui modal">
    <div class="header">Delete Confirmation</div>
    <div class="content">
        <div class="ui error message">
            <div class="header">
                Are you sure you want to delete this role?
            </div>
            <p>This action will remove the role and all associated permissions. Please confirm to proceed with the
                deletion.</p>
        </div>
    </div>
    <div class="actions">
        <button class="ui small button" id="btnCancelDelete">Cancel</button>
        <button class="ui small red button" id="btnConfirmDeleteRole">Delete</button>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        let deleteId = null;
        
        const confirmDeleteButton = document.getElementById('btnConfirmDeleteRole');
        const cancelDeleteButton = document.getElementById('btnCancelDelete');
        const deleteRoleConfirmationModal = document.getElementById('deleteRoleConfirmationModal');

        // Ensure the button to trigger delete modal exists and is accessible
        const deleteRoleButton = document.getElementById('btnDeleteRole');

        // Attach click event to the delete button if it exists
        if (deleteRoleButton) {
            deleteRoleButton.addEventListener('click', function() {
                deleteId = this.getAttribute('data-id');
                console.log('deleteId', deleteId);
                $(deleteRoleConfirmationModal).modal('show');
            });
        }

        confirmDeleteButton.addEventListener('click', function () {
            $(deleteRoleConfirmationModal).modal('hide');
            showLoading();
            fetch('/role/delete/' + deleteId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hideLoading();
                   sessionStorage.setItem('toastMessage', JSON.stringify({message: 'Role deleted successfully!', type: 'success'}));
                   window.location.href = '{{ route('manage-roles') }}'
                } else {
                    hideLoading();
                    showToast('Failed to delete role.', 'error');
                }
            })
            .catch(error => {
                hideLoading();
                showToast('Error: ' + error.message, 'error');
            });
        });

        cancelDeleteButton.addEventListener('click', function () {
            $(deleteRoleConfirmationModal).modal('hide');
        });
    });
</script>