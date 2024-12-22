@extends('components.layout')

@section('page-title')
@parent
<div>Manage Users</div>
@endsection

@section('content')
<div class="flex flex-col h-full px-5 py-7 overflow-y-auto overflow-x-hidden">
    <div class="ui stackable equal width grid">
        <div class="row">
            <div class="column">
                <div class="ui icon message">
                    <img src="{{ asset('imgs/users.png') }}" alt="icon" class="ui image w-14 h-14 mr-4">
                    <div class="content">
                        <div class="header">
                            Users
                        </div>
                        <p>Streamline user management by assigning roles that govern access and capabilities in the
                            Light Master System. This approach ensures that users have the necessary permissions to
                            perform their tasks effectively.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <form action="{{ route('user', ['userId' => 0]) }}" method="get">
                    @csrf
                    <button type="submit" id="btnAddUser" class="ui green small button !text-black">
                        <i class="plus icon"></i>
                        Add User
                    </button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <div id="grdUsers" style="height: 500px; width:100%;" class="ag-theme-alpine"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        showLoading();

        // Ensure the users' data is correctly encoded
        var users = {!! json_encode($users) !!};

        // Check if users data is not undefined
        if (users && Array.isArray(users)) {
            // Format the row data to match the new structure
            var formattedRowData = users.map(user => ({
                FullName: user.FirstName + ' ' + user.LastName,
                UserName: user.UserName,
                Roles: user.roles.map(role => role.RoleName).join(', '),
                Active: user.Active == 1 ? 'True' : 'False',
                UserID: user.UserID
            }));

            var gridOptions = {
                columnDefs: [
                    { headerName: "Full Name", field: "FullName", sortable: true, filter: true },
                    { headerName: "User Name", field: "UserName", sortable: true, filter: true },
                    { headerName: "Roles", field: "Roles", sortable: true, filter: true },
                    { headerName: "Active", field: "Active", sortable: true, filter: true }
                ],
                defaultColDef: {
                    flex: 1,
                    resizable: true
                },
                rowData: formattedRowData,
                pagination: true,
                paginationPageSize: 10,
                onRowClicked: function(event) {
                    var userId = event.data.UserID;
                    window.location.href = '/user/' + userId;
                }
            };

            const eGridDiv = document.querySelector('#grdUsers');
            const gridApi = agGrid.createGrid(eGridDiv, gridOptions);
        } else {
            console.error('Users data is not defined or is not an array.');
        }
        
        hideLoading();
    });
</script>

@endsection