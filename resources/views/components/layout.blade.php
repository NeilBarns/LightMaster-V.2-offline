<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>{{ config('app.name') }}</title>
    {{-- <meta name="websocket-url" content="{{ config('app.websocket') }}"> --}}

    <link rel="icon" type="image/x-icon" href="{{ asset('imgs/lightmaster-icon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- JQuery -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>

    <!-- Fonts -->
    {{--
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet"> --}}

    {{--
    <link href="/css/app.css" rel="stylesheet"> --}}
    <script>
        window.appConfig = {
            websocketUrl: "{{ config('app.websocket_url') }}"
        };
    </script>
    <!-- UIkit CSS -->
    <link rel="stylesheet" href="{{ asset('css/uikit.min.css') }}" />

    <!-- UIkit JS -->
    <script defer src="{{ asset('js/uikit.min.js') }}"></script>
    <script defer src="{{ asset('js/uikit-icons.min.js') }}"></script>

    <!-- AG Grid -->
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script> --}}
    <link rel="stylesheet" href="{{ asset('css/ag-grid.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ag-theme-balham.css') }}">
    <script src="{{ asset('js/ag-grid-community.min.js') }}"></script>

    <!-- Chart.js  -->
    <script src="{{ asset('js/chart.js') }}"></script>

    

    <script src="{{ asset('js/functions/websocketHandler.js') }}" defer></script>

    <!-- Fomantic IU -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/semantic.min.css') }}">
    <script src="{{ asset('js/semantic.min.js') }}"></script>
   
    <script>
        const TimeTransactionTypeEnum = {};
        @foreach (App\Enums\TimeTransactionTypeEnum::cases() as $case)
            TimeTransactionTypeEnum['{{ $case['name'] }}'] = '{{ $case['value'] }}';
        @endforeach
        window.TimeTransactionTypeEnum = TimeTransactionTypeEnum;
    </script>
   
   <script>
        const DeviceStatusEnum = {};
        @foreach (App\Enums\DeviceStatusEnum::cases() as $case)
            DeviceStatusEnum['{{ $case['name'] }}'] = '{{ $case['value'] }}';
        @endforeach
        window.DeviceStatusEnum = DeviceStatusEnum;

        window.cache = {
            intervalIds: {}
        };
    </script>

    <script>
        const DeviceHeartbeatStatusEnum = {};
        @foreach (App\Enums\DeviceHeartbeatStatusEnum::cases() as $case)
            DeviceHeartbeatStatusEnum['{{ $case['name'] }}'] = '{{ $case['value'] }}';
        @endforeach
        window.DeviceHeartbeatStatusEnum = DeviceHeartbeatStatusEnum;
    </script>
    
    <script defer src="{{ asset('js/functions/LocalStorageFunctions.js') }}"></script>
    <script defer src="{{ asset('js/functions/Enums.js') }}"></script>
    <script defer src="{{ asset('js/functions/CommonFunctions.js') }}"></script>
    <script defer src="{{ asset('js/functions/DeviceElementFunctions.js') }}"></script>
    <script defer src="{{ asset('js/functions/DateTimeUtilFunctions.js') }}"></script>
    <script defer src="{{ asset('js/functions/NumberUtilFunctions.js') }}"></script>
    <script defer src="{{ asset('js/code_behinds/device-card.js') }}"></script>
    <script defer src="{{ asset('js/code_behinds/device-detail.js') }}"></script>
    <script defer src="{{ asset('js/code_behinds/financial-reports.js') }}"></script>
    <script defer src="{{ asset('js/DAO/DAOHelper.js') }}"></script>
    <script defer src="{{ asset('js/DAO/DeviceDAO.js') }}"></script>
    <script defer src="{{ asset('js/DAO/DeviceTimeDAO.js') }}"></script>
    <script defer src="{{ asset('js/DAO/DeviceTimeTransactionsDAO.js') }}"></script>
    <script defer src="{{ asset('js/Handlers/DeviceTimeTransactionHandler.js') }}"></script>
    <script defer src="{{ asset('js/Handlers/DeviceHeartbeatHandler.js') }}"></script>
    <script defer src="{{ asset('js/Handlers/DeviceTimeControlOnLoadHandler.js') }}"></script>
    <script defer src="{{ asset('js/Handlers/DeviceRateAndUsageReportHandler.js') }}"></script>
    <script defer src="{{ asset('js/Handlers/DeviceOverviewTimeTransactionsReportHandler.js') }}"></script>
    <script defer src="{{ asset('js/Handlers/DeviceOverviewTimeTransactionsReportExportHandler.js') }}"></script>
    <script defer src="{{ asset('js/Handlers/DeviceDetailedTimeTransactionsReportHandler.js') }}"></script>
    <script defer src="{{ asset('js/Handlers/DeviceDetailedTimeTransactionsReportHandlerExport.js') }}"></script>
    <script defer src="{{ asset('js/Handlers/NotificationsHandler.js') }}"></script>
