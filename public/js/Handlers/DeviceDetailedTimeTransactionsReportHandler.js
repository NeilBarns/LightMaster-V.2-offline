class DeviceDetailedTimeTransactionsReportHandler {
    /**
     * 
     * @param {*} deviceArray 
     * @param {Element} grid
     */
    constructor(deviceArray, grid, dateFrom = null, dateTo = null) {
        this.deviceArray = deviceArray;
        this.grid = grid;
        this.dateFrom = dateFrom;
        this.dateTo = dateTo;
    }

    async DoSession() {
        if (!this.grid) {
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
                this.grid.innerHTML = "<p class='text-center'>No data available.</p>";
                return;
            }

            // Initialize AG Grid
            this.initializeGrid(this.grid, result.data);
        } catch (error) {
            console.error("Failed to load data for AG Grid:", error);
            this.grid.innerHTML = "<p class='text-center text-red-500'>Error loading data.</p>";
        }
    }

    initializeGrid(gridDiv, data) {
        console.log(data);
        if (gridDiv.__agGridInstance && gridDiv.__agGridInstance.api) {
            gridDiv.__agGridInstance.api.destroy(); // Safely destroy the grid instance
            gridDiv.__agGridInstance = null; // Clear the instance reference
        }

        gridDiv.innerHTML = "";

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

        // Calculate totals for the footer
        const totalDuration = data.reduce((sum, transaction) => sum + (transaction.duration || 0), 0);
        const totalRate = data.reduce((sum, transaction) => sum + Number(transaction.rate || 0), 0);

        // Prepare row data
        const rowData = data.map(transaction => ({
            deviceName: transaction.deviceName,
            transactionType: transaction.transactionType,
            isOpenTime: transaction.isOpenTime,
            transactionDateTime: formatDateTime(transaction.transactionDateTime),
            formattedDuration: convertSecondsToTimeFormat(transaction.duration),
            formattedRate: `PHP ${formatNumberToDecimal(Number(transaction.rate), 2)}`,
            reason: transaction.reason,
            triggeredBy: transaction.triggeredBy || "System",
        }));

        // Add footer row
        rowData.push({
            deviceName: "Total",
            transactionType: "",
            isOpenTime: null,
            transactionDateTime: "",
            formattedDuration: convertSecondsToTimeFormat(totalDuration),
            formattedRate: `PHP ${formatNumberToDecimal(totalRate, 2)}`,
            triggeredBy: "",
            isFooter: true,
        });

        // AG Grid options
        const gridOptions = {
            columnDefs: columnDefs,
            rowData: rowData,
            pagination: true,
            paginationPageSize: 10,
            domLayout: "autoHeight",
            getRowStyle: params => {
                if (params.data.isFooter) {
                    return { fontWeight: "bold", backgroundColor: "#f0f0f0" };
                }
                return null;
            },
        };

        gridDiv.__agGridInstance = new agGrid.Grid(gridDiv, gridOptions);
    }
}
