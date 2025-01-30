class DeviceDetailedTimeTransactionsReportHandlerExport {
    /**
     * 
     * @param {*} deviceArray 
     * @param {Element} grid
     */
    constructor(deviceArray, gridExport, dateFrom = null, dateTo = null) {
        this.deviceArray = deviceArray;
        this.gridExport = gridExport;
        this.dateFrom = dateFrom;
        this.dateTo = dateTo;
      
        this.gridOptions = null;
    }

    async DoSession() {
        if (!this.gridExport) {
            console.error("Grid container not found");
            return;
        }

        if (!this.dateFrom && !this.dateTo)
            {
                const today = new Date();
    
                const yesterday = new Date(today);
                yesterday.setDate(today.getDate() - 2);
        
                const tomorrow = new Date(today);
                tomorrow.setDate(today.getDate() + 1);
        
                const dateFrom = yesterday.toISOString();
                const dateTo = tomorrow.toISOString();   
    
                this.dateFrom = dateFrom;
                this.dateTo = dateTo;
            }

        try {
            const result = await GetDetailedTransactionsReportsByDevice(this.deviceArray, this.dateFrom, this.dateTo);

            if (!result.success) {
                console.warn("No data returned for device:", this.deviceArray);
                this.gridExport.innerHTML = "<p class='text-center'>No data available.</p>";
                return;
            }

            // Initialize AG Grid
            this.initializeGrid(this.gridExport, result.data);
            this.exportToCsv();
        } catch (error) {
            console.error("Failed to load data for AG Grid:", error);
            this.gridExport.innerHTML = "<p class='text-center text-red-500'>Error loading data.</p>";
        }
    }

    async initializeGrid(gridDivExport, data) 
    {    
        if (gridDivExport.__agGridInstance && gridDivExport.__agGridInstance.api) {
            gridDivExport.__agGridInstance.api.destroy(); // Safely destroy the grid instance
            gridDivExport.__agGridInstance = null; // Clear the instance reference
        }

        gridDivExport.innerHTML = "";

        const columnDefs = [
            { headerName: "Device", field: "deviceName", sortable: true, filter: true },
            { headerName: "Transaction", field: "transactionType", sortable: true, filter: true },
            { headerName: "Open Time?", field: "isOpenTime", sortable: true, filter: true, 
              cellRenderer: params => params.data.isFooter ? "" : (params.value ? "Yes" : "No") },
            { headerName: "Time", field: "transactionDateTime", sortable: true, filter: true },
            { headerName: "Duration", field: "formattedDuration", sortable: true, filter: true },
            { headerName: "Rate", field: "formattedRate", sortable: true, filter: true },
            { headerName: "Reason", field: "reason", filter: true },
            {
                headerName: "Triggered By",
                field: "triggeredBy",
                sortable: true,
                filter: true,
                cellRenderer: params => params.data.isFooter ? "" : (params.value || "System"),
            },
        ];

        // Prepare row data
        const rowData = data.map(transaction => ({
            deviceName: transaction.deviceName,
            transactionType: transaction.transactionType,
            isOpenTime: transaction.isOpenTime,
            transactionDateTime: formatDateTime(transaction.transactionDateTime),
            formattedDuration: formatSecondsToHHMMSS(transaction.duration),
            formattedRate: transaction.rate,
            reason: transaction.reason,
            triggeredBy: transaction.triggeredBy || "System",
        }));

        // AG Grid options
        const gridOptions = {
            columnDefs: columnDefs,
            rowData: rowData,
            pagination: false,
            // paginationPageSize: 10,
            domLayout: "autoHeight",
            getRowStyle: params => {
                if (params.data.isFooter) {
                    return { fontWeight: "bold", backgroundColor: "#f0f0f0" };
                }
                return null;
            },
        };

        this.gridOptions = gridOptions;
        gridDivExport.__agGridInstance = new agGrid.Grid(gridDivExport, gridOptions);
    }

    exportToCsv() {
        if (this.gridOptions) {
            this.gridOptions.api.exportDataAsCsv({

                fileName: `${this.dateFrom}-${this.dateTo} Detailed Transactions ${getFormattedTimestamp()}.csv`,
                processCellCallback: params => {
                    const columnId = params.column.getColId();
    
                    // Format Total Rate as number with 2 decimal places
                    if (columnId === "totalRate") {
                        return parseFloat(params.value).toFixed(2);
                    }
    
                    // Remove "Summary" column by returning null
                    if (columnId === "summary") {
                        return null;
                    }
    
                    // Return other fields as is
                    return params.value;
                },
                skipFooters: true, // Explicitly skip the footer row
            });
        } else {
            console.warn("No grid options found to export data.");
        }
    }
}
