<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('imgs/lightmaster-icon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- JQuery -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>

    <!-- Fonts -->
    {{--
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet"> --}}

    {{--
    <link href="/css/app.css" rel="stylesheet"> --}}

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

    <!-- Fomantic IU -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/semantic.min.css') }}">
    <script src="{{ asset('js/semantic.min.js') }}"></script>

</head>

<body id="bdy" class="antialiased">
    <div class="toast-container" id="toastContainer"></div>
    <div id="loadingScreen" style="">
        <img src="{{ asset('imgs/loading.gif') }}" alt="Loading...">
    </div>
    <div class="flex flex-row h-full">
        <div class="grow-0 w-72 min-w-72 h-full">
            <x-side-nav />
        </div>
        <div class="grow h-full">
            <div class="flex flex-col grow-0 h-full">
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
                    <div class="basis-1/2 flex items-center justify-end px-4">
                        <div class="relative inline-block text-left">
                            <button id="user-menu-button" class="flex items-center focus:outline-none">
                                <img src="{{ asset('imgs/people.png') }}" alt="User Image" class="w-8 h-8 rounded-full">
                                <span class="ml-2 text-sm font-medium text-gray-700">{{ auth()->user()->FirstName }} {{
                                    auth()->user()->LastName }}</span>
                                <svg class="w-4 h-4 ml-1 text-gray-700" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="user-menu" style="z-index: 1000 !important"
                                class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                <div class="py-1">
                                    <a href="{{ route('profile', ['userId' => auth()->user()->UserID]) }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    <a href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="h-[92%] overflow-y-hidden">
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
        </div>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/service-worker.js').then(function (registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function (err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const toastData = sessionStorage.getItem('toastMessage');
            if (toastData) {
                const { message, type } = JSON.parse(toastData);
                showToast(message, type);
                sessionStorage.removeItem('toastMessage');
            }
        });

        function showLoading() {
            const loadingScreen = document.getElementById('loadingScreen');
            loadingScreen.classList.remove('!hidden'); // Remove the '!hidden' class
            loadingScreen.classList.add('!block'); // Add the '!block' class
        }

        function hideLoading() {
            const loadingScreen = document.getElementById('loadingScreen');
            loadingScreen.classList.remove('!block'); // Remove the '!block' class
            loadingScreen.classList.add('!hidden'); // Add the '!hidden' class
        }

        // Toast function
        function showToast(message, type = 'error', duration = 3000) {
            console.log(message);
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
            fetchActiveTransactions();
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
        });

    </script>

    @yield('scripts')

</body>

</html>