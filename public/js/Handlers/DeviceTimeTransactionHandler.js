let countDownInterval;

class DeviceTimeTransactionHandler {
    constructor(payload) {
        if (!payload) {
            throw new Error(
                "Payload is required to initialize DeviceTimeTransactionHandler."
            );
        }

        /**
         * Parameter values
         */
        this.deviceId = payload.DeviceID;
        this.startTime = new Date(payload.StartTime).getTime();
        this.pauseTime = new Date(payload.PauseTime).getTime();
        this.resumeTime = new Date(payload.ResumeTime).getTime();
        this.endTime = new Date(payload.EndTime).getTime();
        this.isOpenTime = payload.IsOpenTime;
        this.totalTime = payload.TotalTime;
        this.totalRate = payload.TotalRate;
        this.transactionType = payload.TransactionType;
        this.doHeartbeatCheck = payload.DoHeartbeatCheck ?? false;
        this.totalUsedTime = payload.TotalUsedTime ?? 0;

        /**
         * Elements/Components
         */
        this.deviceCardElement = getDeviceCardElement(this.deviceId);
        //this.deviceStatusElement = this.getDeviceStatusElement(this.deviceCardElement);
        this.deviceOpenTimeTimeElement = getOpenTimeTimeElement(this.deviceId);
        this.deviceOpenTimeRateElement = getOpenTimeRateElement(this.deviceId);
        this.deviceRemainingTimerElement = getRemainingTimerElement(
            this.deviceId
        );
        this.deviceBareTimerElement = getBareTimerElement(this.deviceId);
        this.deviceStartTimeCollectionElement =
            getDeviceStartTimeCollectionElement(this.deviceId);
        this.deviceStartTimeLblElement = getStartTimeLblElement(this.deviceId);
        this.deviceEndButtonElement = getDeviceEndButtonElement(this.deviceId);
        this.deviceEndTimeLblElement = getEndTimeLblElement(this.deviceId);
        this.deviceTotalTimeLblElement = getTotalTimeLblElement(this.deviceId);
        this.deviceTotalRateLblElement = getTotalRateLblElement(this.deviceId);
        this.deviceExtendButtonElement = getExtendButtonElement(this.deviceId);
        this.deviceExtendMenuButtonElement = getExtendMenuButtonElement(
            this.deviceId
        );
        this.devicePauseButtonElement = getPauseButtonElement(this.deviceId);
        this.deviceResumeButtonElement = getResumeButtonElement(this.deviceId);
        this.deviceSyncPopupElement = getDeviceSyncPopupElement(this.deviceId);
        this.deviceStatusRibbonElement = getDeviceStatusRibbonElement(
            this.deviceId
        );
        this.deviceNotificationBannerElement =
            getDeviceNotificationBannerElement(this.deviceId);

        /**
         * Derived values
         */
        this.deviceStatusIdValue = getDeviceStatusIdValue(this.deviceId);
        this.deviceRemTimeNotifValue = getRemainingTimeNotifValue(
            this.deviceCardElement
        );
        this.remainingTimeValue = this.DefineRemainingTime();
        // console.log("this.remainingTimeValue", this.remainingTimeValue);
    }

    DoHeartbeatCheck() {
        DisableTimeControls(this.deviceId);

        DeviceNetworkLoadingIcon(this.deviceId, true);
        GetDeviceHeartbeat(this.deviceId).then((result) => {
            if (!result.success) {
                showToast(
                    result.message ||
                        "Possible network error occurred. Please see logs for more info.",
                    "error"
                );
            } else {
                //If the operation is successful
                //let the websocket handle the changes
            }

            DeviceNetworkLoadingIcon(this.deviceId, false);
            HideLoading();
        });
    }

