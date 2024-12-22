@extends('components.layout')

@section('page-title')
@parent
<div class="flex justify-center align-middle">
    <button class="ui icon button" onclick="window.location='{{ route('manage-roles') }}'">
        <i class="arrow left icon"></i>
    </button>
    <span class="self-center ml-2">Manage Roles > Roles</span>
</div>
@endsection

@section('content')
<div class="flex flex-col h-full px-5 py-7 overflow-y-auto overflow-x-hidden">
    <div class="ui stackable equal width grid">
        <div class="row">
            <div class="column">
                <div class="ui icon message">
                    <img src="{{ asset('imgs/roles-2.png') }}" alt="icon" class="ui image w-14 h-14 mr-4">
                    <div class="content">
                        <div class="header">
                            Roles
                        </div>
                        <p>By defining roles with specific permissions, administrators can efficiently manage user
                            capabilities and access levels.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="column">
                <h5 class="ui header">Role Information</h5>
            </div>
        </div>

        <div class="row">
            <div class="column">
                <form id="roleForm" class="ui form">
                    @csrf
                    <div class="ui three column stackable grid">
                        <div class="ten wide column">
                            <div class="three wide column">
                                <div class="field">
                                    <label>Role name</label>
                                </div>
                                <div class="ui right corner labeled small input w-1/2">
                                    <input type="input" name="role_name" id="roleName"
                                        value="{{ $role->RoleName ?? '' }}" placeholder="User manager" required>
                                    <div class="ui right corner label">
                                        <i class="asterisk icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="column mt-5">
                                <div class="field">
                                    <label>Description</label>
                                </div>
                                <div class="ui fluid small input">
                                    <textarea name="role_description" id="role_description"
                                        placeholder="Decription for this role">{{ $role->Description ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="fluid column">
                            <button id="btnDeleteRole" data-id="{{ $role->RoleID }}" type="button"
                                class="ui red small compact labeled icon button !float-right !mt-2"
                                style="display: none;">
                                <i class="trash alternate icon"></i>
                                Delete
                            </button>
                        </div>
                    </div>

                    <div class="ui divider !mt-10 !mb-5"></div>
                    <div class="row">
                        <div class="column">
                            <h5 class="ui header">User Role Permissions</h5>
                        </div>
                    </div>

                    <div class="row !mt-5">
                        <div class="column">
                            <div id="grdPermissions" class="ag-theme-balham w-full !text-base" style="height: 500px;">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="permissions" id="permissions">
                    <input type="hidden" name="role_id" id="hdnRoleId" value="{{ $role->RoleID ?? '' }}">

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
                                <button id="btnUpdateRole" type="submit" class="ui fluid small blue button"
                                    style="display: none;">Update</button>
                                <button id="btnSaveRole" type="submit" class="ui fluid small blue button"
                                    style="display: none;">Save</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<x-modals.delete-role-confirmation-modal />

<script>
    document.addEventListener('DOMContentLoaded', function() {

    const roleID = {{ $role->RoleID ?? 'null' }};
    if (roleID) {
        document.getElementById('btnUpdateRole').style.display = 'block';
        document.getElementById('btnDeleteRole').style.display = 'block';
    } else {
        document.getElementById('btnSaveRole').style.display = 'block';
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
                headerName: "PermissionID",
                field: "PermissionId",
                hide: true
            },
            {
                headerName: "PermissionName",
                field: "PermissionName",
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
        rowData: {!! json_encode($permissions) !!},
        rowSelection: 'multiple',
        rowHeight: 50,
        headerHeight: 60,
        pagination: true,
        paginationPageSize: 10,
        onGridReady: function() {
                var existingPermissions = {!! json_encode($role->rolePermissions->pluck('PermissionID')) !!};
                gridApi.forEachNode(function(node) {
                    if (existingPermissions.includes(node.data.PermissionId)) {
                        node.setSelected(true);
                    }
                });
            }
    };

    const eGridDiv = document.querySelector('#grdPermissions');
    const gridApi = agGrid.createGrid(eGridDiv, gridOptions);

    document.getElementById('btnSaveRole').addEventListener('click', function() {
        event.preventDefault();

        if (!document.getElementById('roleForm').checkValidity()) {
            document.getElementById('roleForm').reportValidity();
            return;
        }

        showLoading();
        var selectedNodes = gridApi.getSelectedNodes();
        var selectedData = selectedNodes.map(node => node.data.PermissionId);

        if (selectedData.length === 0) {
            showToast('Please select at least one permission.', 'error');
            hideLoading();
            return;
        }

        document.getElementById('permissions').value = JSON.stringify(selectedData);

        const formData = new FormData(document.getElementById('roleForm'));

        fetch('{{ route('roles.insert') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                sessionStorage.setItem('toastMessage', JSON.stringify({message: 'Role saved successfully!', type: 'success'}));
                window.location.href = '/manage-roles';
            } else {
                showToast(data.message || 'Error saving role.', 'error');
            }
        })
        .catch(error => {
            showToast('An error occurred: ' + error.message, 'error');
        });

        hideLoading();
    });

    document.getElementById('btnUpdateRole').addEventListener('click', function(event) {
        event.preventDefault();
        showLoading();

        if (!document.getElementById('roleForm').checkValidity()) {
            document.getElementById('roleForm').reportValidity();
            return;
        }

        const selectedNodes = gridApi.getSelectedNodes();
        const selectedData = selectedNodes.map(node => node.data.PermissionId);

        if (selectedData.length === 0) {
            showToast('Please select at least one permission.', 'error');
            hideLoading();
            return;
        }

        document.getElementById('permissions').value = JSON.stringify(selectedData);

        const formData = new FormData(document.getElementById('roleForm'));

        fetch('{{ route('roles.update', ['roleId' => $role->RoleID ?? 0]) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                sessionStorage.setItem('toastMessage', JSON.stringify({message: 'Role updated successfully!', type: 'success'}));
                window.location.href = '{{ route('manage-roles') }}';
            } else {
                showToast(data.message || 'Error updating role.', 'error');
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