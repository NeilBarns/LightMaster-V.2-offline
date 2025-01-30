function showToast(message, type = 'error', duration = 3000) {
    const toastContainer = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.classList.add('toast', type);
    toast.classList.add('!flex');
    toast.classList.add('flex-row');

    // toast success show  
    toast.innerHTML = '<div style="padding: 15px; display: flex; justify-content: center; align-items: center;"><i style="font-size: large;" class="exclamation triangle icon"></i></div><div style="display: flex;align-items: center;text-align: left;padding: 5px;">' + message + '</div>';

    toastContainer.appendChild(toast);

    // Show toast
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);

    // Remove toast
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 500);
    }, duration);
}

function ShowLoading() {
    const loadingScreen = document.getElementById('loadingScreen');
    loadingScreen.classList.remove('!hidden'); // Remove the '!hidden' class
    loadingScreen.classList.add('!block'); // Add the '!block' class
}

function HideLoading() {
    const loadingScreen = document.getElementById('loadingScreen');
    loadingScreen.classList.remove('!block'); // Remove the '!block' class
    loadingScreen.classList.add('!hidden'); // Add the '!hidden' class
}

function showSyncingPopup(deviceSyncPopupElement, showPopup) {
    if (showPopup) {
        deviceSyncPopupElement.classList.remove('!hidden');
        deviceSyncPopupElement.classList.add('!flex');
        return;
    }

    deviceSyncPopupElement.classList.add('!hidden');
    deviceSyncPopupElement.classList.remove('!flex');
}

function getCsrfToken()
{
    return csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}


function hideSessionDetailsModal() {
    $('#sessionDetailsModal').modal('hide');
}