    //Starting point
    DoSession() {
        if (this.transactionType === window.TimeTransactionTypeEnum.START) {
            if (this.doHeartbeatCheck === true) {
                this.DoHeartbeatCheck();
            } else {
                UpdateDeviceCardStatusRibbon(this.deviceId, "Running", 2);
                UpdateTimeControlersForRunning(this.deviceId, this.isOpenTime);
                this.UpdateDeviceCardLabels(false, this.isOpenTime);
                this.StartTimer(this.isOpenTime);
            }
        }

        if (this.transactionType === window.TimeTransactionTypeEnum.STARTFREE) {
            if (this.doHeartbeatCheck === true) {
                this.DoHeartbeatCheck();
            } else {
                UpdateDeviceCardStatusRibbon(this.deviceId, "Start Free", 7);
                // UpdateTimeControlersForRunning(this.deviceId, this.isOpenTime);
                DisableTimeControls(this.deviceId);
                this.UpdateDeviceCardLabels(false, this.isOpenTime);
                this.StartTimer(this.isOpenTime);
            }
        }

        if (
            this.transactionType === window.TimeTransactionTypeEnum.END ||
            this.transactionType === window.TimeTransactionTypeEnum.ENDFREE
        ) {
            this.remainingTimeValue = 0;
            StopTimer(this.deviceId);
            this.ShowSyncingPopup(false);
            HideLoading();
            UpdateDeviceCardStatusRibbon(this.deviceId, "Inactive", 3);
            DisableTimeControls(this.deviceId);
            this.CheckAndNotifyRemainingTime(false);
            //Set delay before resetting the total
            //duration and rate
            setTimeout(() => {
                // this.UpdateDeviceCardLabels(true);
                UpdateTimeControlersForEnd(this.deviceId);
            }, 2000);

            if (this.doHeartbeatCheck === true) {
                this.DoHeartbeatCheck();
            }
        }

        if (this.transactionType === window.TimeTransactionTypeEnum.EXTEND) {
            if (this.doHeartbeatCheck === true) {
                this.DoHeartbeatCheck();
            } else {
                this.UpdateDeviceCardLabels(false);

                StopTimer(this.deviceId);

                this.StartTimer();
                this.ShowNotification("Extended time");
            }
        }

        if (this.transactionType === window.TimeTransactionTypeEnum.PAUSE) {
            if (this.doHeartbeatCheck === true) {
                this.DoHeartbeatCheck();
            } else {
                this.UpdateDeviceCardLabels(false, this.isOpenTime);
                UpdateDeviceCardStatusRibbon(this.deviceId, "Pause", 5);
                this.UpdateTimer();
                StopTimer(this.deviceId);
                this.CheckAndNotifyRemainingTime(false);
                UpdateTimeControlersForPause(this.deviceId);
            }
        }

        if (this.transactionType === window.TimeTransactionTypeEnum.RESUME) {
            if (this.doHeartbeatCheck === true) {
                this.DoHeartbeatCheck();
            } else {
                UpdateDeviceCardStatusRibbon(this.deviceId, "Running", 2);
                UpdateTimeControlersForRunning(this.deviceId, this.isOpenTime);
                this.UpdateDeviceCardLabels(false, this.isOpenTime);
                this.StartTimer(this.isOpenTime);
            }
        }
    }

    UpdateDeviceCardLabels(reset = false, isOpenTime = false) {
        const startTimeElement = this.deviceStartTimeLblElement;
        const endTimeElement = this.deviceEndTimeLblElement;
        const totalTimeElement = this.deviceTotalTimeLblElement;
        const totalRateElement = this.deviceTotalRateLblElement;
        const remainingTimeElement = this.deviceRemainingTimerElement;

        if (reset) {
            if (startTimeElement) {
                startTimeElement.textContent = `Start time: --:--:--`;
            }

            if (endTimeElement) {
                endTimeElement.textContent = `End time: --:--:--`;
            }

            if (totalTimeElement) {
                totalTimeElement.textContent = `Total time: 0 hr 0 mins`;
            }

            if (totalRateElement) {
                totalRateElement.textContent = `Total charge/rate: PHP 0.00`;
            }

            if (remainingTimeElement) {
                remainingTimeElement.textContent = `Time spent: 0h 0m 0s`;
                remainingTimeElement.classList.remove("!font-bold");
            }

            if (this.deviceCardElement) {
                this.deviceCardElement.classList.remove("!bg-amber-200");

                const content =
                    this.deviceCardElement.querySelector(".content");
                const extraContent =
                    this.deviceCardElement.querySelector(".extra");

                if (content) {
                    content.classList.remove("!bg-amber-200");
                }

                if (extraContent) {
                    extraContent.classList.remove("!bg-amber-200");
                }
            }
        } else {
            let startTime = this.startTime;
            let endTime = this.endTime;
            let totalTime = this.totalTime;
            let totalRate = this.totalRate;

            if (startTimeElement) {
                startTimeElement.textContent = `Start time: ${convertTo12HourFormat(
                    formatTime(new Date(startTime))
                )}`;
            }

            if (totalRateElement) {
                totalRateElement.textContent = `Total charge/rate: PHP ${Number(
                    totalRate
                ).toFixed(2)}`;
            }

            if (isOpenTime === false) {
                if (endTimeElement) {
                    endTimeElement.textContent = `End time: ${convertTo12HourFormat(
                        formatTime(new Date(endTime))
                    )}`;
                }

                if (totalTimeElement) {
                    totalTimeElement.textContent = `Total time: ${convertMinutesToHoursAndMinutes(
                        totalTime
                    )}`;
                }
            } else {
                if (endTimeElement) {
                    endTimeElement.textContent = `End time: ∞`;
                }

                if (totalTimeElement) {
                    totalTimeElement.textContent = `Total time: ∞`;
                }
            }
        }
    }

    ComputeRunningOpenTimeRate() {
        if (this.deviceTotalRateLblElement) {
            let remainingTimeInSeconds = this.remainingTimeValue / 60;
            let openTimeRunningRate = 0;

            if (remainingTimeInSeconds < this.deviceOpenTimeTimeElement) {
                openTimeRunningRate = this.deviceOpenTimeRateElement;
            } else {
                openTimeRunningRate =
                    (this.remainingTimeValue /
                        60 /
                        this.deviceOpenTimeTimeElement) *
                    this.deviceOpenTimeRateElement;
            }

            this.deviceTotalRateLblElement.textContent =
                "Total charge/rate: PHP " +
                parseFloat(openTimeRunningRate).toFixed(2);
        }
    }

