document.addEventListener('DOMContentLoaded', function () {
    
    const startButtonList = document.querySelectorAll('#startItems .item');
    const singleExtendButtonList = document.querySelectorAll('.extend-time-single-button');
    const extendButtonList = document.querySelectorAll('#extendItems .item');
    const endButtonList = document.querySelectorAll('.btnEndTime');
    const pauseButtonList = document.querySelectorAll('.pause-time-button');
    const resumeButtonList = document.querySelectorAll('.resume-time-button');
    const networkControlsList = document.querySelectorAll('.networkControls');

    if (networkControlsList)
    {
        networkControlsList.forEach(item => {
            if (!item.classList.contains('event-attached')) {
                item.addEventListener('click', function() {
    
                    const deviceId = this.getAttribute('data-id');
                    DeviceNetworkLoadingIcon(deviceId, true);
                    setTimeout(() => {
                        GetDeviceHeartbeat(deviceId)
                        .then((result) => {
                            console.log('GetDeviceHeartbeat', result);
                            if (!result.success) {
                                showToast(result.message || "Possible network error occurred. Please see logs for more info.", 'error');
                            } else {
                                //If the operation is successful
                                //let the websocket handle the changes
                            }
                            
                            DeviceNetworkLoadingIcon(deviceId, false);
                        });
                    }, 1500);
                });
                item.classList.add('event-attached');
            }
        });
    }

    if (resumeButtonList) 
    {
        resumeButtonList.forEach(item => {
            if (!item.classList.contains('event-attached')) {
                item.addEventListener('click', function() {
    
                    const deviceId = this.getAttribute('data-id');
    
                    ShowLoading();
    
                    ResumeDeviceTime(deviceId)
                        .then((result) => {
                            if (!result.success) {
                                showToast(result.message || "Possible network error occurred. Please see logs for more info.", 'error');
                            } else {
                                //If the operation is successful
                                //let the websocket handle the changes
                            }
    
                            HideLoading();
                        });
                });
                item.classList.add('event-attached');
            }
        });
    }

    if (pauseButtonList)
    {
        pauseButtonList.forEach(item => {
            if (!item.classList.contains('event-attached')) {
                item.addEventListener('click', function () {
                    
                    const deviceId = this.getAttribute('data-id');
                    
                    ShowLoading();

                    PauseDeviceTime(deviceId)
                    .then((result) => {
                        if (!result.success) {
                            showToast(result.message || "Possible network error occurred. Please see logs for more info.", 'error');
                        } else {
                            //If the operation is successful
                            //let the websocket handle the changes
                        }

                        HideLoading();
                    });
                });
                item.classList.add('event-attached');
            }
        });
    }

    

    if (endButtonList)
    {
        endButtonList.forEach(item => {
            if (!item.classList.contains('event-attached')) {
                item.addEventListener('click', function () {
                    const deviceId = this.getAttribute('data-id');
                    $(`#endTimeModal-${deviceId}`).modal('show');
                });
                item.classList.add('event-attached');
            }
        });
    }

    if (extendButtonList)
    {
        extendButtonList.forEach(item => {
            if (!item.classList.contains('event-attached')) {
                item.addEventListener('click', function () {
                    
                    const deviceId = this.closest('.dropdown').getAttribute('data-id');
                    const extendTime = this.getAttribute('data-value');
                    const extendTimeRate = this.getAttribute('data-rate');

                    ShowLoading();

                    ExtendDeviceTime(deviceId, extendTime, extendTimeRate)
                    .then((result) => {
                        if (!result.success) {
                            showToast(result.message || "Possible network error occurred. Please see logs for more info.", 'error');
                        } else {
                            //If the operation is successful
                            //let the websocket handle the changes
                        }

                        HideLoading();
                    });
                });
                item.classList.add('event-attached');
            }
        });
    }

    if (singleExtendButtonList) 
    {
        singleExtendButtonList.forEach(item => {
            if (!item.classList.contains('event-attached')) {
                item.addEventListener('click', function () {
                    const deviceId = this.getAttribute('data-id');
                    const extendTime = this.getAttribute('data-time');
                    const extendTimeRate = this.getAttribute('data-rate');

                    ShowLoading();

                    ExtendDeviceTime(deviceId, extendTime, extendTimeRate)
                    .then((result) => {
                        if (!result.success) {
                            showToast(result.message || "Possible network error occurred. Please see logs for more info.", 'error');
                        } else {
                            //If the operation is successful
                            //let the websocket handle the changes
                        }

                        HideLoading();
                    });
                });
                item.classList.add('event-attached');
            }
        });
    }

    if (startButtonList)
    {
        startButtonList.forEach(item => {
            if (!item.classList.contains('event-attached')) {
                
                item.addEventListener('click', function () {
                    
                    const deviceId = this.closest('.dropdown').getAttribute('data-id');
                    const timingType = this.getAttribute('data-type');
    
                    ShowLoading();
    
                    //Rated time
                    if (timingType === 'rated')
                    {
                        StartRatedDeviceTime(deviceId)
                        .then((result) => {    
                            if (!result.success) {
                                showToast(result.message || "Possible network error occurred. Please see logs for more info.", 'error');
                            } else {
                                //If the operation is successful
                                //let the websocket handle the changes
                            }
    
                            HideLoading();
                        });
                    }
                    //Open time
                    else 
                    {
                        StartOpenDeviceTime(deviceId)
                        .then((result) => {
                            if (!result.success) {
                                showToast(result.message || "Possible network error occurred. Please see logs for more info.", 'error');
                            } else {
                                //If the operation is successful
                                //let the websocket handle the changes
                            }

                            HideLoading();
                        });
                    }
                });
                item.classList.add('event-attached');
            }
        });
    }

    // Handle cancel end time
    $(document).on('click', '.cancel-button', function() {
        const deviceId = $(this).data('id');
        $(`#endTimeModal-${deviceId}`).modal('hide');
    });

    $(document).on('click', '.confirm-end-time-button', function () {
        const deviceId = $(this).data('id');
        $(`#endTimeModal-${deviceId}`).modal('hide');
        ShowLoading();

        EndDeviceTimeManual(deviceId)
        .then((result) => {
            if (!result.success) {
                showToast(result.message || "Possible network error occurred. Please see logs for more info.", 'error');
            } else {
                // let intervalId = GetTimerIntervalId(deviceId);
                // clearInterval(intervalId);
                StopTimer(deviceId);
                //If the operation is successful
                //let the websocket handle the changes
            }
    
            HideLoading();
        });
        
    });
});