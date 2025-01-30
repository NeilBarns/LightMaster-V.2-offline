//#region Element Getters 

function getDeviceCardElement(deviceId) {
    const deviceCard = document.querySelector(`[data-device-id="${deviceId}"]`);

    if (deviceCard) {
        return deviceCard;
    }

    return null;
}

function getRemainingTimeNotifValue(deviceCard) {
    if (deviceCard) {
        let deviceRemTimeNotif = deviceCard.getAttribute('data-remaining-time-notification');

        if (deviceRemTimeNotif === null || deviceRemTimeNotif < 1) {
            return 0;
        }

        return deviceRemTimeNotif;
    }

    return null;
}

function getOpenTimeElement(deviceId) {
    const openTimeElement = document.querySelector(`.btnOpenTime[data-id="${deviceId}"]`);

    if (openTimeElement) {
        return openTimeElement;
    }

    return null;
}

function getOpenTimeTimeElement(deviceId) {
    const openTimeElement = getOpenTimeElement(deviceId);

    if (openTimeElement) {
        const openTime = openTimeElement.getAttribute('data-value');
        return openTime;
    }

    return null;
}

function getOpenTimeRateElement(deviceId) {
    const openTimeElement = getOpenTimeElement(deviceId);

    if (openTimeElement) {
        const openRate = openTimeElement.getAttribute('data-rate');
        return openRate;
    }

    return null;
}

function getRemainingTimerElement(deviceId) {
    const timerElement = document.getElementById(`lblRemainingTime-${deviceId}`);

    if (timerElement) {
        return timerElement;
    }

    return null;
}

function getBareTimerElement(deviceId) {
    const bareTimerElement = document.getElementById(`lblBareRemainingTime-${deviceId}`);

    if (bareTimerElement) {
        return bareTimerElement;
    }

    return null;
}

function getTotalRateLblElement(deviceId) {
    const totalRateElement = document.getElementById(`lblTotalRate-${deviceId}`);

    if (totalRateElement) {
        return totalRateElement;
    }

    return null;
}

function getTotalTimeLblElement(deviceId) {
    const totalTimeElement = document.getElementById(`lblTotalTime-${deviceId}`);

    if (totalTimeElement) {
        return totalTimeElement;
    }

    return null;
}

function getEndTimeLblElement(deviceId) {
    const endTimeElement = document.getElementById(`lblEndTime-${deviceId}`);

    if (endTimeElement) {
        return endTimeElement;
    }

    return null;
}

function getStartTimeLblElement(deviceId) {
    const startTimeElement = document.getElementById(`lblStartTime-${deviceId}`);

    if (startTimeElement) {
        return startTimeElement;
    }

    return null;
}

function getDeviceSyncPopupElement(deviceId) {
    const deviceSync = document.getElementById(`device-sync-${deviceId}`);

    if (deviceSync) {
        return deviceSync;
    }

    return null;
}


function getDeviceStartTimeCollectionElement(deviceId) {
    const startTimeCollections = document.querySelector(`.startItems[data-id="${deviceId}"]`);

    if (startTimeCollections) {
        return startTimeCollections;
    }

    return null;
}

function getDeviceEndButtonElement(deviceId) {
    const btnEndButton = document.querySelector(`.btnEndTime[data-id="${deviceId}"]`);

    if (btnEndButton) {
        return btnEndButton;
    }

    return null;
}

function getPauseButtonElement(deviceId) {
    const pauseButton = document.querySelector(`.pause-time-button[data-id="${deviceId}"]`);

    if (pauseButton) {
        return pauseButton;
    }

    return null;
}

function getExtendButtonElement(deviceId) {
    const extendButton = document.querySelector(`.extend-time-single-button[data-id="${deviceId}"]`);

    if (extendButton) {
        return extendButton;
    }

    return null;
}

function getExtendMenuButtonElement(deviceId) {
    const extendMenuButton = document.querySelector(`.extend-items-dropdown[data-id="${deviceId}"]`);

    if (extendMenuButton) {
        return extendMenuButton;
    }

    return null;
}

function getResumeButtonElement(deviceId) {
    const resumeButton = document.querySelector(`.resume-time-button[data-id="${deviceId}"]`);

    if (resumeButton) {
        return resumeButton;
    }

    return null;
}