    StartTimer(isOpenTime = false) {
        countDownInterval = setInterval(
            function () {
                if (this.remainingTimeValue > 0) {
                    if (!isOpenTime) {
                        this.remainingTimeValue--;
                        this.CheckAndNotifyRemainingTime(true);
                    } else {
                        this.remainingTimeValue++;
                        this.ComputeRunningOpenTimeRate();
                    }
                    this.UpdateTimer();
                } else {
                    StopTimer(this.deviceId);
                    DisableTimeControls(this.deviceId);
                    this.ShowSyncingPopup(true);
                }
            }.bind(this),
            1000
        );

        // const deviceStatusElement = getDeviceStatusElement(this.deviceId);
        // deviceStatusElement.setAttribute('timer-interval-id', countDownInterval);
        window.cache.intervalIds[this.deviceId] = countDownInterval;
    }

    CheckAndNotifyRemainingTime(show = false) {
        if (show) {
            if (this.deviceRemTimeNotifValue > 0) {
                if (
                    this.remainingTimeValue <=
                    this.deviceRemTimeNotifValue * 60
                ) {
                    const content =
                        this.deviceCardElement.querySelector(".content");
                    const extraContent =
                        this.deviceCardElement.querySelector(".extra");

                    if (content) {
                        content.classList.add("!bg-amber-200");
                    }

                    if (extraContent) {
                        extraContent.classList.add("!bg-amber-200");
                    }

                    this.deviceCardElement.classList.add("!bg-amber-200");
                    this.deviceRemainingTimerElement.classList.add(
                        "!font-bold"
                    );
                } else {
                    const content =
                        this.deviceCardElement.querySelector(".content");
                    const extraContent =
                        this.deviceCardElement.querySelector(".extra");

                    if (content) {
                        content.classList.remove("!bg-amber-200");
                    }

                    if (extraContent) {
                        extraContent.classList.remove("!bg-amber-200");
                    }

                    this.deviceCardElement.classList.remove("!bg-amber-200");
                    this.deviceRemainingTimerElement.classList.remove(
                        "!font-bold"
                    );
                }
            }
        } else {
            if (this.deviceCardElement) {
                this.deviceCardElement.classList.remove("!bg-amber-200");

                const content =
                    this.deviceCardElement.querySelector(".content");
                const extraContent =
                    this.deviceCardElement.querySelector(".extra");

                if (content) {
                    content.classList.remove("!bg-amber-200");
                }

                if (extraContent) {
                    extraContent.classList.remove("!bg-amber-200");
                }
            }

            if (this.deviceRemainingTimerElement) {
                this.deviceRemainingTimerElement.classList.remove("!font-bold");
            }
        }
    }

    UpdateTimer() {
        const hours = Math.floor(this.remainingTimeValue / 3600);
        const minutes = Math.floor((this.remainingTimeValue % 3600) / 60);
        const seconds = this.remainingTimeValue % 60;

        if (this.deviceRemainingTimerElement) {
            this.deviceRemainingTimerElement.textContent = `Time spent: ${hours}h ${minutes}m ${seconds}s`;
        }

        if (this.deviceBareTimerElement) {
            this.deviceBareTimerElement.textContent = `${this.remainingTimeValue}`;
        }
    }

    ShowSyncingPopup(showPopup) {
        if (this.deviceSyncPopupElement) {
            if (showPopup) {
                this.deviceSyncPopupElement.classList.remove("!hidden");
                this.deviceSyncPopupElement.classList.add("!flex");
                return;
            }

            this.deviceSyncPopupElement.classList.add("!hidden");
            this.deviceSyncPopupElement.classList.remove("!flex");
        }
    }

    ShowNotification(message) {
        const banner = this.deviceNotificationBannerElement;
        if (banner) {
            banner.textContent = message;
            banner.classList.add("show");
            setTimeout(() => {
                banner.classList.remove("show");
            }, 3000);
        }
    }

    DefineRemainingTime() {
        if (this.transactionType === window.TimeTransactionTypeEnum.PAUSE) {
            if (this.isOpenTime) {
                // return Math.floor((this.pauseTime - this.resumeTime) / 1000);
                // return Math.floor(((new Date(this.startTime + (this.totalUsedTime * 1000)))) / 1000);
                return this.totalUsedTime;
            } else {
                return Math.floor((this.endTime - this.pauseTime) / 1000);
            }
        }

        if (this.transactionType === window.TimeTransactionTypeEnum.RESUME) {
            if (this.isOpenTime) {
                console.log(this.resumeTime - this.totalUsedTime * 1000);
                return Math.floor(
                    (new Date(nowSynced() + this.totalUsedTime * 1000) -
                        this.resumeTime) /
                        1000
                );
            }
        }

        if (this.isOpenTime) {
            return Math.floor(
                (new Date(nowSynced() + 1000) - this.startTime) / 1000
            );
        } else {
            return Math.floor((this.endTime - nowSynced()) / 1000);
        }
    }
}
