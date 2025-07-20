function UpdateDeviceBaseRateTime(deviceId, newBaseTime, newBaseRate) {
    const route = `/device-time/base`;
    return makeRequest(route, "POST", {
        device_id: deviceId,
        base_time: newBaseTime,
        base_rate: newBaseRate,
    });
}

function UpdateDeviceOpenRateTime(deviceId, newOpenTime, newOpenRate) {
    const route = `/device-time/open`;
    return makeRequest(route, "POST", {
        device_id: deviceId,
        open_time: newOpenTime,
        open_rate: newOpenRate,
    });
}

function UpdateFreeLightTimeLimit(deviceId, newFreeLightTimeLimitValue) {
    const route = `/device-time/freetimelimit`;
    return makeRequest(route, "POST", {
        device_id: deviceId,
        free_light_time: newFreeLightTimeLimitValue,
    });
}

function UpdateDeviceIncrementStatus(deviceId, deviceTimeID, newStatus) {
    const route = `/device-time/increment/status/${deviceTimeID}`;
    return makeRequest(route, "POST", {
        device_id: deviceId,
        incrementStatus: newStatus,
    });
}

function UpdateDeviceRemainingTimeNotification(deviceId, newremainingTime) {
    const route = `/device/update/remainingtime`;
    return makeRequest(route, "POST", {
        device_id: deviceId,
        remainingTime: newremainingTime,
    });
}