function getDeviceNotificationBannerElement(deviceId) {
    const banner = document.getElementById(`notification-banner-${deviceId}`);

    if (banner) {
        return banner;
    }

    return null;
}

function getDeviceStatusRibbonElement(deviceId) {
    const statusRibbon = document.getElementById(`device-status-${deviceId}`);

    if (statusRibbon) {
        return statusRibbon;
    }

    return null;
}

function getDeviceStatusElement(deviceId) {
    const deviceStatusElement = document.querySelector(`#device-status[data-id="${deviceId}"]`);

    if (deviceStatusElement) {
        return deviceStatusElement;
    }

    return null;
}

function getDeviceStatusIdValue(deviceId) {
    const deviceStatusElement = getDeviceStatusElement(deviceId);
    let statusId = null;

    if (deviceStatusElement) {

        statusId = deviceStatusElement.getAttribute('data-device-status-id');
        return statusId;
    }

    return null;
}

function getEditDeviceNameSection(deviceId) {
    const divDeviceNameList = document.querySelector(`.divDeviceName[data-id="${deviceId}"]`);
    
    if (divDeviceNameList) 
    {
        return divDeviceNameList;
    }

    return null;
}

function getLblDeviceName(deviceId) {
    const lblDeviceName = document.querySelector(`.lblDeviceName[data-id="${deviceId}"]`);
    
    if (lblDeviceName) 
    {
        return lblDeviceName;
    }

    return null;
}

function getBtnSaveName(deviceId) {
    const btnSaveName = document.querySelector(`.btnSaveName[data-id="${deviceId}"]`);
    
    if (btnSaveName) 
    {
        return btnSaveName;
    }

    return null;
}

function getTxtDeviceName(deviceId) {
    const txtDeviceName = document.querySelector(`.txtDeviceName[data-id="${deviceId}"]`);
    
    if (txtDeviceName) 
    {
        return txtDeviceName;
    }

    return null;
}

function getTxtBaseTime(deviceId) {
    const txtBaseTime = document.querySelector(`.txtBaseTime[data-id="${deviceId}"]`);
    
    if (txtBaseTime) 
    {
        return txtBaseTime;
    }

    return null;
}

function getTxtBaseRate(deviceId) {
    const txtBaseRate = document.querySelector(`.txtBaseRate[data-id="${deviceId}"]`);
    
    if (txtBaseRate) 
    {
        return txtBaseRate;
    }

    return null;
}

function getTxtOpenTime(deviceId) {
    const txtOpenTime = document.querySelector(`.txtOpenTime[data-id="${deviceId}"]`);
    
    if (txtOpenTime) 
    {
        return txtOpenTime;
    }

    return null;
}

function getTxtOpenRate(deviceId) {
    const txtOpenRate = document.querySelector(`.txtOpenRate[data-id="${deviceId}"]`);
    
    if (txtOpenRate) 
    {
        return txtOpenRate;
    }

    return null;
}

function getTxtFreeTimeLimit(deviceId) {
    const txtFreeTimeLimit = document.querySelector(`.txtFreeTimeLimit[data-id="${deviceId}"]`);
    
    if (txtFreeTimeLimit) 
    {
        return txtFreeTimeLimit;
    }

    return null;
}

function getBannerIncrementStatus(deviceTimeId) {
    const bannerIncrementStatus = document.querySelector(`.bannerIncrementStatus[data-id="${deviceTimeId}"]`);
    
    if (bannerIncrementStatus) 
    {
        return bannerIncrementStatus;
    }

    return null;
}

function getImageIncrementStatus(deviceTimeId) {
    const imageIncrementStatus = document.querySelector(`img[data-id="${deviceTimeId}"]`);
    
    if (imageIncrementStatus) 
    {
        return imageIncrementStatus;
    }

    return null;
}

function getTxtRemainingTime(deviceId) {
    const txtRemainingTime = document.querySelector(`.txtRemainingTime[data-id="${deviceId}"]`);
    
    if (txtRemainingTime) 
    {
        return txtRemainingTime;
    }

    return null;
}

function getcanvasDeviceLevelRateAndUsage(deviceId) {
    const canvasDeviceLevelRateAndUsage = document.querySelector(`.canvasDeviceLevelRateAndUsage[data-id="${deviceId}"]`)
    
    if (canvasDeviceLevelRateAndUsage) 
    {
        return canvasDeviceLevelRateAndUsage;
    }

    return null;
}

