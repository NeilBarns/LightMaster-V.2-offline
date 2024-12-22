<div id="addIncrementModal" class="ui modal">
    <div id="incrementModalHeader" class="header">Add Time Increment</div>
    <div class="content">
        <form class="ui form" id="addIncrementForm" action="{{ route('device-time.increment.insert') }}" method="POST">
            @csrf
            <div class="field">
                <label>Time (minutes)</label>
                <div class="ui fluid small input">
                    <input type="number" id="increment-time" name="time" placeholder="Enter time in minutes" required>
                </div>
            </div>
            <div class="field">
                <label>Rate (PHP)</label>
                <div class="ui fluid small input">
                    <input type="number" step="0.01" id="increment-rate" name="rate" placeholder="Enter rate in PHP"
                        required>
                </div>
            </div>
            <input type="hidden" name="device_id" value="{{ $device->DeviceID }}">
            <input type="hidden" id="increment-id" name="id">
        </form>
    </div>
    <div class="actions">
        <button class="ui small button" id="cancelButton">Cancel</button>
        <button class="ui small button primary" id="saveButton">Save</button>
    </div>
</div>

<script>
    $(document).ready(function () {
    // Show the modal when the "Add Increment" button is clicked
    $('#addIncrementButton').on('click', function () {
        $('#increment-time').val('');
        $('#increment-rate').val('');
        $('#increment-id').val('');
        $('#addIncrementForm').attr('action', '{{ route("device-time.increment.insert") }}');
        $('#incrementModalHeader').text('Add Time Increment');
        $('#saveButton').text('Save');
        $('#addIncrementModal').modal('show');
    });

    // Show the modal and populate fields when an edit button is clicked
    $('.edit-increment-button').on('click', function () {
        const time = $(this).data('time');
        const rate = $(this).data('rate');
        const id = $(this).data('id');

        $('#increment-time').val(time);
        $('#increment-rate').val(rate);
        $('#increment-id').val(id);
        $('#addIncrementForm').attr('action', `/device-time/increment/update/${id}`);
        $('#incrementModalHeader').text('Edit Time Increment');
        $('#saveButton').text('Update');
        $('#addIncrementModal').modal('show');
    });

    // Close the modal when the "Cancel" button is clicked
    $('#cancelButton').on('click', function () {
        $('#addIncrementModal').modal('hide');
    });

    // Handle the form submission
    $('#saveButton').on('click', function () {
        $('#addIncrementForm').submit();
    });
});
</script>