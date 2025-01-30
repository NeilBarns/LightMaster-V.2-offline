
function GetRunningDevice() {
    const route = `/device-time/running`;
    return makeRequest(route);
}

function GetMonthlyReportsByDevice(deviceId) {
    const route = `/reports/usage/monthly/device/${deviceId}`;
    return makeRequest(route);
}

function GetMonthlyReports() {
    const route = `/reports/usage/monthly`;
    return makeRequest(route);
}

function GetDailyReportsByDevice(deviceId) {
    const route = `/reports/usage/daily/device/${deviceId}`;
    return makeRequest(route);
}

function GetOverviewTransactionsReportsByDevice(deviceIds, dateFrom, dateTo) {
    const route = `/reports/transactions/overview/device`;
    return makeRequest(route, 'POST', {
        dateFrom: dateFrom,
        dateTo: dateTo,
        deviceIds: deviceIds
    });
}

function GetDetailedTransactionsReportsByDevice(deviceIds, dateFrom, dateTo) {
    const route = `/reports/transactions/detailed/device`;
    return makeRequest(route, 'POST', {
        dateFrom: dateFrom,
        dateTo: dateTo,
        deviceIds: deviceIds
    });
}

function StartRatedDeviceTime(deviceId) {
    const route = `/device-time/start/rated/${deviceId}`;
    return makeRequest(route, 'POST', {});
}

function StartOpenDeviceTime(deviceId) {
    const route = `/device-time/start/open/${deviceId}`;
    return makeRequest(route, 'POST', {});
}

function EndDeviceTimeManual(deviceId) {
    const route = `/device-time/end/${deviceId}`;
    return makeRequest(route, 'POST');
}

function ExtendDeviceTime(deviceId, extendTime, extendTimeRate) {
    const route = `/device-time/extend/${deviceId}`;
    const body = { increment: extendTime, rate: extendTimeRate };
    return makeRequest(route, 'POST', body);
}

function PauseDeviceTime(deviceId) {
    const route = `/device-time/pause/${deviceId}`;
    return makeRequest(route, 'POST', {});
}

function ResumeDeviceTime(deviceId) {
    const route = `/device-time/resume/${deviceId}`;
    return makeRequest(route, 'POST', {});
}

function TestLight(deviceId) {
    const route = `/api/device/test/${deviceId}`;
    return makeRequest(route, 'POST', {});
}

function StartDeviceFreeLight(deviceId) {
    const route = `/device/startfree/${deviceId}`;
    return makeRequest(route, 'POST', {});
}

function StopDeviceFreeLight(deviceId) {
    const route = `/device/stopfree/${deviceId}`;
    return makeRequest(route, 'POST', {});
}

function DeployDevice(deviceId)
{
    const route = `/device/deploy/${deviceId}`;
    return makeRequest(route, 'POST', {});
}

function CancelDeviceExchange(deviceId)
{
    const route = `/device/cancel/exchange/${deviceId}`;
    return makeRequest(route, 'POST', {});
}