function getBtnMonthlyDeviceLevel(deviceId) {
    const btnMonthlyDeviceLevel = document.querySelector(`.btnMonthlyDeviceLevel[data-id="${deviceId}"]`);
    
    if (btnMonthlyDeviceLevel) 
    {
        return btnMonthlyDeviceLevel;
    }

    return null;
}

function getBtnDailyDeviceLevel(deviceId) {
    const btnDailyDeviceLevel = document.querySelector(`.btnDailyDeviceLevel[data-id="${deviceId}"]`);
    
    if (btnDailyDeviceLevel) 
    {
        return btnDailyDeviceLevel;
    }

    return null;
}

function getGrdDeviceLevelOverview(deviceId) {
    const grdDeviceLevelOverview = document.querySelector(`.grdDeviceLevelOverview[data-id="${deviceId}"]`);
    
    if (grdDeviceLevelOverview) 
    {
        return grdDeviceLevelOverview;
    }

    return null;
}

function getGrdDeviceLevelDetailed(deviceId) {
    const grdDeviceLevelDetailed = document.querySelector(`.grdDeviceLevelDetailed[data-id="${deviceId}"]`);
    
    if (grdDeviceLevelDetailed) 
    {
        return grdDeviceLevelDetailed;
    }

    return null;
}

function getGrdSessionDetails() {
    const grdSessionDetails = document.querySelector(`.grdSessionDetails`);
    
    if (grdSessionDetails) 
    {
        return grdSessionDetails;
    }

    return null;
}

function getDivDeviceLevelOverview(deviceId) {
    const divDeviceLevelOverview = document.querySelector(`.divDeviceLevelOverview[data-id="${deviceId}"]`);
    
    if (divDeviceLevelOverview) 
    {
        return divDeviceLevelOverview;
    }

    return null;
}

function getDivDeviceLevelDetailed(deviceId) {
    const divDeviceLevelDetailed = document.querySelector(`.divDeviceLevelDetailed[data-id="${deviceId}"]`);
    
    if (divDeviceLevelDetailed) 
    {
        return divDeviceLevelDetailed;
    }

    return null;
}

function getBtnDeviceLevelOverview(deviceId) {
    const btnDeviceLevelOverview = document.querySelector(`.btnDeviceLevelOverview[data-id="${deviceId}"]`);
    
    if (btnDeviceLevelOverview) 
    {
        return btnDeviceLevelOverview;
    }

    return null;
}

function getBtnDeviceLevelDetailed(deviceId) {
    const btnDeviceLevelDetailed = document.querySelector(`.btnDeviceLevelDetailed[data-id="${deviceId}"]`);
    
    if (btnDeviceLevelDetailed) 
    {
        return btnDeviceLevelDetailed;
    }

    return null;
}

function getGrdFinanceLevelOverview() {
    const grdFinanceLevelOverview = document.querySelector(`.grdFinanceLevelOverview`);
    
    if (grdFinanceLevelOverview) 
    {
        return grdFinanceLevelOverview;
    }

    return null;
}

function getGrdFinanceLevelOverviewExport() {
    const grdFinanceLevelOverviewExport = document.querySelector(`#grdFinanceLevelOverviewExport`);
    
    if (grdFinanceLevelOverviewExport) 
    {
        return grdFinanceLevelOverviewExport;
    }

    return null;
}

function getGrdFinanceLevelDetailed() {
    const grdFinanceLevelDetailed = document.querySelector(`#grdFinanceLevelDetailed`);
    
    if (grdFinanceLevelDetailed) 
    {
        return grdFinanceLevelDetailed;
    }

    return null;
}

function getGrdFinanceLevelDetailedExport() {
    const grdFinanceLevelDetailedExport = document.querySelector(`.grdFinanceLevelDetailedExport`);
    
    if (grdFinanceLevelDetailedExport) 
    {
        return grdFinanceLevelDetailedExport;
    }

    return null;
}

function getDateStartDateOverview() {
    const dateStartDateOverview = document.querySelector(`#dateStartDateOverview`);
    
    if (dateStartDateOverview) 
    {
        return dateStartDateOverview;
    }

    return null;
}

