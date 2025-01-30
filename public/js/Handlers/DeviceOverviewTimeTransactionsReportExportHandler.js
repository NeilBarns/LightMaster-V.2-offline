class DeviceOverviewTimeTransactionsReportExportHandler {
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
      
        this.gridExportOptions = null;
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
            const result = await GetOverviewTransactionsReportsByDevice(this.deviceArray, this.dateFrom, this.dateTo);

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
        let exportColumnDefs = null;
        let exportRowData = null;


        if (gridDivExport.__agGridInstance && gridDivExport.__agGridInstance.api) {
            gridDivExport.__agGridInstance.api.destroy(); // Safely destroy the grid instance
            gridDivExport.__agGridInstance = null; // Clear the instance reference
        }

        gridDivExport.innerHTML = "";

        exportColumnDefs = [
            { headerName: "Device", field: "deviceName", sortable: true, filter: true },
            { headerName: "Start Time", field: "startTime", sortable: true, filter: true },
            { headerName: "End Time", field: "endTime", sortable: true, filter: true },
            {
                headerName: "Open Time?",
                field: "isOpenTime",
                sortable: true,
                filter: true,
                valueFormatter: params => (params.value ? "Yes" : "No"), // Format as "Yes" or "No"
            },
            { 
                headerName: "Total Duration", 
                field: "totalDuration", 
                sortable: true, 
                filter: true,
            },
            {
                headerName: "Total Rate",
                field: "totalRate",
                sortable: true,
                filter: true,
                valueFormatter: params => parseFloat(params.value).toFixed(2), // Format as a number with 2 decimals
            },
        ];

        exportRowData = data.map(threadData => {
            const startTransaction = threadData.transactions.find(t => t.transactionType === "Start" || t.transactionType === "Start Free");
            const endTransaction = threadData.transactions.find(t => t.transactionType === "End" || t.transactionType === "End Free");
    
            return {
                thread: threadData.thread,
                deviceName: startTransaction ? startTransaction.deviceName : "Unknown Device",
                startTime: startTransaction ? formatDateTime(startTransaction.transactionDateTime) : "N/A",
                endTime: endTransaction ? formatDateTime(endTransaction.transactionDateTime) : "N/A",
                isOpenTime: startTransaction?.isOpenTime || false,
                totalDuration: formatSecondsToHHMMSS(threadData.totalDuration),
                totalRate: threadData.totalRate,
            };
        }).filter(item => item.deviceName !== "Unknown Device");

        const gridExportOptions = {
            columnDefs: exportColumnDefs,
            rowData: exportRowData,
            pagination: false,
            // paginationPageSize: 100000,
            domLayout: "autoHeight",
            getRowStyle: params => {
                if (params.data.isFooter) {
                    return { fontWeight: "bold", backgroundColor: "#f0f0f0" }; // Highlight footer row
                }
                return null;
            },
        };
        
        this.gridExportOptions = gridExportOptions;
        gridDivExport.__agGridInstance = new agGrid.Grid(gridDivExport, gridExportOptions);
    }
    
    exportToCsv() {
        if (this.gridExportOptions) {
            this.gridExportOptions.api.exportDataAsCsv({

                fileName: `${this.dateFrom}-${this.dateTo} Overview Transactions ${getFormattedTimestamp()}.csv`,
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
    
    booleanRenderer(params) {
        // Ensure "Yes" or "No" is displayed for boolean values
        return params.value ? "Yes" : "No";
    }
    
    currencyFormatter(params) {
        // Format the currency as PHP xxx.xx
        return `PHP ${formatNumberToDecimal(Number(params.value), 2)}`;
    }
}

