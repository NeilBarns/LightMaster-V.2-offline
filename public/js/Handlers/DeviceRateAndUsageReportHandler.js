let chart;

class DeviceRateAndUsageReportHandler {
    /**
     * 
     * @param {*} deviceId
     * @param {Canvas element to be overridden by chart} canvas 
     * @param {VIEWTYPE enum} viewType 
     * @param {DATA_INCLUSION_TYPE enum} dataInclusionType
     */
    constructor(deviceId, canvas, viewType, dataInclusionType) {
        this.deviceID = deviceId;
        this.canvas = canvas;
        this.viewType = viewType;
        this.dataInclusionType = dataInclusionType;
    }

    DoSession() {
        if (chart) {
            chart.destroy();
        }

        this.canvas.getContext("2d");

        if (this.dataInclusionType === DATA_INCLUSION_TYPE.FULL) {
            chart = new Chart(this.canvas, this.GetConfig(this.viewType));
            this.FetchUsageAndRateChartData(this.deviceID, this.viewType, this.dataInclusionType, true);
        } else {
            chart = new Chart(this.canvas, this.GetConfig(this.viewType));
            this.FetchUsageAndRateChartData(this.deviceID, this.viewType, this.dataInclusionType, false);
        }        
    }

    FetchUsageAndRateChartData(deviceID, viewType, dataInclusionType, isFull) {
        if (viewType === VIEWTYPE.Monthly && isFull) {
            let datasets = [];
            let labels = []; // Initialize labels for months
    
            GetMonthlyReports().then((result) => {
                if (!result.success) {
                    console.error("No data available");
                    return;
                }
    
                // Extract unique months for labels
                labels = result.data.map((monthData) => monthData.month);
    
                // Map data for each device
                const deviceMap = {}; // To keep track of devices and their datasets
    
                result.data.forEach((monthData) => {
                    monthData.devices.forEach((deviceData) => {
                        // Initialize datasets for each device if not already added
                        if (!deviceMap[deviceData.deviceName]) {
                            deviceMap[deviceData.deviceName] = {
                                rate: Array(labels.length).fill(0),
                                usage: Array(labels.length).fill(0),
                            };
    
                            // Add datasets for Rate
                            datasets.push({
                                label: `${deviceData.deviceName} Rate`,
                                data: deviceMap[deviceData.deviceName].rate,
                                backgroundColor: this.getRandomColor(),
                                yAxisID: 'y',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                                borderRadius: {
                                    topLeft: 5, 
                                    topRight: 5,
                                },
                                order: 2
                            });
    
                            // Add datasets for Usage
                            datasets.push({
                                label: `${deviceData.deviceName} Usage`,
                                data: deviceMap[deviceData.deviceName].usage,
                                type: 'line',
                                backgroundColor: this.getRandomColor(),
                                borderColor: 'rgb(66, 66, 71)', 
                                borderWidth: 2,
                                pointRadius: 5, 
                                yAxisID: 'y1',
                                tension: 0.4,
                                cubicInterpolationMode: 'monotone',
                                order: 1
                            });
                        }
    
                        // Populate data for the respective month index
                        const monthIndex = labels.indexOf(monthData.month);
                        if (monthIndex !== -1) {
                            deviceMap[deviceData.deviceName].rate[monthIndex] = Number(deviceData.totalRate);
                            deviceMap[deviceData.deviceName].usage[monthIndex] = Number(deviceData.totalDuration);
                        }
                    });
                });
    
                // Call the method to update the chart
                this.UpdateChartWithMultipleDatasets(labels, datasets);
            });
        } else if (viewType === VIEWTYPE.Monthly) {
            GetMonthlyReportsByDevice(deviceID).then((result) => {
                if (!result.success) {
                    console.error("Failed to fetch data for monthly device");
                } else {
                    const labels = Object.keys(result.data);
                    const rates = Object.values(result.data).map((entry) => entry.totalRate);
                    const usage = Object.values(result.data).map((entry) => entry.totalDuration);
                    this.UpdateChart(labels, rates, usage);
                }
            });
        } else {
            GetDailyReportsByDevice(deviceID).then((result) => {
                if (!result.success) {
                    console.error("Failed to fetch data for daily device");
                } else {
                    const labels = Object.keys(result.data);
                    const rates = Object.values(result.data).map((entry) => entry.totalRate);
                    const usage = Object.values(result.data).map((entry) => entry.totalDuration);
                    this.UpdateChart(labels, rates, usage);
                }
            });
        }
    
        this.UpdateChartTitle(this.viewType);
        this.UpdateReportControlButtons(this.viewType, this.deviceID);
        HideLoading();
    }
    

