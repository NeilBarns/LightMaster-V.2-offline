@extends('components.layout')

@section('page-title')
@parent
<div class="flex justify-center align-middle">
    <button class="ui icon button" onclick="window.location='{{ route('manage-users') }}'">
        <i class="arrow left icon"></i>
    </button>
    <span class="self-center ml-2">Profile</span>
</div>
@endsection

@section('content')
<div class="flex flex-col h-full px-5 py-7 overflow-y-auto overflow-x-hidden">
    <div class="ui stackable equal width grid">

        <div class="row">
            <div class="column">
                <h5 class="ui header">User Information</h5>
            </div>
        </div>

        <div class="row">
            <div class="column">
                <form id="userProfileForm" class="ui form">
                    @csrf
                    <div class="ui three column stackable grid">
                        <div class="four wide column">
                            <div class="field">
                                <label>First name</label>
                            </div>
                            <div class="ui fluid small right labeled input">
                                <input type="input" name="first_name" id="firstName" placeholder="John"
                                    value="{{ $user->FirstName ?? '' }}" readonly>
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
                                    value="{{ $user->LastName ?? '' }}" readonly>
                                <div class="ui right corner label">
                                    <i class="asterisk icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="eight wide column">
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
                                        <input type="input" name="user_name_profile" id="user_name_profile"
                                            placeholder="John" value="{{ $user->UserName ?? '' }}" required>
                                        <div class="ui right corner label">
                                            <i class="asterisk icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="four wide column">
                                    <div class="field">
                                        <label>Password</label>
                                    </div>
                                    {{-- <div class="ui fluid small right labeled input">
                                        <input type="password" name="password" id="password" placeholder="Doe"
                                            value="{{ $user->Password ?? '' }}" required>
                                        <div class="ui right corner label">
                                            <i class="asterisk icon"></i>
                                        </div>
                                    </div> --}}

                                    <div class="ui fluid small right labeled input">
                                        <input type="password" name="password_profile" id="password_profile"
                                            placeholder="Doe" value="{{ $user->Password ?? '' }}" required>
                                        <div class="ui right corner label">
                                            <i class="asterisk icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="four wide column">
                                    <div class="field">
                                        <label>Confirm Password</label>
                                    </div>
                                    {{-- <div class="ui fluid small right labeled input">
                                        <input type="password" name="confirm_password" id="confirmPassword"
                                            placeholder="Doe" value="{{ $user->Password ?? '' }}" required>
                                        <div class="ui right corner label">
                                            <i class="asterisk icon"></i>
                                        </div>
                                    </div> --}}

                                    <div class="ui fluid small right labeled input">
                                        <input type="password" name="confirm_password_profile"
                                            id="confirm_password_profile" placeholder="Doe"
                                            value="{{ $user->Password ?? '' }}" required>
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
                                <button id="btnUpdateUserProfile" type="submit"
                                    class="ui fluid small blue button">Update</button>
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

    var gridOptions = {
        columnDefs: [
            {
                headerName: "RoleID",
                field: "RoleID",
                hide: true
            },
            {
                headerName: "Role name",
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
        const password = document.getElementById('password_profile').value;
        const confirmPassword = document.getElementById('confirm_password_profile').value;
        if (password !== confirmPassword) {
            showToast('Passwords do not match.', 'error');
            return false;
        }
        return true;
    }

    document.getElementById('btnUpdateUserProfile').addEventListener('click', function(event) {
        event.preventDefault();
        if (!validatePasswords()) return;

        if (!document.getElementById('userProfileForm').checkValidity()) {
            document.getElementById('userProfileForm').reportValidity();
            return;
        }
        
        showLoading();

        const formData = new FormData(document.getElementById('userProfileForm'));

        fetch('{{ route('profile.update', ['userId' => $user->UserID ?? 0]) }}', {
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
                window.location.href = '{{ route('profile', ['userId' => $user->UserID ?? 0]) }}';
            } else {
                showToast(data.message || 'Error updating user.', 'error');
            }
        })
        .catch(error => {
            console.log(error);
            showToast('An error occurred: ' + error.message, 'error');
        });

        hideLoading();
    });
});
</script>
@endsection