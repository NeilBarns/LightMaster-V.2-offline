const ws = new WebSocket(WEBSOCKET_URL);
const messageQueue = [];

ws.onopen = () => {
    console.log('WebSocket connection established');
    while (messageQueue.length > 0) {
        const queuedMessage = messageQueue.shift();
        ws.send(queuedMessage);
        console.log('Queued message sent:', queuedMessage);
    }
};

ws.onmessage = function (event) {
    const data = JSON.parse(event.data);
    console.log('data', data);
    /*
    * Real time updates when a new device has
    * been added or removed/deleted
    */
    if (data.type === 'device.add.remove.updates')
    {
        location.reload();
    }

    /*
    * Real time updates for time
    * transactions
    */
    if (data.type === 'device.transaction.updates') {
        new DeviceTimeTransactionHandler(data.payload).DoSession();
    }

    /*
    * Real time updates for device
    * heartbeat
    */
    if (data.type === 'device.heartbeat.updates') {
        new DeviceHeartbeatHandler(data.payload).DoSession();
    }

    /*
    * Real time updates for notifications
    */
    if (data.type === 'device.notification.updates') {
        new NotificationHandler(data.payload).DoSession();
    }
};

ws.onclose = () => {
    console.log('WebSocket connection closed');
};

ws.onerror = (error) => {
    console.error('WebSocket error:', error);
};

function sendWebSocketMessage(type, payload) {
    const message = JSON.stringify({ type, payload });
    if (ws.readyState === WebSocket.OPEN) {
        ws.send(message);
        console.log('Message sent:', message);
    } else if (ws.readyState === WebSocket.CONNECTING) {
        console.log('WebSocket not open yet. Queuing message.');
        messageQueue.push(message);
    } else {
        console.error('WebSocket is not open. Cannot send message.');
    }
}

window.sendWebSocketMessage = sendWebSocketMessage;