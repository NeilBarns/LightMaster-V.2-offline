
class NotificationHandler {
    
    constructor(payload)
    {
        if (!payload)
        {
            throw new Error("Payload is required to initialize DeviceTimeTransactionHandler.");
        }

        /**
         * Parameter values
         */
        this.notificationID = payload.NotificationID;
        this.deviceId = payload.DeviceID;
        this.notificationLevelID = payload.NotificationLevelID;
        this.notificationSourceID = payload.NotificationSourceID;
        this.notification = payload.Notification;
        this.createdDate = payload.CreatedDate;
        this.notificationList = getNotificationList();
    }

    DoSession()
    {
        this.CreateNotificationCard();
        this.UpdateNotificationButton();
        this.PlayNotificationSound();
    }

    CreateNotificationCard()
    {
        const notificationCard = `
        <div data-id="${this.notificationID}" class="relative flex items-center ${this.GetCardColor(this.notificationLevelID)} p-4 rounded-lg shadow-md space-x-4 mb-5 cursor-pointer border-l-4 border-l-blue-500">
            <div class="flex items-center justify-center w-12 h-12 text-white">
                <img src="/imgs/nodes.png" alt="Bell icon" class="w-9 h-9 !block">
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900 break-words">
                    ${this.notification ?? 'Notification text here'}
                </p>
                <p class="text-sm text-gray-500">
                    ${this.FormatDate(this.createdDate)}
                </p>
            </div>
        </div>`;

        this.notificationList.insertAdjacentHTML('afterbegin', notificationCard);
    }

    UpdateNotificationButton()
    {
        const notificationImage = getNotificationImage();
        if (notificationImage)
        {
            ShowNewNotification();
        }
    }

    PlayNotificationSound()
    {
        const notificationSound = getNotificationSound();

        if (notificationSound)
        {
            notificationSound.play();
        }
    }

    GetCardColor(levelID) {
        switch (levelID) {
            case 1: return 'bg-white-100'; // NORMAL
            case 2: return 'bg-yellow-100'; // WARNING
            case 3: return 'bg-red-300'; // ERROR
            default: return 'bg-white-100'; // Default
        }
    }

    FormatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
}