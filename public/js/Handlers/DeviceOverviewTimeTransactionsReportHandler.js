class DeviceOverviewTimeTransactionsReportHandler {
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
            const result = await GetOverviewTransactionsReportsByDevice(this.deviceArray, this.dateFrom, this.dateTo);

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

    initializeGrid(gridDiv, data) 
    {
        if (gridDiv.__agGridInstance && gridDiv.__agGridInstance.api) {
            gridDiv.__agGridInstance.api.destroy(); // Safely destroy the grid instance
            gridDiv.__agGridInstance = null; // Clear the instance reference
        }

        gridDiv.innerHTML = "";

        const columnDefs = [
            { headerName: "Device", field: "deviceName", sortable: true, filter: true },
            { headerName: "Start Time", field: "startTime", sortable: true, filter: true },
            { headerName: "End Time", field: "endTime", sortable: true, filter: true },
            {
                headerName: "Open Time?",
                field: "isOpenTime",
                sortable: true,
                filter: true,
                cellRenderer: params => {
                    return params.data.isFooter ? "" : params.value ? "Yes" : "No";
                },
            },
            { headerName: "Total Duration", field: "totalDuration", sortable: true, filter: true },
            {
                headerName: "Total Rate",
                field: "totalRate",
                sortable: true,
                filter: true,
                dataType: Number,
                valueFormatter: params => {
                    return params.value ? `PHP ${parseFloat(params.value).toFixed(2)}` : "";
                },
            },
            {
                headerName: "Summary",
                field: "summary",
                cellRenderer: params => {
                    if (params.data.isFooter) return "";
                    const link = document.createElement("a");
                    link.href = "#";
                    link.textContent = "View Details";
                    link.onclick = event => {
                        event.preventDefault();
                        const transactions = params.data.transactions;
                        this.showSessionDetailsModal(transactions); 
                    };
                    return link;
                },
            },
        ];
    
        // Calculate totals for footer
        const totalDuration = data.reduce((sum, threadData) => sum + (threadData.totalDuration || 0), 0);
        const totalRate = data.reduce((sum, threadData) => sum + Number(threadData.totalRate || 0), 0);
    
        // Prepare row data
        const rowData = data
        .map(threadData => {
            const startTransaction = threadData.transactions.find(t => t.transactionType === "Start" || t.transactionType === "Start Free");
            const endTransaction = threadData.transactions.find(t => t.transactionType === "End" || t.transactionType === "End Free");

            return {
                thread: threadData.thread,
                deviceName: startTransaction ? startTransaction.deviceName : "Unknown Device",
                startTime: startTransaction ? formatDateTime(startTransaction.transactionDateTime) : "N/A",
                endTime: endTransaction ? formatDateTime(endTransaction.transactionDateTime) : "N/A",
                isOpenTime: startTransaction?.isOpenTime || false,
                totalDuration: convertSecondsToTimeFormat(threadData.totalDuration),
                totalRate: threadData.totalRate,
                transactions: threadData.transactions,
            };
        })
        .filter(item => item.deviceName !== "Unknown Device");


        console.log('rowData', rowData);
    
        // Add footer row
        rowData.push({
            deviceName: "Overall Duration and Rate Total", // Label for the footer row
            startTime: "",
            endTime: "",
            isOpenTime: null, 
            totalDuration: convertSecondsToTimeFormat(totalDuration),
            totalRate: totalRate,
            summary: "",
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
                    return { fontWeight: "bold", backgroundColor: "#f0f0f0" }; // Highlight footer row
                }
                return null;
            },
        };

        gridDiv.__agGridInstance = new agGrid.Grid(gridDiv, gridOptions);
    }
    
    booleanRenderer(params) {
        // Ensure "Yes" or "No" is displayed for boolean values
        return params.value ? "Yes" : "No";
    }
    
    currencyFormatter(params) {
        // Format the currency as PHP xxx.xx
        return `PHP ${formatNumberToDecimal(Number(params.value), 2)}`;
    }
    
    showSessionDetailsModal(transactions) {
        const gridDiv = getGrdSessionDetails(this.deviceArray);

        if (gridDiv)
        {
            // Check if a grid instance exists and destroy it
            if (gridDiv.__agGridInstance && gridDiv.__agGridInstance.api) {
                gridDiv.__agGridInstance.api.destroy(); // Safely destroy the grid instance
                gridDiv.__agGridInstance = null; // Clear the instance reference
            }
        
            // Clear the grid container content
            gridDiv.innerHTML = "";
        
            // Column definitions for the modal grid
            const modalColumnDefs = [
                { headerName: "Transaction", field: "transactionType", sortable: true, filter: true },
                { headerName: "Transaction Date Time", field: "transactionDateTime", sortable: true, filter: true },
                { headerName: "Duration", field: "formattedDuration", sortable: true, filter: true },
                { headerName: "Rate", field: "formattedRate", sortable: true, filter: true },
                { headerName: "Reason", field: "reason", filter: true },
                {
                    headerName: "Triggered By",
                    field: "triggeredBy",
                    sortable: true,
                    filter: true,
                    cellRenderer: params => {
                        // Leave the footer row blank for Triggered By
                        return params.data.isFooter ? "" : params.value || "System";
                    },
                },
            ];
        
            // Calculate totals for footer
            const totalDuration = transactions.reduce((sum, transaction) => sum + (transaction.duration || 0), 0);
            const totalRate = transactions.reduce((sum, transaction) => sum + Number(transaction.rate || 0), 0);
        
            // Prepare row data for the modal
            const modalRowData = transactions.map(transaction => ({
                transactionType: transaction.transactionType,
                transactionDateTime: formatDateTime(transaction.transactionDateTime),
                formattedDuration: convertSecondsToTimeFormat(transaction.duration),
                formattedRate: `PHP ${formatNumberToDecimal(Number(transaction.rate), 2)}`,
                reason: transaction.reason,
                triggeredBy: transaction.triggeredBy || "System",
            }));
        
            // Add footer row data
            modalRowData.push({
                transactionType: "Total", // Label for the footer row
                transactionDateTime: "",
                formattedDuration: convertSecondsToTimeFormat(totalDuration),
                formattedRate: `PHP ${formatNumberToDecimal(totalRate, 2)}`,
                triggeredBy: "", // Explicitly set as empty
                isFooter: true, // Custom field to identify the footer row
            });
        
            // AG Grid options for the modal
            const modalGridOptions = {
                columnDefs: modalColumnDefs,
                rowData: modalRowData,
                pagination: true,
                paginationPageSize: 10,
                domLayout: "normal",
                getRowStyle: params => {
                    if (params.data.isFooter) {
                        return { fontWeight: "bold", backgroundColor: "#f0f0f0" }; // Highlight footer row
                    }
                    return null;
                },
            };
        
            // Initialize AG Grid in the modal
            gridDiv.__agGridInstance = new agGrid.Grid(gridDiv, modalGridOptions);
        
            // Show the modal using Semantic UI
            $("#sessionDetailsModal").modal("show");
        }
    }
}

