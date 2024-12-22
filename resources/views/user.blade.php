@extends('components.layout')

@section('page-title')
@parent
<div class="flex justify-center align-middle">
    <button class="ui icon button" onclick="window.location='{{ route('manage-users') }}'">
        <i class="arrow left icon"></i>
    </button>
    <span class="self-center ml-2">Manage Users > Users</span>
</div>
@endsection

@section('content')
<div class="flex flex-col h-full px-5 py-7 overflow-y-auto overflow-x-hidden">
    <div class="ui stackable equal width grid">

        <div class="row">
            <div class="column">
                <div class="ui icon message">
                    <img src="{{ asset('imgs/people.png') }}" alt="icon" class="ui image w-14 h-14 mr-4">
                    <div class="content">
                        <div class="header">
                            Users
                        </div>
                        <p>Control user access and interactions within the Light Master System by organizing users
                            into
                            roles. This enables you to tailor permissions and functionalities, providing each user
                            with
                            the appropriate level of control.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="column">
                <h5 class="ui header">User Information</h5>
            </div>
        </div>

        <div class="row">
            <div class="column">
                <form id="userForm" class="ui form">
                    @csrf
                    <div class="ui three column stackable grid">
                        <div class="four wide column">
                            <div class="field">
                                <label>First name</label>
                            </div>
                            <div class="ui fluid small right labeled input">
                                <input type="input" name="first_name" id="firstName" placeholder="John"
                                    value="{{ $user->FirstName ?? '' }}" required>
                                <div class="ui right corner label">
                                    <i class="asterisk icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="four wide column">
                            <div class="field">
                                <label>Last name</label>
                            </div>
                            <div class="ui fluid small right labeled input">
                                <input type="input" name="last_name" id="lastName" placeholder="Doe"
                                    value="{{ $user->LastName ?? '' }}" required>
                                <div class="ui right corner label">
                                    <i class="asterisk icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="eight wide column">
                            <div class="fluid column">
                                <button id="btnDeleteUser" data-id="{{ $user->UserID }}" type="button"
                                    class="ui red small compact labeled icon button !float-right !mt-2"
                                    style="display: none;">
                                    <i class="trash alternate icon"></i>
                                    Delete
                                </button>
                                <button id="btnDisableUser" data-id="{{ $user->UserID }}" type="button"
                                    class="ui small compact labeled icon button !float-right !mt-2"
                                    style="display: none;">
                                    <i class="power off icon"></i>
                                    Deactivate
                                </button>
                                <button id="btnActivateUser" data-id="{{ $user->UserID }}" type="button"
                                    class="ui small compact labeled icon green button !float-right !mt-2"
                                    style="display: none;">
                                    <i class="power off icon"></i>
                                    Activate
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="ui divider !mt-10 !mb-5"></div>
                    <div class="row !mb-9">
                        <div class="column">
                            <h5 class="ui header">User Account Information</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="column">
                            <div class="ui three column stackable grid">
                                <div class="four wide column">
                                    <div class="field">
                                        <label>Username</label>
                                    </div>
                                    <div class="ui fluid small right labeled input">
                                        <input type="input" name="user_name" id="userName" placeholder="John"
                                            value="{{ $user->UserName ?? '' }}" required>
                                        <div class="ui right corner label">
                                            <i class="asterisk icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="four wide column">
                                    <div class="field">
                                        <label>Password</label>
                                    </div>
                                    <div class="ui fluid small right labeled input">
                                        <input type="password" name="password" id="password" placeholder="Doe"
                                            value="{{ $user->Password ?? '' }}" required>
                                        <div class="ui right corner label">
                                            <i class="asterisk icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="four wide column">
                                    <div class="field">
                                        <label>Confirm Password</label>
                                    </div>
                                    <div class="ui fluid small right labeled input">
                                        <input type="password" name="confirm_password" id="confirmPassword"
                                            placeholder="Doe" value="{{ $user->Password ?? '' }}" required>
                                        <div class="ui right corner label">
                                            <i class="asterisk icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="column">
                            <div class="ui three column stackable grid">
                                <div class="four wide column">
                                    <span class="text-base text-gray-500">Created date: {{ $user->created_at ?
                                        $user->created_at->format('m/d/Y') : 'N/A' }}</span>
                                </div>
                                <div class="four wide column">
                                    <span class="text-base text-gray-500">Last Logged date: {{ $user->LastLoggedDate ?
                                        $user->LastLoggedDate->format('m/d/Y') : 'N/A' }}</span>
                                </div>
                                <div class="four wide column">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ui divider !mt-10 !mb-5"></div>
                    <div class="row !mb-9">
                        <div class="column">
                            <h5 class="ui header">User Roles</h5>
                        </div>
                    </div>

                    <div class="row !mt-5">
                        <div class="column">
                            <div id="grdRoles" class="ag-theme-balham w-full !text-base" style="height: 500px;">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="roles" id="roles">
                    <input type="hidden" name="user_id" value="{{ $user->UserID }}">

                    <div class="column">
                        <div class="ui four column stackable grid">
                            <div class="column">

                            </div>
                            <div class="column">

                            </div>
                            <div class="column">

                            </div>
                            <div class="column">
                                <div class="field">
                                    <label class="invisible">search</label>
                                </div>
                                <button id="btnUpdateUser" type="submit" class="ui fluid small blue button"
                                    style="display: none;">Update</button>
                                <button id="btnSaveUser" type="submit" class="ui fluid small blue button"
                                    style="display: none;">Save</button>
                            </div>
                        </div>

                    </div>

                </form>
            </div>
        </div>





    </div>