    UpdateChart(labels, rates, usage) {
        chart.data.labels = labels;
        chart.data.datasets[0].data = rates;
        chart.data.datasets[1].data = usage;
        chart.update();
    }

    UpdateChartWithMultipleDatasets(labels, datasets) {
        if (chart) {
            chart.destroy(); // Destroy the existing chart instance
        }

        chart = new Chart(this.canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets,
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Rate (PHP)',
                        },
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        title: {
                            display: true,
                            text: 'Usage (minutes)',
                        },
                    },
                },
            },
        });
    }

    UpdateChartTitle(viewType) {
        const currentDate = new Date();
        const currentYear = currentDate.getFullYear();
        const currentMonth = currentDate.toLocaleString('default', { month: 'long' });

        const titleText = viewType === VIEWTYPE.Daily ? "Daily Rate and Usage" : "Monthly Rate and Usage";
        const subtitleText = viewType === VIEWTYPE.Daily ? "For the current Month of " + currentMonth : "For the current year of " + currentYear;
        document.getElementById("chartTitle").textContent = titleText;
        document.getElementById("chartSubtitle").textContent = subtitleText;
        chart.update();
    }

    UpdateReportControlButtons(viewType, deviceId) {
        const btnMonthlyDeviceLevel = getBtnMonthlyDeviceLevel(deviceId);
        const btnDailyDeviceLevel = getBtnDailyDeviceLevel(deviceId);

        if (btnMonthlyDeviceLevel && btnDailyDeviceLevel) {
            if (viewType === VIEWTYPE.Daily) {
                btnDailyDeviceLevel.classList.add("bg-blue-500", "text-white");
                btnMonthlyDeviceLevel.classList.remove("bg-blue-500", "text-white");
                btnDailyDeviceLevel.classList.remove("bg-gray-200", "text-gray-800");
                btnMonthlyDeviceLevel.classList.add("bg-gray-200", "text-gray-800");
            } else {
                btnMonthlyDeviceLevel.classList.add("bg-blue-500", "text-white");
                btnDailyDeviceLevel.classList.remove("bg-blue-500", "text-white");
                btnMonthlyDeviceLevel.classList.remove("bg-gray-200", "text-gray-800");
                btnDailyDeviceLevel.classList.add("bg-gray-200", "text-gray-800");
            }
        }
    }

    GetConfig(viewType) {
        const labels = viewType === VIEWTYPE.Daily ? getDaysInCurrentMonth() : monthlyLabels;
    
        return {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: "Rate (PHP)",
                        data: [],
                        backgroundColor: 'rgb(126, 183, 237)', 
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        borderRadius: {
                            topLeft: 5, 
                            topRight: 5,
                        },
                        yAxisID: 'y',
                        order: 2, 
                    },
                    {
                        label: "Usage (Minutes)",
                        data: [],
                        type: 'line',
                        backgroundColor: 'rgb(66, 66, 71)', 
                        borderColor: 'rgb(66, 66, 71)', 
                        borderWidth: 2,
                        pointRadius: 5, 
                        pointBackgroundColor: 'rgb(66, 66, 71)', 
                        yAxisID: 'y1',
                        order: 1, 
                        tension: 0.4, 
                        cubicInterpolationMode: 'monotone'
                    }
                ],
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Rate (PHP)'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false 
                        },
                        title: {
                            display: true,
                            text: 'Usage (minutes)'
                        }
                    }
                }
            }
        };
    }
    

    getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
}
