class DeviceHeartbeatHandler {

    constructor(payload)
    {
        if (!payload)
        {
            throw new Error("Payload is required to initialize DeviceTimeTransactionHandler.");
        }
        
         /**
         * Parameter values
         */
         this.deviceId = payload.DeviceID;
         this.heartbeatStatus = payload.HeartbeatStatus;
         this.deviceStatus = payload.DeviceStatus;

         /**
         * Elements/Components
         */
         this.iconDevicePendingNetwork = getIconDevicePendingNetwork(this.deviceId);
         this.iconDeviceOnline = getIconDeviceOnline(this.deviceId);
         this.iconDeviceOffline = getIconDeviceOffline(this.deviceId);
    }

    DoSession()
    {
        if (this.heartbeatStatus === window.DeviceHeartbeatStatusEnum.ONLINE)
        {
            this.UpdateDeviceHeartbeatStatus(true);
        }

        if (this.heartbeatStatus === window.DeviceHeartbeatStatusEnum.OFFLINE)
        {
            this.UpdateDeviceHeartbeatStatus(false);
        }
    }

    UpdateDeviceHeartbeatStatus(isOnline)
    {
        if (this.iconDevicePendingNetwork && this.iconDeviceOffline
            && this.iconDeviceOffline)
        {
            this.iconDevicePendingNetwork.classList.add('!hidden');
            this.iconDevicePendingNetwork.classList.remove('!block');

            if (isOnline)
            {
                this.iconDeviceOffline.classList.add('!hidden');
                this.iconDeviceOffline.classList.remove('!block');

                this.iconDeviceOnline.classList.remove('!hidden');
                this.iconDeviceOnline.classList.add('!block');
                
                if (this.deviceStatus == window.DeviceStatusEnum.INACTIVE)
                {
                    UpdateTimeControlersForEnd(this.deviceId);
                }

                if (this.deviceStatus == window.DeviceStatusEnum.PAUSE)
                {
                    UpdateTimeControlersForPause(this.deviceId);
                }
            }
            else 
            {
                this.iconDeviceOnline.classList.add('!hidden');
                this.iconDeviceOnline.classList.remove('!block');
    
                this.iconDeviceOffline.classList.remove('!hidden');
                this.iconDeviceOffline.classList.add('!block');

                DisableTimeControls(this.deviceId);
            }
        }
    }
}