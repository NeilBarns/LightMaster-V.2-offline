document.addEventListener('DOMContentLoaded', function () {

    /**
     * Overview Report
     */
    const divFinanceLevelOverviewList = document.querySelectorAll('.divFinanceLevelOverview');

    /**
     * Overview Report
     */
    const divFinanceLevelDetailedList = document.querySelectorAll('.divFinanceLevelDetailed');

    /**
     * Rate and Usage Chart
     */
    const canvasFinanceLevelRateAndUsageList = document.querySelectorAll('#canvasFinanceLevelRateAndUsage');

    /**
     * Detailed button reports
     */
    const btnFinanceLevelDetailedList = document.querySelectorAll('.btnFinanceLevelDetailed');

    /**
     * Overview button reports
     */
    const btnFinanceLevelOverviewList = document.querySelectorAll('.btnFinanceLevelOverview');

    /**
     * Filter overview reports button
     */
    const btnApplyFilterOverviewList = document.querySelectorAll('.btnApplyFilterOverview');

    /**
     * Filter detailed reports button
     */
    const btnApplyFilterDetailedList = document.querySelectorAll('.btnApplyFilterDetailed');

    /**
     * Export report button
     */
    const btnFinanceLevelExportCsvList = document.querySelectorAll('#btnFinanceLevelExportCsv');

    //#region Device specific Monthly/Daily Rate and Usage Reports

    if (canvasFinanceLevelRateAndUsageList)
    {
        canvasFinanceLevelRateAndUsageList.forEach((item) => {
            if (item)
            {
                new DeviceRateAndUsageReportHandler(0, item, VIEWTYPE.Monthly, DATA_INCLUSION_TYPE.FULL).DoSession();
            }
        });
    }
    
    //#endregion

    //#region Device Overview table

    if (divFinanceLevelOverviewList)
    {
        divFinanceLevelOverviewList.forEach((item) => {
            if (item)
            {
                const grdFinanceLevelOverview = getGrdFinanceLevelOverview();
    
                if (grdFinanceLevelOverview)
                {
                    new DeviceOverviewTimeTransactionsReportHandler([], grdFinanceLevelOverview).DoSession();
                }
            }
        });
    }
    
    //#endregion

    //#region Device Detailed table

    if (divFinanceLevelDetailedList) 
    {
        divFinanceLevelDetailedList.forEach((item) => 
        {
            if (item)
            {
                const grdFinanceLevelDetailed = getGrdFinanceLevelDetailed();
            
                if (grdFinanceLevelDetailed) 
                {               
                    new DeviceDetailedTimeTransactionsReportHandler([], grdFinanceLevelDetailed).DoSession();
                }
            }
        });
    }
        
        
    //#endregion
    
    //#region Filter Overview reports

    if (btnApplyFilterOverviewList) {
        btnApplyFilterOverviewList.forEach((item) => {
            if (!item.classList.contains('event-attached')) {
                item.addEventListener('click', function() {
                    const dateStartDateOverview = getDateStartDateOverview();
                    const dateEndDateOverview = getDateEndDateOverview();
                    const ddlDeviceOverview = getDdlDeviceOverview();
    
                    if (dateStartDateOverview &&
                        dateEndDateOverview &&
                        ddlDeviceOverview
                    ) {
                        const dateFrom = dateStartDateOverview.value;
                        const dateTo = dateEndDateOverview.value;
                        const grdFinanceLevelOverview = getGrdFinanceLevelOverview();
                        const deviceIds = $('#ddlDeviceOverview').val();
    
                        if (grdFinanceLevelOverview) {
                            new DeviceOverviewTimeTransactionsReportHandler(deviceIds, grdFinanceLevelOverview, dateFrom, dateTo).DoSession();
                        }
                    }
                });
                item.classList.add('event-attached');
            }
        });
    }

    //#endregion

    //#region Filter Detailed reports

    if (btnApplyFilterDetailedList) {
        btnApplyFilterDetailedList.forEach((item) => {
            if (!item.classList.contains('event-attached')) {
                item.addEventListener('click', function() {
                    const dateStartDateDetailed = getDateStartDateDetailed();
                    const dateEndDateDetailed = getDateEndDateDetailed();
                    const ddlDeviceDetailed = getDdlDeviceDetailed();
    
                    if (dateStartDateDetailed &&
                        dateEndDateDetailed &&
                        ddlDeviceDetailed
                    ) {
                        const dateFrom = dateStartDateDetailed.value;
                        const dateTo = dateEndDateDetailed.value;
                        const grdFinanceLevelDetailed = getGrdFinanceLevelDetailed();
                        const deviceIds = $('#ddlDeviceDetailed').val();
    
                        if (grdFinanceLevelDetailed) {
                            new DeviceDetailedTimeTransactionsReportHandler(deviceIds, grdFinanceLevelDetailed, dateFrom, dateTo).DoSession();
                        }
                    }
    
                });
                item.classList.add('event-attached');
            }
        });
    }

    //#endregion

    //#region Detailed reports button

    if (btnFinanceLevelDetailedList)
    {
        btnFinanceLevelDetailedList.forEach((item) => {
            if (!item.classList.contains('event-attached')) {
                item.addEventListener('click', function() {
                    
                    const divFinanceLevelDetailed = getDivFinanceLevelDetailed();
                    const divFinanceLevelOverview = getDivFinanceLevelOverview();
                    const btnFinanceLevelOverview = getBtnFinanceLevelOverview();

                    if (divFinanceLevelDetailed)
                    {
                        divFinanceLevelDetailed.classList.add("!block");
                        divFinanceLevelDetailed.classList.remove("!hidden");
                    }

                    if (divFinanceLevelOverview)
                    {
                        divFinanceLevelOverview.classList.remove("!block");
                        divFinanceLevelOverview.classList.add("!hidden");
                    }

                    this.classList.add("bg-blue-500", "text-white");
                    this.classList.remove("bg-gray-200", "text-gray-800");
                    this.classList.add("active");

                    if (btnFinanceLevelOverview)
                    {
                        btnFinanceLevelOverview.classList.remove("bg-blue-500", "text-white");
                        btnFinanceLevelOverview.classList.add("bg-gray-200", "text-gray-800");
                        btnFinanceLevelOverview.classList.remove("active");
                    }
                });
                item.classList.add('event-attached');
            }
        });
    }

    //#endregion

    //#region Overview reports button

    if (btnFinanceLevelOverviewList)
    {
        btnFinanceLevelOverviewList.forEach((item) => {
            if (!item.classList.contains('event-attached')) {
                item.addEventListener('click', function() {
                    
                    const divFinanceLevelDetailed = getDivFinanceLevelDetailed();
                    const divFinanceLevelOverview = getDivFinanceLevelOverview();
                    const btnFinanceLevelDetailed = getBtnFinanceLevelDetailed();

                    if (divFinanceLevelOverview)
                    {
                        divFinanceLevelOverview.classList.add("!block");
                        divFinanceLevelOverview.classList.remove("!hidden");
                    }

                    if (divFinanceLevelDetailed)
                    {
                        divFinanceLevelDetailed.classList.remove("!block");
                        divFinanceLevelDetailed.classList.add("!hidden");
                    }

                    this.classList.add("bg-blue-500", "text-white");
                    this.classList.remove("bg-gray-200", "text-gray-800");
                    this.classList.add("active");
                        
                    if (btnFinanceLevelDetailed)
                    {
                        btnFinanceLevelDetailed.classList.remove("bg-blue-500", "text-white");
                        btnFinanceLevelDetailed.classList.add("bg-gray-200", "text-gray-800");
                        btnFinanceLevelDetailed.classList.remove("active");
                    }
                });
                item.classList.add('event-attached');
            }
        });
    }

    //#endregion

    //#region Overview export button

    if (btnFinanceLevelExportCsvList) {
        btnFinanceLevelExportCsvList.forEach((item) => {
            if (!item.classList.contains('event-attached')) {
                item.addEventListener('click', function() {
                    
                    const btnFinanceLevelOverview = getBtnFinanceLevelOverview();
                    const btnFinanceLevelDetailed = getBtnFinanceLevelDetailed();

                    if (btnFinanceLevelOverview.classList.contains('active'))
                    {
                        const dateStartDateOverview = getDateStartDateOverview();
                        const dateEndDateOverview = getDateEndDateOverview();
                        const ddlDeviceOverview = getDdlDeviceOverview();
                        const grdFinanceLevelOverviewExport = getGrdFinanceLevelOverviewExport();

                        if (dateStartDateOverview &&
                            dateEndDateOverview &&
                            ddlDeviceOverview &&
                            grdFinanceLevelOverviewExport
                        ) {
                            const dateFrom = dateStartDateOverview.value;
                            const dateTo = dateEndDateOverview.value;
                            const deviceIds = $('#ddlDeviceOverview').val();
        
                            new DeviceOverviewTimeTransactionsReportExportHandler(deviceIds, 
                                grdFinanceLevelOverviewExport, dateFrom, dateTo).DoSession();
                        }
                    }
                    
                    if (btnFinanceLevelDetailed.classList.contains('active'))
                    {
                        const dateStartDateDetailed = getDateStartDateDetailed();
                        const dateEndDateDetailed = getDateEndDateDetailed();
                        const ddlDeviceDetailed = getDdlDeviceDetailed();
                        const grdFinanceLevelDetailedExport = getGrdFinanceLevelDetailedExport();

                        if (dateStartDateDetailed &&
                            dateEndDateDetailed &&
                            ddlDeviceDetailed &&
                            grdFinanceLevelDetailedExport
                        ) {
                            const dateFrom = dateStartDateDetailed.value;
                            const dateTo = dateEndDateDetailed.value;
                            const deviceIds = $('#ddlDeviceDetailed').val();
        
                            new DeviceDetailedTimeTransactionsReportHandlerExport(deviceIds, 
                                grdFinanceLevelDetailedExport, dateFrom, dateTo).DoSession();
                        }
                    }

                    
                });
                item.classList.add('event-attached');
            }
        });
    }

    //#endregion

});