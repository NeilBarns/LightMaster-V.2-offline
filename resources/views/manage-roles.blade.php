@extends('components.layout')

@section('page-title')
@parent
<div>Manage Roles</div>
@endsection

@section('content')
<div class="flex flex-col h-full px-5 py-7 overflow-y-auto overflow-x-hidden">
    <div class="ui stackable equal width grid">
        <div class="row">
            <div class="column">
                <div class="ui icon message">
                    <img src="{{ asset('imgs/roles.png') }}" alt="icon" class="ui image w-14 h-14 mr-4">
                    <div class="content">
                        <div class="header">
                            Roles
                        </div>
                        <p>Manage which role your user belongs to. Roles organizes the permissions for the users.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <form action="{{ route('role', ['roleId' => 0]) }}" method="get">
                    @csrf
                    <button type="submit" id="btnAddRole" class="ui green small button !text-black">
                        <i class="plus icon"></i>
                        Add Role
                    </button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <div id="grdRoles" style="height: 500px; width:100%;" class="ag-theme-alpine"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        showLoading();
        var gridOptions = {
            columnDefs: [
                { headerName: "RoleID", field: "RoleID", hide: true },
                { headerName: "Role Name", field: "RoleName", sortable: true, filter: true },
                { headerName: "Description", field: "Description", sortable: true, filter: true },
                { headerName: "Created By", field: "CreatedByUserID", sortable: true, filter: true }
            ],
            defaultColDef: {
                flex: 1,
                resizable: true
            },
            rowData: {!! json_encode($roles) !!},
            pagination: true,
            paginationPageSize: 10,
            onRowClicked: function(event) {
                var roleId = event.data.RoleID;
                window.location.href = '/role/' + roleId;
            }
        };

        const eGridDiv = document.querySelector('#grdRoles');
        const gridApi = agGrid.createGrid(eGridDiv, gridOptions);
        hideLoading();
    });
</script>
@endsection