function getDateEndDateOverview() {
    const dateEndDateOverview = document.querySelector(`#dateEndDateOverview`);
    
    if (dateEndDateOverview) 
    {
        return dateEndDateOverview;
    }

    return null;
}

function getDdlDeviceOverview() {
    const ddlDeviceOverview = document.querySelector(`#ddlDeviceOverview`);
    
    if (ddlDeviceOverview) 
    {
        return ddlDeviceOverview;
    }

    return null;
}

function getDateStartDateDetailed() {
    const dateStartDateDetailed = document.querySelector(`#dateStartDateDetailed`);
    
    if (dateStartDateDetailed) 
    {
        return dateStartDateDetailed;
    }

    return null;
}

function getDateEndDateDetailed() {
    const dateEndDateDetailed = document.querySelector(`#dateEndDateDetailed`);
    
    if (dateEndDateDetailed) 
    {
        return dateEndDateDetailed;
    }

    return null;
}

function getDdlDeviceDetailed() {
    const ddlDeviceDetailed = document.querySelector(`#ddlDeviceDetailed`);
    
    if (ddlDeviceDetailed) 
    {
        return ddlDeviceDetailed;
    }

    return null;
}

function getBtnFinanceLevelOverview() {
    const btnFinanceLevelOverview = document.querySelector(`.btnFinanceLevelOverview`);
    
    if (btnFinanceLevelOverview) 
    {
        return btnFinanceLevelOverview;
    }

    return null;
}

function getBtnFinanceLevelDetailed() {
    const btnFinanceLevelDetailed = document.querySelector(`.btnFinanceLevelDetailed`);
    
    if (btnFinanceLevelDetailed) 
    {
        return btnFinanceLevelDetailed;
    }

    return null;
}

function getDivFinanceLevelOverview() {
    const divFinanceLevelOverview = document.querySelector(`.divFinanceLevelOverview`);
    
    if (divFinanceLevelOverview) 
    {
        return divFinanceLevelOverview;
    }

    return null;
}

function getDivFinanceLevelDetailed() {
    const divFinanceLevelDetailed = document.querySelector(`.divFinanceLevelDetailed`);
    
    if (divFinanceLevelDetailed) 
    {
        return divFinanceLevelDetailed;
    }

    return null;
}

function getIconDevicePendingNetwork(deviceId) {
    const iconDevicePendingNetwork = document.querySelector(`.iconDevicePendingNetwork[data-id="${deviceId}"]`);
    
    if (iconDevicePendingNetwork) 
    {
        return iconDevicePendingNetwork;
    }

    return null;
}

function getIconDeviceOnline(deviceId) {
    const iconDeviceOnline = document.querySelector(`.iconDeviceOnline[data-id="${deviceId}"]`);
    
    if (iconDeviceOnline) 
    {
        return iconDeviceOnline;
    }

    return null;
}

function getIconDeviceOffline(deviceId) {
    const iconDeviceOffline = document.querySelector(`.iconDeviceOffline[data-id="${deviceId}"]`);
    
    if (iconDeviceOffline) 
    {
        return iconDeviceOffline;
    }

    return null;
}

function getIconDeviceLoading(deviceId) {
    const iconDeviceLoading = document.querySelector(`.iconDeviceLoading[data-id="${deviceId}"]`);
    
    if (iconDeviceLoading) 
    {
        return iconDeviceLoading;
    }

    return null;
}

function getNotificationList() {
    const notificationList = document.querySelector(`.notification-list`);
    
    if (notificationList) 
    {
        return notificationList;
    }

    return null;
}

function getBtnShowNotificationButton() {
    const btnShowNotificationButton = document.querySelector(`.btnShowNotificationButton`);
    
    if (btnShowNotificationButton) 
    {
        return btnShowNotificationButton;
    }

    return null;
}

function getBtnShowHasNotificationButton() {
    const btnShowHasNotificationButton = document.querySelector(`.btnShowHasNotificationButton`);
    
    if (btnShowHasNotificationButton) 
    {
        return btnShowHasNotificationButton;
    }

    return null;
}

function getBtnCloseNotificationButton() {
    const btnCloseNotificationButton = document.querySelector(`.btnCloseNotificationButton`);
    
    if (btnCloseNotificationButton) 
    {
        return btnCloseNotificationButton;
    }

    return null;
}

function getNotificationButton() {
    const notificationButton = document.getElementById('notification-button');

    if (notificationButton)
    {
        return notificationButton;
    }

    return null;
}

