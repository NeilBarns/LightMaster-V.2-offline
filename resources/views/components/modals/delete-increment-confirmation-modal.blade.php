<div id="deleteConfirmationModal" class="ui modal">
    <div class="header">Delete Time Increment</div>
    <div class="content">
        <p>Are you sure you want to delete this time increment?</p>
    </div>
    <div class="actions">
        <button class="ui small button" id="cancelDeleteButton">Cancel</button>
        <button class="ui small button red" id="confirmDeleteButton">Delete</button>
    </div>
</div>

<script>
    $(document).ready(function() {
        let deleteId = null;

        $('.delete-increment-button').on('click', function() {
            deleteId = $(this).data('id');
            $('#deleteConfirmationModal').modal('show');
        });

        $('#cancelDeleteButton').on('click', function() {
            $('#deleteConfirmationModal').modal('hide');
        });

        $('#confirmDeleteButton').on('click', function() {
            if (deleteId !== null) {
                $.ajax({
                    url: '/device-time/increment/delete/' + deleteId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(result) {
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        showToast('An error occurred while trying to delete the time increment.');
                    }
                });
            }
            $('#deleteConfirmationModal').modal('hide');
        });
    });
</script>