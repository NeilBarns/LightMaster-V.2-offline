function UpdateDeviceName(deviceId, newDeviceName) {
    const route = `/device/update/name`;
    return makeRequest(route, "POST", {
        external_device_id: deviceId,
        external_device_name: newDeviceName,
    });
}

function UpdateEmergencyPasskey(deviceId, newEmergencyPassValue) {
    const route = `/device/update/emergencypass`;
    return makeRequest(route, "POST", {
        device_id: deviceId,
        emergency_passkey: newEmergencyPassValue,
    });
}

//PLURAL
function GetDeviceHeartbeats() {
    const route = `/device/update/heartbeats`;
    return makeRequest(route);
}

//SINGULAR
function GetDeviceHeartbeat(deviceId) {
    const route = `/device/update/heartbeat/${deviceId}`;
    return makeRequest(route);
}
