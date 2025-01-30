class DeviceTimeControlOnLoadHandler
{
    constructor()
    {
        this.deviceId;
        this.deviceStatusElement;

        this.deviceStatusIdValue;
    }

    DoSession()
    {
        ShowLoading();
        this.UpdateNonRunningDevices();
        this.UpdateRunningDevices();
        this.UpdateNoHeartbeatDevices();
    }

    async UpdateNonRunningDevices()
    {
        const container = document.getElementById('device-cards-container');

        if (!container)
        {
            return;
        }

        const deviceCardList = container.querySelectorAll('[data-device-id]');
        let deviceIdList = [];
        
        if (!deviceCardList || deviceCardList.length === 0) {
            return;
        }
        
        deviceIdList = Array.from(deviceCardList).map(card => card.getAttribute('data-device-id'));

        if (!deviceIdList || deviceIdList.length === 0) {
            return;
        }


        deviceIdList.forEach(deviceId => {
            const deviceCard = document.querySelector(`[data-device-id="${deviceId}"]`);
            
            if (!deviceCard) 
            {
                return;
            }

            this.GetRequestData(deviceId);
            this.TimeControlsSetup();
        });
    }

    
    GetRequestData(deviceId)
    {
        /**
         * Derived values
         */
        this.deviceId = deviceId;
        
        /**
         * Elements/Components
         */
        this.deviceStatusElement = getDeviceStatusElement(this.deviceId);
        this.deviceStatusIdValue = getDeviceStatusIdValue(this.deviceId);
        this.deviceStartTimeCollectionElement = getDeviceStartTimeCollectionElement(this.deviceId);
    }


    async UpdateRunningDevices()
    {
        ShowLoading();

        GetRunningDevice()
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

    async UpdateNoHeartbeatDevices()
    {
        ShowLoading();

        GetDeviceHeartbeats()
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

    TimeControlsSetup()
    {
        const startTimeCollections = this.deviceStartTimeCollectionElement;

        if (this.deviceStatusIdValue == window.DeviceStatusEnum.INACTIVE)
        {
            if (startTimeCollections)
            {
                startTimeCollections.classList.remove('disabled'); 
            }
        }

    }
}