function getNotificationImage() {
    const notificationButton = getNotificationButton();

    if (notificationButton)
    {
        const notificationImage = notificationButton.querySelector('.btnShowNotificationButton');
    
        if (notificationImage) 
        {
            return notificationImage;
        }
    }

    return null;
}

function getNotificationSound()
{
    const notificationSound = document.getElementById('notificationSound');

    if (notificationSound)
    {
        return notificationSound;
    }

    return null;
}

//#endregion

function ShowNewNotification() {
    const notificationImage = getNotificationImage();

    if (notificationImage)
    {
        const hasNewNotif = notificationImage.dataset.hasNewNotif; 
        notificationImage.src = hasNewNotif; 
    }
}

function ShowNoNewNotification() {
    const notificationImage = getNotificationImage();

    if (notificationImage)
    {
        const noNewNotif = notificationImage.dataset.noNewNotif; 
        notificationImage.src = noNewNotif;    
    }
}

function UpdateDeviceCardStatusRibbon(deviceId, newStatus, statusId) {
    const deviceStatusElement = getDeviceStatusElement(deviceId);

    if (deviceStatusElement) {
        const statusClasses = {
            'Pending Configuration': 'grey',
            'Running': 'green',
            'Inactive': 'yellow',
            'Disabled': 'red',
            'Start Free': 'orange'
        };

        deviceStatusElement.className = `ui ${statusClasses[newStatus]} ribbon label`;
        deviceStatusElement.textContent = newStatus;
        deviceStatusElement.setAttribute('data-device-status-id', statusId);
    }
}

function GetTimerIntervalId(deviceId)
{
    let intervalId = 0;
    const deviceStatusElement = getDeviceStatusElement(deviceId);

    if (deviceStatusElement)
    {
        intervalId = deviceStatusElement.getAttribute('timer-interval-id');

        if (intervalId)
        {
            return intervalId;
        }
    }

    return 0;
}

function StopTimer(deviceId) {
    const intervalId = window.cache.intervalIds[deviceId];
    if (intervalId) {
        clearInterval(intervalId);
        delete window.cache.intervalIds[deviceId];
        console.log(`Timer stopped for device ${deviceId}`);
    } else {
        console.log(`No active timer found for device ${deviceId}`);
    }
}

function DisableTimeControls(deviceId)
{
    const btnEndButton = getDeviceEndButtonElement(deviceId);
    const startTimeCollections = getDeviceStartTimeCollectionElement(deviceId);
    const extendButtonMenu = getExtendMenuButtonElement(deviceId);
    const extendButton = getExtendButtonElement(deviceId);
    const pauseButton = getPauseButtonElement(deviceId);
    const resumeButton = getResumeButtonElement(deviceId);

    if (btnEndButton)
    {
        btnEndButton.classList.add('disabled');
    }

    if (startTimeCollections)
    {
        startTimeCollections.classList.add('disabled');
    }

    if (extendButtonMenu) 
    {
        extendButtonMenu.classList.add('disabled');
    }

    if (extendButton) 
    {    
        extendButton.classList.add('disabled');
    }
    
    if (pauseButton) 
    {
        pauseButton.classList.add('disabled');
    }

    if (resumeButton) 
    {
        resumeButton.classList.add('disabled');
    }
}

function UpdateTimeControlersForEnd(deviceId)
{
    const btnEndButton = getDeviceEndButtonElement(deviceId);
    const startTimeCollections = getDeviceStartTimeCollectionElement(deviceId);
    const extendButtonMenu = getExtendMenuButtonElement(deviceId);
    const extendButton = getExtendButtonElement(deviceId);
    const pauseButton = getPauseButtonElement(deviceId);
    const resumeButton = getResumeButtonElement(deviceId);

    if (btnEndButton) {
        btnEndButton.classList.add('!hidden');
        btnEndButton.classList.remove('!block');
    }
    
    if (startTimeCollections) {
        startTimeCollections.classList.remove('!hidden');
        startTimeCollections.classList.add('!block');
        startTimeCollections.classList.remove('disabled');
    }
    
    if (extendButtonMenu) {
        extendButtonMenu.classList.add('disabled');
    }
    
    if (extendButton) {
        extendButton.classList.add('disabled');
    }
    
    if (pauseButton) {
        pauseButton.classList.remove('!hidden');
        pauseButton.classList.add('!block');
        pauseButton.classList.add('disabled');
    }

    if (resumeButton)
    {
        resumeButton.classList.add('!hidden');
        resumeButton.classList.remove('!block');
    }
}

