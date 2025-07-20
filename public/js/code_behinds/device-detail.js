document.addEventListener("DOMContentLoaded", function () {
    /**
     * Update Device Name elements
     */
    const btnSaveNameList = document.querySelectorAll(".btnSaveName");
    const btnSaveNameHdnList = document.querySelectorAll(".btnSaveNameHdn");

    /**
     * Update Base Time elements
     */
    const btnSaveBaseTimeList = document.querySelectorAll(".btnSaveBaseTime");

    /**
     * Update Open Time elements
     */
    const btnSaveOpenTimeList = document.querySelectorAll(".btnSaveOpenTime");

    /**
     * Update Increments (UNFINISHED)
     */
    const btnUpdateIncrementStatusList = document.querySelectorAll(
        ".btnUpdateIncrementStatus"
    );

    /**
     * Update Free Time limit elements
     */
    const btnFreeTimeLimitList = document.querySelectorAll(".btnFreeTimeLimit");

    /**
     * Test Device Light
     */
    const btnTestLightList = document.querySelectorAll(".btnTestLight");

    /**
     * Device node exchange
     */
    const btnNodeExchangeList = document.querySelectorAll(".btnNodeExchange");

    /**
     * Device cancel node exchange
     */
    const btnCancelNodeExchangeList = document.querySelectorAll(
        ".btnCancelNodeExchange"
    );

    /**
     * Start Device Free Light
     */
    const btnStartFreeLight = document.querySelectorAll(".btnStartFreeLight");

    /**
     * Stop Device Free Light
     */
    const btnStopFreeLight = document.querySelectorAll(".btnStopFreeLight");

    /**
     * Deploy Device
     */
    const btnDeployDevice = document.querySelectorAll(".btnDeployDevice");

    /**
     * Disable Device
     */
    const btnDisableDevice = document.querySelectorAll(".btnDisableDevice");

    /**
     * Enable Device
     */
    const btnEnableDevice = document.querySelectorAll(".btnEnableDevice");

    /**
     * Device Remaining time notification
     */
    const btnSaveRemainingTime = document.querySelectorAll(
        ".btnSaveRemainingTime"
    );

    /**
     * Device Rate and Usage Chart
     */
    const btnMonthlyDeviceLevel = document.querySelectorAll(
        ".btnMonthlyDeviceLevel"
    );
    const btnDailyDeviceLevel = document.querySelectorAll(
        ".btnDailyDeviceLevel"
    );
    const canvasDeviceLevelRateAndUsageList = document.querySelectorAll(
        "#canvasDeviceLevelRateAndUsage"
    );

    /**
     * Device Overview Report
     */
    const divDeviceLevelOverview = document.querySelectorAll(
        ".divDeviceLevelOverview"
    );

    /**
     * Device Detailed Report
     */
    const divDeviceLevelDetailed = document.querySelectorAll(
        ".divDeviceLevelDetailed"
    );

    /**
     * Overview report button
     */
    const btnDeviceLevelOverviewList = document.querySelectorAll(
        ".btnDeviceLevelOverview"
    );

    /**
     * Detailed report button
     */
    const btnDeviceLevelDetailedList = document.querySelectorAll(
        ".btnDeviceLevelDetailed"
    );

    /**
     * Update Emergency Passkey elements
     */
    const btnEmergencyPasskeyList =
        document.querySelectorAll(".btnEmergencyPass");

    //#region Update Device Name

    if (btnSaveNameList) {
        btnSaveNameList.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    const deviceId = this.getAttribute("data-id");
                    ShowUpdateDeviceNameControls(deviceId, true);
                });
                item.classList.add("event-attached");
            }
        });
    }

    if (btnSaveNameHdnList) {
        btnSaveNameHdnList.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    const deviceId = this.getAttribute("data-id");
                    const txtDeviceName = getTxtDeviceName(deviceId);

                    if (txtDeviceName) {
                        const newDeviceName = deviceNameInput.value.trim();

                        if (newDeviceName) {
                            ShowLoading();

                            UpdateDeviceName(deviceId, newDeviceName).then(
                                (result) => {
                                    if (!result.success) {
                                        showToast(result.message, "error");
                                    } else {
                                        ShowUpdateDeviceNameControls(
                                            deviceId,
                                            false
                                        );
                                        showToast(result.message, "success");

                                        //Change the Device Name label
                                        //with the new one
                                        const lblDeviceName =
                                            getLblDeviceName(deviceId);
                                        if (lblDeviceName) {
                                            lblDeviceName.textContent =
                                                newDeviceName;
                                        }
                                    }
                                    HideLoading();
                                }
                            );
                        } else {
                            ShowUpdateDeviceNameControls(deviceId, false);
                        }
                    }
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Update Base Time

    if (btnSaveBaseTimeList) {
        btnSaveBaseTimeList.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    const deviceId = this.getAttribute("data-id");
                    const txtBaseTime = getTxtBaseTime(deviceId);
                    const txtBaseRate = getTxtBaseRate(deviceId);

                    if (txtBaseTime && txtBaseRate) {
                        const newBaseTime = txtBaseTime.value;
                        const newBaseRate = txtBaseRate.value;

                        if (newBaseTime > 0 && newBaseRate > 0) {
                            ShowLoading();

                            UpdateDeviceBaseRateTime(
                                deviceId,
                                newBaseTime,
                                newBaseRate
                            ).then((result) => {
                                if (!result.success) {
                                    showToast(result.message, "error");
                                } else {
                                    showToast(result.message, "success");
                                }
                                HideLoading();
                            });
                        } else {
                            showToast(
                                "Please enter a valid base time and rate greater than 0.",
                                "error"
                            );
                        }
                    }
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Update Free Time Limit

    if (btnFreeTimeLimitList) {
        btnFreeTimeLimitList.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    const deviceId = this.getAttribute("data-id");
                    const txtFreeTimeLimit = getTxtFreeTimeLimit(deviceId);

                    if (txtFreeTimeLimit) {
                        const newFreeLightTimeLimitValue =
                            txtFreeTimeLimit.value;

                        if (newFreeLightTimeLimitValue > 0) {
                            ShowLoading();

                            UpdateFreeLightTimeLimit(
                                deviceId,
                                newFreeLightTimeLimitValue
                            ).then((result) => {
                                if (!result.success) {
                                    showToast(result.message, "error");
                                } else {
                                    showToast(result.message, "success");
                                }
                                HideLoading();
                            });
                        } else {
                            showToast(
                                "Please enter a valid free time limit greater than 0.",
                                "error"
                            );
                        }
                    }
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Update Open Time

    if (btnSaveOpenTimeList) {
        btnSaveOpenTimeList.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    const deviceId = this.getAttribute("data-id");
                    const txtOpenTime = getTxtOpenTime(deviceId);
                    const txtOpenRate = getTxtOpenRate(deviceId);

                    if (txtOpenTime && txtOpenRate) {
                        const newOpenTime = txtOpenTime.value;
                        const newOpenRate = txtOpenRate.value;

                        if (newOpenTime > 0 && newOpenRate > 0) {
                            ShowLoading();

                            UpdateDeviceOpenRateTime(
                                deviceId,
                                newOpenTime,
                                newOpenRate
                            ).then((result) => {
                                if (!result.success) {
                                    showToast(result.message, "error");
                                } else {
                                    showToast(result.message, "success");
                                }
                                HideLoading();
                            });
                        } else {
                            showToast(
                                "Please enter a valid open time and rate greater than 0.",
                                "error"
                            );
                        }
                    }
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Increments element (UNFINISHED)

    if (btnUpdateIncrementStatusList) {
        btnUpdateIncrementStatusList.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    const deviceTimeID = this.getAttribute("data-id");
                    const deviceId = this.getAttribute("data-device-id");
                    const incrementStatus = this.getAttribute("data-status");
                    const newStatus = incrementStatus === "1" ? "0" : "1";

                    ShowLoading();

                    UpdateDeviceIncrementStatus(
                        deviceId,
                        deviceTimeID,
                        newStatus
                    ).then((result) => {
                        if (!result.success) {
                            showToast(result.message, "error");
                        } else {
                            showToast(result.message, "success");

                            this.setAttribute("data-status", newStatus);

                            //"Disabled" banner on top of the increment
                            const bannerIncrementStatus =
                                getBannerIncrementStatus(deviceTimeID);
                            const imageIncrementStatus =
                                getImageIncrementStatus(deviceTimeID);

                            if (bannerIncrementStatus) {
                                if (newStatus === "0") {
                                    bannerIncrementStatus.classList.add(
                                        "!block"
                                    );
                                    bannerIncrementStatus.classList.remove(
                                        "!hidden"
                                    );
                                } else {
                                    bannerIncrementStatus.classList.remove(
                                        "!block"
                                    );
                                    bannerIncrementStatus.classList.add(
                                        "!hidden"
                                    );
                                }
                            }

                            if (imageIncrementStatus) {
                                if (newStatus === "0") {
                                    imageIncrementStatus.src =
                                        imageIncrementStatus.dataset.enableUrl;
                                } else {
                                    imageIncrementStatus.src =
                                        imageIncrementStatus.dataset.disableUrl;
                                }
                            }
                        }
                        HideLoading();
                    });
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Test Device Light

    if (btnTestLightList) {
        btnTestLightList.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    const deviceId = this.getAttribute("data-id");

                    ShowLoading();

                    TestLight(deviceId).then((result) => {
                        if (!result.success) {
                            showToast(result.message, "error");
                            HideLoading();
                        } else {
                            setTimeout(function () {
                                HideLoading();
                            }, 10000); //Test for 10 seconds
                        }
                    });
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Device node exchange

    if (btnNodeExchangeList) {
        btnNodeExchangeList.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    $(nodeExchangeModal).modal("show");
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Device cancel node exchange

    if (btnCancelNodeExchangeList) {
        btnCancelNodeExchangeList.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    const deviceId = this.getAttribute("data-id");

                    ShowLoading();

                    CancelDeviceExchange(deviceId).then((result) => {
                        if (!result.success) {
                            showToast(result.message, "error");
                            setTimeout(() => {
                                window.location.href = "/device";
                            }, 1500);
                        } else {
                            window.location.href = "/device";
                        }
                        HideLoading();
                    });
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Start Device Free Light

    if (btnStartFreeLight) {
        btnStartFreeLight.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    $(freeLightModal).modal("show");
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Stop Device Free Light

    if (btnStopFreeLight) {
        btnStopFreeLight.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    const deviceId = this.getAttribute("data-id");

                    ShowLoading();

                    StopDeviceFreeLight(deviceId).then((result) => {
                        if (!result.success) {
                            showToast(result.message, "error");
                            setTimeout(() => {
                                window.location.href = "/device";
                            }, 1500);
                        } else {
                            window.location.href = "/device";
                        }
                        HideLoading();
                    });
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Deploy Device

    if (btnDeployDevice) {
        btnDeployDevice.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    ShowLoading();
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Disable device

    if (btnDisableDevice) {
        btnDisableDevice.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    ShowLoading();
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Enable device

    if (btnEnableDevice) {
        btnEnableDevice.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    ShowLoading();
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Device remaining time notification

    if (btnSaveRemainingTime) {
        btnSaveRemainingTime.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    const deviceId = this.getAttribute("data-id");
                    const txtRemainingTime = getTxtRemainingTime(deviceId);

                    if (txtRemainingTime) {
                        const newRemainingTime = txtRemainingTime.value;

                        if (newRemainingTime >= 0) {
                            ShowLoading();

                            UpdateDeviceRemainingTimeNotification(
                                deviceId,
                                newRemainingTime
                            ).then((result) => {
                                if (!result.success) {
                                    showToast(result.message, "error");
                                } else {
                                    showToast(result.message, "success");
                                }
                                HideLoading();
                            });
                        } else {
                            showToast(
                                "Please enter a valid base time and rate greater than 0.",
                                "error"
                            );
                        }
                    }
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Device specific Monthly/Daily Rate and Usage Reports

    if (canvasDeviceLevelRateAndUsageList) {
        canvasDeviceLevelRateAndUsageList.forEach((item) => {
            if (item) {
                const deviceId = item.dataset.id;
                new DeviceRateAndUsageReportHandler(
                    deviceId,
                    item,
                    VIEWTYPE.Monthly,
                    DATA_INCLUSION_TYPE.BYDEVICE
                ).DoSession();
            }
        });
    }

    if (btnMonthlyDeviceLevel) {
        btnMonthlyDeviceLevel.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    ShowLoading();

                    if (canvasDeviceLevelRateAndUsageList) {
                        canvasDeviceLevelRateAndUsageList.forEach((item) => {
                            const deviceId = item.dataset.id;

                            if (item) {
                                new DeviceRateAndUsageReportHandler(
                                    deviceId,
                                    item,
                                    VIEWTYPE.Monthly,
                                    DATA_INCLUSION_TYPE.BYDEVICE
                                ).DoSession();
                            }
                        });
                    }

                    HideLoading();
                });
                item.classList.add("event-attached");
            }
        });
    }

    if (btnDailyDeviceLevel) {
        btnDailyDeviceLevel.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    ShowLoading();

                    if (canvasDeviceLevelRateAndUsageList) {
                        canvasDeviceLevelRateAndUsageList.forEach((item) => {
                            const deviceId = item.dataset.id;

                            if (item) {
                                new DeviceRateAndUsageReportHandler(
                                    deviceId,
                                    item,
                                    VIEWTYPE.Daily,
                                    DATA_INCLUSION_TYPE.BYDEVICE
                                ).DoSession();
                            }
                        });
                    }

                    HideLoading();
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Device Overview table

    if (divDeviceLevelOverview) {
        divDeviceLevelOverview.forEach((item) => {
            if (item) {
                const deviceId = item.dataset.id;
                const deviceArray = [deviceId];

                const grdDeviceLevelOverview =
                    getGrdDeviceLevelOverview(deviceId);

                if (grdDeviceLevelOverview) {
                    new DeviceOverviewTimeTransactionsReportHandler(
                        deviceArray,
                        grdDeviceLevelOverview
                    ).DoSession();
                }
            }
        });
    }

    //#endregion

    //#region Device Detailed table

    if (divDeviceLevelDetailed) {
        divDeviceLevelDetailed.forEach((item) => {
            if (item) {
                const deviceId = item.dataset.id;
                const deviceArray = [deviceId];

                const grdDeviceLevelDetailed =
                    getGrdDeviceLevelDetailed(deviceId);

                if (grdDeviceLevelDetailed) {
                    new DeviceDetailedTimeTransactionsReportHandler(
                        deviceArray,
                        grdDeviceLevelDetailed
                    ).DoSession();
                }
            }
        });
    }

    //#endregion

    //#region Overview report button

    if (btnDeviceLevelOverviewList) {
        btnDeviceLevelOverviewList.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    const deviceId = this.getAttribute("data-id");
                    const deviceArray = [deviceId];
                    const grdDeviceLevelOverview =
                        getGrdDeviceLevelOverview(deviceId);

                    if (grdDeviceLevelOverview) {
                        new DeviceOverviewTimeTransactionsReportHandler(
                            deviceArray,
                            grdDeviceLevelOverview
                        ).DoSession();

                        const divDeviceLevelDetailed =
                            getDivDeviceLevelDetailed(deviceId);
                        const divDeviceLevelOverview =
                            getDivDeviceLevelOverview(deviceId);
                        const btnDeviceLevelDetailed =
                            getBtnDeviceLevelDetailed(deviceId);

                        if (divDeviceLevelOverview) {
                            divDeviceLevelOverview.classList.add("!block");
                            divDeviceLevelOverview.classList.remove("!hidden");
                        }

                        if (divDeviceLevelDetailed) {
                            divDeviceLevelDetailed.classList.remove("!block");
                            divDeviceLevelDetailed.classList.add("!hidden");
                        }

                        this.classList.add("bg-blue-500", "text-white");
                        this.classList.remove("bg-gray-200", "text-gray-800");

                        if (btnDeviceLevelDetailed) {
                            btnDeviceLevelDetailed.classList.remove(
                                "bg-blue-500",
                                "text-white"
                            );
                            btnDeviceLevelDetailed.classList.add(
                                "bg-gray-200",
                                "text-gray-800"
                            );
                        }
                    }
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Detailed report button

    if (btnDeviceLevelDetailedList) {
        btnDeviceLevelDetailedList.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    const deviceId = this.getAttribute("data-id");
                    const deviceArray = [deviceId];
                    const grdDeviceLevelDetailed =
                        getGrdDeviceLevelDetailed(deviceId);

                    if (grdDeviceLevelDetailed) {
                        new DeviceDetailedTimeTransactionsReportHandler(
                            deviceArray,
                            grdDeviceLevelDetailed
                        ).DoSession();

                        const divDeviceLevelDetailed =
                            getDivDeviceLevelDetailed(deviceId);
                        const divDeviceLevelOverview =
                            getDivDeviceLevelOverview(deviceId);
                        const btnDeviceLevelOverview =
                            getBtnDeviceLevelOverview(deviceId);

                        if (divDeviceLevelDetailed) {
                            divDeviceLevelDetailed.classList.add("!block");
                            divDeviceLevelDetailed.classList.remove("!hidden");
                        }

                        if (divDeviceLevelOverview) {
                            divDeviceLevelOverview.classList.remove("!block");
                            divDeviceLevelOverview.classList.add("!hidden");
                        }

                        this.classList.add("bg-blue-500", "text-white");
                        this.classList.remove("bg-gray-200", "text-gray-800");

                        if (btnDeviceLevelOverview) {
                            btnDeviceLevelOverview.classList.remove(
                                "bg-blue-500",
                                "text-white"
                            );
                            btnDeviceLevelOverview.classList.add(
                                "bg-gray-200",
                                "text-gray-800"
                            );
                        }
                    }
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Update Emergency Passkey

    if (btnEmergencyPasskeyList) {
        btnEmergencyPasskeyList.forEach((item) => {
            if (!item.classList.contains("event-attached")) {
                item.addEventListener("click", function () {
                    const deviceId = this.getAttribute("data-id");
                    const txtEmergencyPass = getTxtEmergencyPass(deviceId);

                    if (txtEmergencyPass) {
                        const newEmergencyPassValue = txtEmergencyPass.value;

                        if (newEmergencyPassValue?.trim()) {
                            ShowLoading();

                            UpdateEmergencyPasskey(
                                deviceId,
                                newEmergencyPassValue
                            ).then((result) => {
                                if (!result.success) {
                                    showToast(result.message, "error");
                                } else {
                                    showToast(result.message, "success");
                                }
                                HideLoading();
                            });
                        } else {
                            showToast(
                                "Please enter a valid emergency passkey.",
                                "error"
                            );
                        }
                    }
                });
                item.classList.add("event-attached");
            }
        });
    }

    //#endregion

    //#region Functions

    function ShowUpdateDeviceNameControls(deviceId, show = false) {
        const divDeviceName = getEditDeviceNameSection(deviceId);
        const lblDeviceName = getLblDeviceName(deviceId);
        const btnSaveName = getBtnSaveName(deviceId);
        const txtDeviceName = getTxtDeviceName(deviceId);

        if (divDeviceName) {
            if (show) {
                divDeviceName.classList.remove("!hidden");
            } else {
                divDeviceName.classList.add("!hidden");
            }
        }

        if (lblDeviceName) {
            if (show) {
                lblDeviceName.style.display = "none";
            } else {
                lblDeviceName.style.display = "block";
            }
        }

        if (btnSaveName) {
            if (show) {
                btnSaveName.style.display = "none";
            } else {
                btnSaveName.style.display = "inline-block";
            }
        }

        if (txtDeviceName) {
            if (show) {
                txtDeviceName.focus();
            }
        }
    }

    //#endregion

    // function showReasonModal(reason) {
    //     const reasonMdl = document.getElementById('reasonContent');
    //     if (reasonMdl)
    //     {
    //         reasonMdl.textContent = reason;
    //         $('#reasonModal').modal('show');
    //     }
    // }

    // document.addEventListener("DOMContentLoaded", () => {

    //     const saveWatchdogIntervalButton = document.getElementById('saveWatchdogInterval');
    //     const txt_watchdogInterval = document.getElementById('txt_watchdogInterval');

    //     if (saveWatchdogIntervalButton) {
    //         saveWatchdogIntervalButton.addEventListener('click', function () {
    //             const newDeviceWDInterval = txt_watchdogInterval.value;

    //             if (newDeviceWDInterval > 0) {
    //                 ShowLoading();

    //                 setTimeout(() => {
    //                     fetch('/device/update/watchdog', {
    //                         method: 'POST',
    //                         headers: {
    //                             'Content-Type': 'application/json',
    //                             'X-CSRF-TOKEN': '{{ csrf_token() }}' // Ensure you have this token for Laravel
    //                         },
    //                         body: JSON.stringify({
    //                             deviceId: '{{ $device->DeviceID }}',
    //                             watchdogInterval: newDeviceWDInterval
    //                         })
    //                     })
    //                     .then(response => response.json())
    //                     .then(data => {
    //                         HideLoading(); // Hide loading after the request completes

    //                         if (data.success) {
    //                             showToast(data.message, 'success');
    //                         } else {
    //                             showToast(data.message || 'An error occurred.', 'error');
    //                         }
    //                     })
    //                     .catch(error => {
    //                         HideLoading(); // Hide loading in case of an error
    //                         console.log('Fetch error:', error);
    //                         showToast('An error occurred. Please try again.', 'error');
    //                     });
    //                 }, 2000);
    //             } else {
    //                 showToast('Please enter a valid interval greater than 0.');
    //             }
    //         });
    //     }
});