</head>

<body id="bdy" class="antialiased">
    <audio id="notificationSound" src="{{ asset('sounds/notification1.mp3') }}"></audio>
    <div class="toast-container" id="toastContainer"></div>
    <div id="loadingScreen" style="">
        <img src="{{ asset('imgs/fire-loading.gif') }}" alt="Loading...">
    </div>
    <div class="relative flex flex-row h-full">
        <div id='side-nav' class="grow-0 w-96 h-full transition-all duration-300 shadow-md shadow-gray-400">
            <x-side-nav />
        </div>
        <div id="sidebar-show-pane" class="relative w-5 !bg-[#FFECAE] shadow-md shadow-gray-400 hidden">
            <button id="sidebar-show-toggle" class="absolute w-8 top-4 text-white p-1 rounded-full shadow-md !bg-[#FFECAE] hover:bg-gray-400 hover:shadow-lg transition-all duration-300">
                <img src="{{ asset('imgs/arrow-right-s-line.png') }}" alt="Bell icon" 
                            class="w-6 h-6 !block">
            </button>
        </div>
        <div id="main-container" class="flex flex-row grow-0 h-full w-full transition-all duration-300">
            <!-- Main Content -->
            <div id="main-content" class="flex flex-col grow-0 h-full w-full transition-all duration-300">
                <div class="grow-0 flex flex-row h-16 min-h-16 shadow-md bg-white">
                    <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <div class="basis-1/2">
                        <div class="flex items-center px-4 h-full text-sm font-bold">
                            @section('page-title')
                            @show
                        </div>
                    </div>
                    <div class="basis-1/2 flex items-center justify-end px-1">
                        <div class="relative inline-block text-left">
                            <button id="user-menu-button" class="flex items-center focus:outline-none">
                                <img src="{{ asset('imgs/people.png') }}" alt="User Image" class="w-8 h-8 rounded-full">
                            </button>
                            <div id="user-menu" style="z-index: 1000 !important"
                                class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                <div class="py-1">
                                    <span class="block px-4 py-2 pb-0 text-sm text-gray-700">{{ auth()->user()->FirstName }} {{
                                        auth()->user()->LastName }}</span>
                                    <div class="ui divider"></div>
                                    <a href="{{ route('profile', ['userId' => auth()->user()->UserID]) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    <a href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end px-4">
                        <button id="notification-button" class="flex items-center focus:outline-none {{ isset($notifications) && $notifications->isNotEmpty() ? '' : '!hidden' }}">
                            <img src="{{ asset('imgs/notification-default.png') }}" alt="Bell icon" 
                            class="btnShowNotificationButton w-8 h-8 !block"
                            data-no-new-notif="{{ asset('imgs/notification-default.png') }}" 
                            data-has-new-notif="{{ asset('imgs/notification-has-new.png') }}">
                            <img src="{{ asset('imgs/close.png') }}" alt="Bell icon" class="btnCloseNotificationButton w-8 h-8 !hidden">
                        </button>
                    </div>
                </div>
                <div class="h-full overflow-y-hidden">
                    <div id="wifi-status" class=" text-center bg-red-400 !text-white text-sm !hidden"></div>
                    @yield('content')
                    <script>
                        $(document).ready(function() {
                            @if (session('toast_message'))
                                $.toast({
                                    title: 'Success!',
                                    class: 'success',
                                    displayTime: 3000,
                                    position: 'bottom right',
                                    message: "{{ session('toast_message') }}"
                                });
                            @endif
                        });
                    </script>
                </div>
            </div>
        
            <!-- Notifications List -->
            <div id="notification-list" class="flex flex-col h-full w-0 shadow-2xl bg-white transition-all duration-300">
                <div class="flex items-center px-4 text-sm font-bold shadow-md">
                    <div class="grow-0 flex flex-row h-16 min-h-16">
                        <div class="basis-1/2">
                            <div class="flex items-center px-4 h-full text-sm font-bold">
                                Notifications
                            </div>
                        </div>
                    </div>
                </div>
                <div class="notification-list h-full overflow-y-auto p-5 relative">
                    @isset($notifications)
                    @foreach ($notifications as $notification)
                        <x-notification-card :notification="$notification" />
                    @endforeach
                    @endisset
                </div>
            </div>
        </div>
        
    </div>
    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const sideNav = document.getElementById('side-nav');
            const sidebarHideToggleButton = document.getElementById('sidebar-hide-toggle');
            const sidebarShowToggleButton = document.getElementById('sidebar-show-toggle');
            const sidebarShowPane = document.getElementById('sidebar-show-pane');

            const sideBarSavedState = getSideBarLocalStorageValue();

            if (sideBarSavedState === 'hidden')
            {
                hideSideBar();
            }
            else if (sideBarSavedState === 'shown')
            {
                showSideBar();
            }

            sidebarHideToggleButton.addEventListener('click', function () {
                
                hideSideBar();
                setSideBarLocalStorateValue('hidden');
            });

            sidebarShowToggleButton.addEventListener('click', function () {
                
                showSideBar();
                setSideBarLocalStorateValue('shown');
            });

            function hideSideBar()
            {
                sideNav.classList.add('absolute');
                sideNav.classList.add('-left-96');
                sidebarHideToggleButton.style.left = '0.5rem';
                
                sidebarShowPane.classList.remove('hidden');
                sidebarShowPane.classList.add('block');
            }

            function showSideBar()
            {
                sideNav.classList.remove('absolute');
                sideNav.classList.remove('-left-96');
                sidebarHideToggleButton.style.left = '16rem';
                sidebarShowPane.classList.add('hidden');
                sidebarShowPane.classList.remove('block');
            }
        });

        // let wifiStatusElement = document.getElementById("wifi-status");
        // let errorFlagVisible = false; // Track if error flag is currently visible

        // function checkWifiStatus() {
        //     if (navigator.onLine) {
        //         // Check if connected to the correct SSID
        //         fetch('/check-ssid')
        //             .then(response => response.json())
        //             .then(data => {
        //                 if (!data.connected) {
        //                     wifiStatusElement.innerText = "Not connected to the expected WiFi (" + data.ssid + ")";
        //                     wifiStatusElement.classList.remove('!text-black');
        //                     wifiStatusElement.classList.remove('bg-green-400');
        //                     wifiStatusElement.classList.remove('py-2');
        //                     wifiStatusElement.classList.add('!text-white');
        //                     wifiStatusElement.classList.add('bg-red-400');
        //                     wifiStatusElement.classList.add('py-2');
        //                     wifiStatusElement.style.display = "block";
        //                     errorFlagVisible = true;
        //                 } else {
        //                     if (errorFlagVisible) {
        //                         wifiStatusElement.innerText = "Connected!";
        //                         wifiStatusElement.classList.remove('!text-white');
        //                         wifiStatusElement.classList.remove('bg-red-400');
        //                         wifiStatusElement.classList.remove('py-2');
        //                         wifiStatusElement.classList.add('!text-black');
        //                         wifiStatusElement.classList.add('bg-green-400');
        //                         wifiStatusElement.classList.add('py-2');
                                
        //                         setTimeout(() => {
        //                             wifiStatusElement.style.display = "none"; // Hide flag after 3 seconds
        //                             errorFlagVisible = false;
        //                         }, 3000);
        //                     }
        //                 }
        //             })
        //             .catch(error => {
        //                 console.error("Error checking SSID:", error);
        //             });
        //     } else {
        //         // Show error flag when disconnected
        //         wifiStatusElement.innerText = "Disconnected";
        //         wifiStatusElement.classList.add('bg-red-400');
        //         wifiStatusElement.style.display = "block";
        //         wifiStatusElement.style.display = "block"; // Ensure flag is visible
        //         errorFlagVisible = true;
        //     }
        // }

        // window.addEventListener('load', checkWifiStatus);
        // window.addEventListener('online', checkWifiStatus);
        // window.addEventListener('offline', checkWifiStatus);

    // Check WiFi status every 5 seconds
    setInterval(checkWifiStatus, 5000);

        document.addEventListener('DOMContentLoaded', function () {
            const notificationButton = getNotificationButton();
            const notificationImage = getNotificationImage();
            const mainContent = document.getElementById('main-content');
            const notificationList = document.getElementById('notification-list');
            const btnShowNotificationButton = document.querySelector('.btnShowNotificationButton');
            const btnCloseNotificationButton = document.querySelector('.btnCloseNotificationButton');

            notificationButton.addEventListener('click', () => {
                if (notificationList.classList.contains('w-[25%]')) 
                {
                    notificationList.classList.remove('w-[25%]');
                    notificationList.classList.add('w-0');

                    mainContent.classList.remove('w-[75%]');
                    mainContent.classList.add('w-full');

                    btnShowNotificationButton.classList.add('!block');
                    btnShowNotificationButton.classList.remove('!hidden');
                    btnCloseNotificationButton.classList.remove('!block');
                    btnCloseNotificationButton.classList.add('!hidden');
                } 
                else 
                {
                    notificationList.classList.remove('w-0');
                    notificationList.classList.add('w-[25%]');

                    mainContent.classList.remove('w-full');
                    mainContent.classList.add('w-[75%]');

                    ShowNoNewNotification();

                    btnShowNotificationButton.classList.remove('!block');
                    btnShowNotificationButton.classList.add('!hidden');
                    btnCloseNotificationButton.classList.add('!block');
                    btnCloseNotificationButton.classList.remove('!hidden');
                }
            });

            notificationList.addEventListener('click', function (event) {
                // Check if the clicked element is a notification card
                const notificationCard = event.target.closest('.relative');
                if (notificationCard) {
                    const notificationID = notificationCard.getAttribute('data-id');
                    notificationCard.classList.remove('border-l-4');
                    notificationCard.classList.remove('border-l-blue-500');
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            var userMenuButton = document.getElementById('user-menu-button');
            var userMenu = document.getElementById('user-menu');

            userMenuButton.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent the click from propagating to the window
                userMenu.classList.toggle('hidden');
            });

            // Close the dropdown if clicked outside
            window.addEventListener('click', function(e) {
                if (!userMenu.contains(e.target) && e.target !== userMenuButton) {
                    userMenu.classList.add('hidden');
                }
            });

            const toastData = sessionStorage.getItem('toastMessage');
            if (toastData) {
                const { message, type } = JSON.parse(toastData);
                showToast(message, type);
                sessionStorage.removeItem('toastMessage');
            }

            new DeviceTimeControlOnLoadHandler().DoSession();
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/service-worker.js').then(function (registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function (err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }

        // Toast function
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

        //Session timeout checker
        function checkSession() {
            fetch('/check-session', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.session_active) {
                    window.location.href = '/login'; // Redirect to the login page
                }
            })
            .catch(error => console.error('Error checking session:', error));
        }

        // Check session every 30 seconds
        setInterval(checkSession, 30000);


        // Polling functionality
        if (document.querySelector('#device-management-page'))
        {
            const MIN_POLLING_INTERVAL = 10000; // 10 seconds
            let pollingInterval;
            let pollingTimeout;

            function fetchActiveTransactions() {
                // console.log('Fetching active transactions...');
                fetch('/active-transactions')
                    .then(response => response.json())
                    .then(data => {
                        // console.log('Fetched data:', data);

                        const processedData = processDeviceTransactions(data);
                        // console.log('Processed Data:', processedData);

                        // If there are running timers, adjust the polling interval to the shortest interval.
                        // Otherwise, set the polling interval to the minimum interval.
                        adjustPollingInterval(processedData);
                    })
                    .catch(error => console.error('Error fetching transactions:', error));
            }

            function processDeviceTransactions(data) {
                let shortestInterval = Infinity; // Start with a large value

                const transactions = data.map(transaction => {
                    let totalDuration = transaction.totalDuration;
                    let startTime = new Date(transaction.StartTime).getTime();

                    if (isNaN(startTime)) {
                        console.log(`Invalid StartTime for DeviceID: ${transaction.DeviceID}`);
                        return null; // Skip this transaction if StartTime is invalid
                    }

                    let endTime = startTime + totalDuration * 60 * 1000; // Convert totalDuration to milliseconds

                    const currentTime = Date.now();
                    const remainingTimeInSeconds = Math.max(0, Math.floor((endTime - currentTime) / 1000));

                    // Add 3 seconds to the remaining time
                    const adjustedRemainingTimeInSeconds = remainingTimeInSeconds + 3;

                    console.log(`Remaining time for DeviceID ${transaction.DeviceID}: ${adjustedRemainingTimeInSeconds} seconds`);

                    // Only calculate the interval for valid remaining times
                    if (adjustedRemainingTimeInSeconds > 0) {
                        const newInterval = adjustedRemainingTimeInSeconds * 1000; // Convert to milliseconds

                        // Find the shortest remaining time
                        if (newInterval < shortestInterval) {
                            shortestInterval = newInterval;
                        }
                    }

                    return {
                        DeviceID: transaction.DeviceID,
                        totalDuration,
                        startTime: new Date(startTime).toISOString(),
                        endTime: new Date(endTime).toISOString(),
                        remainingTimeInSeconds: adjustedRemainingTimeInSeconds
                    };
                });

                // Filter out null transactions (those with invalid start times)
                const validTransactions = transactions.filter(transaction => transaction !== null);

                return { validTransactions, shortestInterval };
            }

            function adjustPollingInterval({ validTransactions, shortestInterval }) {
                if (validTransactions.length > 0 && shortestInterval < Infinity) {
                    // If there are active timers, use the shortest remaining time as the polling interval
                    pollingInterval = Math.max(shortestInterval, MIN_POLLING_INTERVAL);
                } else {
                    // If there are no active timers, use the minimum polling interval
                    pollingInterval = MIN_POLLING_INTERVAL;
                }

                // console.log(`Final polling interval set to: ${pollingInterval} ms`);

                // Clear the current timeout
                if (pollingTimeout) {
                    clearTimeout(pollingTimeout);
                }

                // Set the new interval based on the calculated polling interval
                pollingTimeout = setTimeout(function () {
                    console.log('Timeout reached. Reloading the page...');
                    //location.reload(); // Refresh the page after the interval has been reached
                }, pollingInterval);
            }

            // Initial fetch and start polling
            // fetchActiveTransactions();
        }
        
        function isEmptyArray(data) {
            return Array.isArray(data) && data.length === 0;
        }

        function pollingUpdateUI(data, processedData) {
            console.log('pollingUpdateUI');
            console.log('currentlyWatchedDevices', currentlyWatchedDevices);
            if (!isEmptyArray(data)) {
                processedData.forEach(device => {
                    const deviceId = device.DeviceID;
                    currentlyWatchedDevices.push(deviceId);
                });
            } else {
                if (currentlyWatchedDevices.length > 0) {
                    currentlyWatchedDevices.forEach(deviceId => {
                        const startTimeElement = document.getElementById(`lblStartTime-${deviceId}`);
                        const endTimeElement = document.getElementById(`lblEndTime-${deviceId}`);
                        const totalTimeElement = document.getElementById(`lblTotalTime-${deviceId}`);
                        const totalRateElement = document.getElementById(`lblTotalRate-${deviceId}`);

                        if (startTimeElement && endTimeElement && totalTimeElement && totalRateElement) {
                            startTimeElement.textContent = `Start time: --:--`;
                            endTimeElement.textContent = `End time: --:--`;
                            totalTimeElement.textContent = `Total time: 0 hr 0 mins`;
                            totalRateElement.textContent = `Total charge/rate: PHP 0.00`;
                        }

                        const startButton = document.querySelector(`.start-time-button[data-id="${deviceId}"]`);
                        startButton.textContent = 'Start Time';
                        startButton.classList.remove('red');
                        const extendButtonMenu = document.querySelector(`.dropdown[data-id="${deviceId}"]`);
                        const extendButton = document.querySelector(`.extend-time-single-button[data-id="${deviceId}"]`);
                        if (extendButtonMenu) {
                            extendButtonMenu.classList.add('disabled');
                        }
                        if (extendButton) {
                            extendButton.classList.add('disabled');
                            extendButton.setAttribute('disabled', 'true');
                        }

                        updateStatusRibbon(deviceId, 'Inactive');
                    });

                    currentlyWatchedDevices = [];
                }
            }
            console.log('Active transactions:', data);
        }

        

        function updateStatusRibbon(deviceId, newStatus) {
            const statusRibbon = document.getElementById(`device-status-${deviceId}`);
            const statusClasses = {
                'Pending Configuration': 'grey',
                'Running': 'green',
                'Inactive': 'yellow',
                'Disabled': 'red'
            };
            statusRibbon.className = `ui ${statusClasses[newStatus]} ribbon label`;
            statusRibbon.textContent = newStatus;
        }


    </script>

    @yield('scripts')

</body>

</html>