</div>

<x-modals.delete-user-confirmation-modal />

<script>
    document.addEventListener('DOMContentLoaded', function() {

    const userID = {{ $user->UserID ?? 'null' }};
    const isUserActive = {{ $user->Active ?? 'null' }};
    if (userID) {
        document.getElementById('btnUpdateUser').style.display = 'block';
        document.getElementById('btnDeleteUser').style.display = 'block';

        if (isUserActive)
        {
            document.getElementById('btnDisableUser').style.display = 'block';
        }
        else
        {
            document.getElementById('btnActivateUser').style.display = 'block';
        }

        
    } else {
        document.getElementById('btnSaveUser').style.display = 'block';
    }

    var gridOptions = {
        columnDefs: [
            {
                headerCheckboxSelection: true,
                checkboxSelection: true,
                flex: 0,
                width: 50,
                cellStyle: { display: 'flex', alignItems: 'center' } 
            },
            {
                headerName: "RoleID",
                field: "RoleID",
                hide: true
            },
            {
                headerName: "RoleName",
                field: "RoleName",
                sortable: true,
                filter: true,
                flex: 1,
                cellStyle: { display: 'flex', alignItems: 'center' } 
            },
            {
                headerName: "Description",
                field: "Description",
                sortable: true,
                filter: true,
                flex: 2,
                cellStyle: { display: 'flex', alignItems: 'center' } 
            }
        ],
        defaultColDef: {
            flex: 1,
            resizable: true,
        },
        rowData: {!! json_encode($roles) !!},
        rowSelection: 'multiple',
        rowHeight: 50,
        headerHeight: 60,
        pagination: true,
        paginationPageSize: 10,
        onGridReady: function() {
            var existingPermissions = {!! json_encode($user->roles->pluck('RoleID')) !!};
                gridApi.forEachNode(function(node) {
                    if (existingPermissions.includes(node.data.RoleID)) {
                        node.setSelected(true);
                    }
                });
            }
    };

    const eGridDiv = document.querySelector('#grdRoles');
    const gridApi = agGrid.createGrid(eGridDiv, gridOptions);

    function validatePasswords() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        if (password !== confirmPassword) {
            showToast('Passwords do not match.', 'error');
            return false;
        }
        return true;
    }

    document.getElementById('btnDisableUser').addEventListener('click', function() {
        changeUserStatus(userID, 0);
    });

    document.getElementById('btnActivateUser').addEventListener('click', function() {
        changeUserStatus(userID, 1);
    });

    function changeUserStatus(userId, status) {
        showLoading();

        const url = `/user/status/${userId}/${status}`;

        const formData = new FormData();
        formData.append('userId', userId);
        formData.append('status', status);

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                'Content-Type': 'application/json',  
            }
        })
        .then(response => response.json()) 
        .then(data => {
            hideLoading();

            if (data.success) {
                sessionStorage.setItem('toastMessage', JSON.stringify({message: data.message, type: 'success'}));
                window.location.href = '/manage-users';
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            hideLoading();
            showToast('An error occurred: ' + error.message, 'error');
        });
    }

    document.getElementById('btnSaveUser').addEventListener('click', function() {
        event.preventDefault();
        if (!validatePasswords()) return;

        if (!document.getElementById('userForm').checkValidity()) {
            document.getElementById('userForm').reportValidity();
            return;
        }

        showLoading();
        var selectedNodes = gridApi.getSelectedNodes();
        var selectedData = selectedNodes.map(node => node.data.RoleID);

        if (selectedData.length === 0) {
            showToast('Please select at least one role.', 'error');
            hideLoading();
            return;
        }

        document.getElementById('roles').value = JSON.stringify(selectedData);

        const formData = new FormData(document.getElementById('userForm'));

        fetch('{{ route('user.insert') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                sessionStorage.setItem('toastMessage', JSON.stringify({message: 'User saved successfully!', type: 'success'}));
                window.location.href = '/manage-users';
            } else {
                showToast(data.message || 'Error saving user.', 'error');
            }
        })
        .catch(error => {
            showToast('An error occurred: ' + error.message, 'error');
        });

        hideLoading();
    });

    document.getElementById('btnUpdateUser').addEventListener('click', function(event) {
        event.preventDefault();
        if (!validatePasswords()) return;

        if (!document.getElementById('userForm').checkValidity()) {
            document.getElementById('userForm').reportValidity();
            return;
        }
        
        showLoading();

        const selectedNodes = gridApi.getSelectedNodes();
        const selectedData = selectedNodes.map(node => node.data.RoleID);

        if (selectedData.length === 0) {
            showToast('Please select at least one role.', 'error');
            hideLoading();
            return;
        }

        document.getElementById('roles').value = JSON.stringify(selectedData);

        const formData = new FormData(document.getElementById('userForm'));

        fetch('{{ route('user.update', ['userId' => $user->UserID ?? 0]) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                sessionStorage.setItem('toastMessage', JSON.stringify({message: 'User updated successfully!', type: 'success'}));
                window.location.href = '{{ route('manage-users') }}';
            } else {
                showToast(data.message || 'Error updating user.', 'error');
            }
        })
        .catch(error => {
            showToast('An error occurred: ' + error.message, 'error');
        });

        hideLoading();
    });
});
</script>
@endsection