function UpdateTimeControlersForRunning(deviceId, isOpenTime)
{
    const btnEndButton = getDeviceEndButtonElement(deviceId);
    const startTimeCollections = getDeviceStartTimeCollectionElement(deviceId);
    const extendButtonMenu = getExtendMenuButtonElement(deviceId);
    const extendButton = getExtendButtonElement(deviceId);
    const pauseButton = getPauseButtonElement(deviceId);
    const resumeButton = getResumeButtonElement(deviceId);

    if (btnEndButton) {
        btnEndButton.classList.remove('!hidden');
        btnEndButton.classList.add('!block');
        btnEndButton.classList.remove('disabled');
    }
    
    if (startTimeCollections) {
        startTimeCollections.classList.add('!hidden');
        startTimeCollections.classList.remove('!block');
    }
    
    if (extendButtonMenu) {
        if (!isOpenTime) {
            extendButtonMenu.classList.remove('disabled');
        }
    }
    
    if (extendButton) {
        if (!isOpenTime) {
            extendButton.classList.remove('disabled');
        }
    }
    
    if (pauseButton) {
        if (!isOpenTime) {
            pauseButton.classList.remove('!hidden');
            pauseButton.classList.add('!block');
            pauseButton.classList.remove('disabled');
        }
        else
        {
            pauseButton.classList.remove('!hidden');
            pauseButton.classList.add('!block');
            pauseButton.classList.add('disabled');
        }
    }

    if (resumeButton)
    {
        resumeButton.classList.add('!hidden');
        resumeButton.classList.remove('!block');
    }
}   

function UpdateTimeControlersForPause(deviceId)
{
    const btnEndButton = getDeviceEndButtonElement(deviceId);
    const startTimeCollections = getDeviceStartTimeCollectionElement(deviceId);
    const extendButtonMenu = getExtendMenuButtonElement(deviceId);
    const extendButton = getExtendButtonElement(deviceId);
    const pauseButton = getPauseButtonElement(deviceId);
    const resumeButton = getResumeButtonElement(deviceId);

    if (startTimeCollections) {
        startTimeCollections.classList.add('!hidden');
        startTimeCollections.classList.remove('!block');
    }
    
    if (btnEndButton) {
        btnEndButton.classList.remove('!hidden');
        btnEndButton.classList.add('!block');
        btnEndButton.classList.remove('disabled');
    }
    
    if (extendButtonMenu) {
        extendButtonMenu.classList.add('disabled');
    }
    
    if (extendButton) {
        extendButton.classList.add('disabled');
    }

    if (pauseButton) 
    {
        pauseButton.classList.add('!hidden');
        pauseButton.classList.remove('!block');
    }

    if (resumeButton)
    {
        resumeButton.classList.remove('!hidden');
        resumeButton.classList.add('!block');
        resumeButton.classList.remove('disabled');
    }
}

function DeviceNetworkLoadingIcon(deviceId, show)
{
    const iconDeviceLoading = getIconDeviceLoading(deviceId);
    const iconDevicePendingNetwork = getIconDevicePendingNetwork(deviceId);
    const iconDeviceOnline = getIconDeviceOnline(deviceId);
    const iconDeviceOffline = getIconDeviceOffline(deviceId);

    if (iconDeviceLoading)
    {
        if (show)
        {
            if (iconDevicePendingNetwork && iconDeviceOffline
                && iconDeviceOffline)
            {
                iconDevicePendingNetwork.classList.add('!hidden');
                iconDevicePendingNetwork.classList.remove('!block');
        
                iconDeviceOnline.classList.add('!hidden');
                iconDeviceOnline.classList.remove('!block');
        
                iconDeviceOffline.classList.add('!hidden');
                iconDeviceOffline.classList.remove('!block');
            }

            iconDeviceLoading.classList.add('!block');
            iconDeviceLoading.classList.remove('!hidden');
        }
        else 
        {
            iconDeviceLoading.classList.remove('!block');
            iconDeviceLoading.classList.add('!hidden');
        }
    }
}