@extends('components.layout')

@section('page-title')
@parent
<div>Activity Logs</div>
@endsection

@section('content')
<div class="flex flex-col h-full px-5 py-7 overflow-y-auto overflow-x-hidden">
    <div class="ui stackable equal width grid">
        <div class="row">
            <div class="column">
                <div class="ui icon message">
                    <img src="{{ asset('imgs/activity.png') }}" alt="icon" class="ui image w-14 h-14 mr-4">
                    <div class="content">
                        <div class="header">
                            Activity Logs
                        </div>
                        <p>Review the actions taken within the system and track the activity logs.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <div id="grdActivityLogs" style="height: 500px; width:100%;" class="ag-theme-alpine"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // AG Grid setup
        var gridOptions = {
            columnDefs: [
                { headerName: 'Log ID', field: 'LogID', sortable: true, hide: true },
                { headerName: 'Log', field: 'Log', sortable: true, filter: true },
                {
                    headerName: 'Action by',
                    field: 'user', // Reference to the user object
                    sortable: true,
                    filter: true,
                    valueGetter: params => {
                        if (params.data.user) {
                            return params.data.user.FirstName + ' ' + params.data.user.LastName;
                        } else {
                            return 'Unknown User'; // Fallback if user is null
                        }
                    }
                },
                {
                    headerName: 'Action Date',
                    field: 'created_at',
                    sortable: true,
                    filter: 'agDateColumnFilter', // Enables date filtering
                    valueGetter: params => {
                        let date = new Date(params.data.created_at);
                        return date.toLocaleString('en-US', {
                            month: 'long',
                            day: 'numeric',
                            year: 'numeric',
                            hour: 'numeric',
                            minute: 'numeric',
                            hour12: true
                        });
                    }
                }
            ],
            defaultColDef: {
                flex: 1,
                minWidth: 150,
                resizable: true,
            },
            rowData: {!! json_encode($logs) !!}, // Passing the logs as row data
            pagination: true,
            paginationPageSize: 10,
        };

        // Create the grid
        var eGridDiv = document.querySelector('#grdActivityLogs');
        new agGrid.Grid(eGridDiv, gridOptions);
    });

</script>
@endsection