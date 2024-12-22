@extends('components.layout')

@section('page-title')
@parent
<div>Action Reports</div>
@endsection

@section('content')
<div class="flex flex-col h-full px-5 py-7 overflow-y-auto overflow-x-hidden">
    <div class="ui stackable equal width grid">
        <div class="row">
            <div class="column">
                <div class="text-center">
                    <h3 id="chartTitle">Monthly Rate and Usage By Device</h3>
                    <p id="chartSubtitle" class="text-muted text-gray-400">For the current year of {{ date('Y') }}</p>
                    <canvas id="financialChart" width="400" height="160"></canvas>
                </div>
            </div>
        </div>

        <div class="ui divider"></div>
        <div class="row">
            <div class="column">
                <h5 class="ui header">Time Transactions
                </h5>
            </div>
        </div>

        <div class="row">
            <div class="column">
                <div class="flex space-x-1">
                    <button id="overviewButton"
                        class="px-4 py-2 bg-blue-500 text-white rounded-l text-sm hover:bg-blue-600 focus:outline-none">Overview</button>
                    <button id="detailedButton"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-r text-sm hover:bg-gray-300 focus:outline-none">Detailed</button>
                </div>
            </div>
        </div>

        <div id="tblDetailed" class="row !hidden">
            <div class="column">
                <div class="filter-section mb-4">
                    <div class="ui form">
                        <div class="four fields">
                            <div class="field">
                                <label>Start Date</label>
                                <input type="date" id="startDate" name="startDate">
                            </div>
                            <div class="field">
                                <label>End Date</label>
                                <input type="date" id="endDate" name="endDate">
                            </div>
                            <div class="field">
                                <label>Device</label>
                                <select id="deviceFilter" name="deviceFilter" class="ui fluid dropdown" multiple>
                                    <option value="">All Devices</option>
                                    @foreach ($devices as $device)
                                    <option value="{{ $device->ExternalDeviceName }}">{{ $device->ExternalDeviceName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Transaction Type</label>
                                <select id="transactionFilter" name="transactionFilter" class="ui fluid dropdown"
                                    multiple>
                                    <option value="">All Transactions</option>
                                    <option value="Start">Start</option>
                                    <option value="End">End</option>
                                    <option value="Pause">Pause</option>
                                    <option value="Resume">Resume</option>
                                    <option value="Start Free">Start Free</option>
                                    <option value="End Free">End Free</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>Triggered By</label>
                                <select id="triggeredByFilter" name="triggeredByFilter" class="ui fluid dropdown"
                                    multiple>
                                    <option value="">All Users</option>
                                    <option value="Device">Device</option>
                                    @foreach ($users as $user)
                                    <option value="{{ $user->FirstName }} {{ $user->LastName }}">{{ $user->FirstName }}
                                        {{ $user->LastName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label class="!text-white">Apply</label>
                                <button class="ui small button primary" id="applyFilters">Apply Filters</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tableContainer" class="ui celled table-container max-h-[500px] overflow-y-auto">
                    <table class="ui celled table" id="transactionTable">
                        <thead class="sticky top-0 bg-white">
                            <tr>
                                <th class="px-4 py-2">Device</th>
                                <th class="px-4 py-2">Transaction</th>
                                <th class="px-4 py-2">Open time?</th>
                                <th class="px-4 py-2">Time</th>
                                <th class="px-4 py-2">Duration</th>
                                <th class="px-4 py-2">Rate</th>
                                <th class="px-4 py-2">Triggered By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $totalRate = 0;
                            $totalDuration = 0;
                            @endphp

                            @foreach($rptDeviceTimeTransactions as $transaction)
                            @php
                            // Accumulate total rate and duration
                            if ($transaction->TransactionType == 'End'){
                            $totalRate += $transaction->Rate;
                            }
                            if ($transaction->TransactionType == 'Start' || $transaction->TransactionType == 'Extend')
                            {
                            $totalDuration += $transaction->Duration;
                            }

                            // Determine the creator name
                            $creatorName = 'N/A';
                            if ($transaction->CreatedByUserId === 999999) {
                            $creatorName = 'Device';
                            } elseif ($transaction->creator && $transaction->creator->UserID > 0) {
                            $creatorName = $transaction->creator->FirstName . ' ' . $transaction->creator->LastName;
                            }
                            @endphp
                            @if ($transaction->TransactionType == 'End')
                            <tr>
                                <td>{{ $transaction->device ? $transaction->device->ExternalDeviceName : 'N/A' }}</td>
                                <td>{{ $transaction->TransactionType }}</td>
                                <td></td>
                                <td>{{ $transaction->Time->format('F d, Y h:i:s A') }}</td>
                                <td colspan="2" class="font-bold">{{ $transaction->StoppageType }} Stoppage</td>
                                <td>{{ $creatorName }}</td>
                            </tr>
                            <tr class="font-bold">
                                <td colspan="4" class="font-bold">Total Duration and Rate</td>
                                <td>{{ convertSecondsToTimeFormat($totalDuration) }}</td>
                                <td colspan="3">PHP {{ number_format($transaction->Rate, 2) }}</td>
                            </tr>
                            @elseif ($transaction->TransactionType == 'Pause' && $transaction->Reason == null)
                            <tr>
                                <td>{{ $transaction->device ? $transaction->device->ExternalDeviceName : 'N/A' }}</td>
                                <td>{{ $transaction->TransactionType }}</td>
                                <td></td>
                                <td>{{ $transaction->Time->format('F d, Y h:i:s A') }}</td>
                                <td colspan="2" class="font-bold">Remaining time: {{
                                    convertSecondsToTimeFormat($transaction->Duration) }}</td>
                                <td>{{ $creatorName }}</td>
                            </tr>
                            @elseif ($transaction->TransactionType == 'Pause' && $transaction->Reason != null)
                            <tr>
                                <td>{{ $transaction->device ? $transaction->device->ExternalDeviceName : 'N/A' }}</td>
                                <td>{{ $transaction->TransactionType }}</td>
                                <td></td>
                                <td>{{ $transaction->Time->format('F d, Y h:i:s A') }}</td>
                                <td colspan="2">
                                    <a href="javascript:void(0);"
                                        onclick="showReasonModal('{{ $transaction->Reason }} with remaining time {{ convertSecondsToTimeFormat($transaction->Duration) }}')">
                                        Show reason
                                    </a>
                                </td>
                                <td>{{ $creatorName }}</td>
                            </tr>
                            @elseif ($transaction->TransactionType == 'Start Free')
                            <tr>
                                <td>{{ $transaction->device ? $transaction->device->ExternalDeviceName : 'N/A' }}</td>
                                <td>{{ $transaction->TransactionType }}</td>
                                <td></td>
                                <td>{{ $transaction->Time->format('F d, Y h:i:s A') }}</td>
                                <td colspan="2">
                                    <a href="javascript:void(0);"
                                        onclick="showReasonModal('{{ $transaction->Reason }}')">
                                        Show reason
                                    </a>
                                </td>
                                <td>{{ $creatorName }}</td>
                            </tr>
                            @elseif ($transaction->TransactionType == 'End Free')
                            <tr>
                                <td>{{ $transaction->device ? $transaction->device->ExternalDeviceName : 'N/A' }}</td>
                                <td>{{ $transaction->TransactionType }}</td>
                                <td></td>
                                <td>{{ $transaction->Time->format('F d, Y h:i:s A') }}</td>
                                <td colspan="2">Duration: {{ convertSecondsToTimeFormat($transaction->Duration) }}</td>
                                <td>{{ $creatorName }}</td>
                            </tr>
                            @elseif ($transaction->TransactionType == 'Resume')
                            <tr>
                                <td>{{ $transaction->device ? $transaction->device->ExternalDeviceName : 'N/A' }}</td>
                                <td>{{ $transaction->TransactionType }}</td>
                                <td></td>
                                <td colspan="3">{{ $transaction->Time->format('F d, Y h:i:s A') }}</td>
                                <td>{{ $creatorName }}</td>
                            </tr>
                            @else
                            <tr>
                                @if ($transaction->TransactionType == 'Start')
                                <td>{{ $transaction->device ? $transaction->device->ExternalDeviceName : 'N/A' }}</td>
                                <td>{{ $transaction->TransactionType }}</td>
                                <td>{{ $transaction->IsOpenTime == 1 ? "Yes" : "No" }}</td>
                                <td>{{ $transaction->Time->format('F d, Y h:i:s A') }}</td>
                                <td>{{ convertSecondsToTimeFormat($transaction->Duration) }}</td>
                                <td>{{ number_format($transaction->Rate, 2) }}</td>
                                <td>{{ $creatorName }}</td>
                                @else
                                <td>{{ $transaction->device ? $transaction->device->ExternalDeviceName : 'N/A' }}</td>
                                <td>{{ $transaction->TransactionType }}</td>
                                <td></td>
                                <td>{{ $transaction->Time->format('F d, Y h:i:s A') }}</td>
                                <td>{{ convertSecondsToTimeFormat($transaction->Duration) }}</td>
                                <td>{{ number_format($transaction->Rate, 2) }}</td>
                                <td>{{ $creatorName }}</td>
                                @endif
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                        <tfoot class="sticky bottom-0 bg-white !font-bold">
                            <tr class="!font-bold bg-cyan-100 h-20">
                                <td colspan="4" class="!font-bold">Overall Duration and Rate Total</td>
                                <td class="!font-bold">{{ convertSecondsToTimeFormat($totalDuration) }}</td>
                                <td colspan="3" class="!font-bold">PHP {{ number_format($totalRate, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div id="tblOverview" class="row !block">
            <div class="column">
                <div class="filter-section mb-4">
                    <div class="ui form">
                        <div class="four fields">
                            <div class="field">
                                <label>Start Date</label>
                                <input type="date" id="startDate_overview" name="startDate_overview">
                            </div>
                            <div class="field">
                                <label>End Date</label>
                                <input type="date" id="endDate_overview" name="endDate_overview">
                            </div>
                            <div class="field">
                                <label>Device</label>
                                <select id="deviceFilter_overview" name="deviceFilter_overview"
                                    class="ui fluid dropdown" multiple>
                                    <option value="">All Devices</option>
                                    @foreach ($devices as $device)
                                    <option value="{{ $device->ExternalDeviceName }}">{{ $device->ExternalDeviceName }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label class="!text-white">Apply</label>
                                <button class="ui small button primary" id="applyOverviewFilters">Apply Filters</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="newTableContainer" class="ui celled table-container max-h-[500px] overflow-y-auto">
                    <table class="ui celled table" id="gameSessionsTable">
                        <thead class="sticky top-0 bg-white">
                            <tr>
                                <th class="px-4 py-2">Device</th>
                                <th class="px-4 py-2">Start Time</th>
                                <th class="px-4 py-2">End Time</th>
                                <th class="px-4 py-2">Open time?</th>
                                <th class="px-4 py-2">Total Duration</th>
                                <th class="px-4 py-2">Total Rate</th>
                                <th class="px-4 py-2">Summary</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $sessions = []; // Array to store sessions
                            $sessionId = null;
                            // dd($rptDeviceTimeTransactions);
                            // Iterate through transactions and group by DeviceID
                            foreach ($rptDeviceTimeTransactions->sortBy('TransactionID') as $transaction) {
                            $deviceId = $transaction->DeviceID; // Group by DeviceID
                            $deviceName = $transaction->device ? $transaction->device->ExternalDeviceName : 'N/A';

                            // Handle Start transaction
                            if ($transaction->TransactionType == 'Start') {
                            $sessionId = $transaction->DeviceTimeTransactionsID;

                            $sessions[$sessionId] = [
                            'deviceName' => $transaction->device->ExternalDeviceName ?? 'N/A',
                            'startTime' => $transaction->Time,
                            'endTime' => null,
                            'isOpenTime' => $transaction->IsOpenTime,
                            'totalDuration' => $transaction->Duration,
                            'totalRate' => $transaction->Rate,
                            'transactions' => [$transaction],
                            ];
                            }

                            // Handle Extend transaction: add the duration and rate to the same session
                            elseif ($transaction->TransactionType == 'Extend') {
                            if (isset($sessions[$sessionId]['startTime']) && !isset($sessions[$sessionId]['endTime'])) {
                            $sessions[$sessionId]['totalDuration'] += $transaction->Duration ?? 0;
                            $sessions[$sessionId]['totalRate'] += $transaction->Rate ?? 0;
                            $sessions[$sessionId]['transactions'][] = $transaction;
                            }
                            }

                            // Handle End transaction: set the end time for the same session
                            elseif ($transaction->TransactionType == 'End') {
                            if (isset($sessions[$sessionId]['startTime']) && !isset($sessions[$sessionId]['endTime'])) {
                            $sessions[$sessionId]['endTime'] = $transaction->Time;
                            $sessions[$sessionId]['transactions'][] = $transaction;
                            }
                            }
                            elseif ($transaction->TransactionType == 'Start Free') {}
                            elseif ($transaction->TransactionType == 'End Free') {}
                            else {
                            $sessions[$sessionId]['transactions'][] = $transaction;
                            }
                            }
                            @endphp

                            @foreach($sessions as $session)
                            <tr>
                                <td>{{ $session['deviceName'] }}</td>
                                <td>{{ $session['startTime'] ? $session['startTime']->format('F d, Y h:i:s A') : 'N/A'
                                    }}</td>
                                <td>{{ $session['endTime'] ? $session['endTime']->format('F d, Y h:i:s A') : 'N/A' }}
                                </td>
                                <td>{{ $session['isOpenTime'] == 1 ? 'Yes' : 'No' }}
                                </td>
                                <td>{{ convertSecondsToTimeFormat($session['totalDuration'] ?? 0 )}}</td>
                                <td>PHP {{ number_format($session['totalRate'], 2) }}</td>
                                <td>
                                    <a href="javascript:void(0);"
                                        onclick='showSessionDetailsModal({{ json_encode($session["transactions"]) }})'>
                                        View Summary
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="sticky bottom-0 bg-white !font-bold">
                            <tr class="!font-bold bg-cyan-100 h-20">
                                <td colspan="4" class="!font-bold">Overall Duration and Rate Total</td>
                                <td class="!font-bold">{{ convertSecondsToTimeFormat($totalDuration) }}</td>
                                <td colspan="3" class="!font-bold">PHP {{ number_format($totalRate, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Modal for showing game session details -->
<div id="sessionDetailsModal" class="ui modal">
    <div class="header">Game Session Details</div>
    <div class="content">
        <table class="ui celled table">
            <thead>
                <tr>
                    <th>Transaction</th>
                    <th>Time</th>
                    <th>Duration</th>
                    <th>Rate</th>
                    <th>Triggered By</th>
                </tr>
            </thead>
            <tbody id="sessionDetailsTableBody"></tbody>
            <tfoot id="sessionDetailsTableFooter"></tfoot>
        </table>
    </div>
    <div class="actions">
        <div class="ui button" onclick="hideSessionDetailsModal()">Close</div>
    </div>
</div>

<div id="reasonModal" class="ui modal">
    <div class="header">Reason</div>
    <div class="content">
        <p id="reasonContent"></p>
    </div>
    <div class="actions">
        <button class="ui button" onclick="$('#reasonModal').modal('hide')">Close</button>
    </div>
</div>
@endsection

<script>
    function showReasonModal(reason) {
        const reasonModal = document.getElementById('reasonModal');
        const reasonContent = document.getElementById('reasonContent');

        if (reasonModal && reasonContent) {
            reasonContent.textContent = reason; // Set the reason in the modal
            $(reasonModal).modal('show'); // Show the modal using Semantic UI's modal function
        } else {
            console.error('Modal or reason content element not found');
        }
    }

    function showSessionDetailsModal(transactions) {
        // Parse the transactions string into an array
        const parsedTransactions = typeof transactions === 'string' ? JSON.parse(decodeURIComponent(transactions)) : transactions;

        const tableBody = document.getElementById('sessionDetailsTableBody');
        const tableFooter = document.getElementById('sessionDetailsTableFooter');
        tableBody.innerHTML = ''; // Clear previous content
        tableFooter.innerHTML = ''; // Clear previous content

        let footerDuration = 0;
        let footerRate = 0;

        parsedTransactions.sort((a, b) => new Date(a.Time) - new Date(b.Time));

        parsedTransactions.forEach(transaction => {
            let creatorName = 'N/A';
            if (transaction.CreatedByUserId === 999999) {
                creatorName = 'Device';
            } else if (transaction.creator && transaction.creator.UserID > 0) {
                creatorName = transaction.creator.FirstName + ' ' + transaction.creator.LastName;
            }

            let row;

            if (transaction.TransactionType === 'Pause')
            {
                row = `
                <tr>
                    <td>${transaction.TransactionType}</td>
                    <td>${new Date(transaction.Time).toLocaleString()}</td>
                    <td colspan="2" class="font-bold">Remaining time: ${convertSecondsToTimeFormat(transaction.Duration ?? 0)}</td>
                    <td>${creatorName}</td>
                </tr>`;
            }
            else if (transaction.TransactionType === 'Resume')
            {
                row = `
                <tr>
                    <td>${transaction.TransactionType}</td>
                    <td>${new Date(transaction.Time).toLocaleString()}</td>
                    <td colspan="2" class="font-bold"></td>
                    <td>${creatorName}</td>
                </tr>`;
            }
            else if (transaction.TransactionType === 'Start Free'){ row = ``; }
            else if (transaction.TransactionType === 'End Free'){ row = ``; }
            else {
                row = `
                <tr>
                    <td>${transaction.TransactionType}</td>
                    <td>${new Date(transaction.Time).toLocaleString()}</td>
                    <td>${convertSecondsToTimeFormat(transaction.Duration ?? 0)}</td>
                    <td>PHP ${transaction.Rate ? transaction.Rate : '0.00'}</td>
                    <td>${creatorName}</td>
                </tr>`;
            }

            if (transaction.TransactionType === 'Start' || transaction.TransactionType === 'Extend')
            {
                footerDuration += transaction.Duration;
                footerRate += parseFloat(transaction.Rate);
            }
            
            
            tableBody.insertAdjacentHTML('beforeend', row);
        });

        let foot;

        foot = `
        <tr class="!font-bold bg-cyan-100 h-20">
                <td colspan="2" class="!font-bold">Overall Duration and Rate Total</td>
                <td class="!font-bold">${convertSecondsToTimeFormat(footerDuration)}</td>
                <td colspan="2" class="!font-bold">PHP ${validateAndFormatNumber(footerRate)}</td>
                </tr>
        `;

        tableFooter.insertAdjacentHTML('beforeend', foot);

        $('#sessionDetailsModal').modal('show'); // Assuming you are using Semantic UI for modals
    }

    // Function to hide the modal
    function hideSessionDetailsModal() {
        $('#sessionDetailsModal').modal('hide');
    }

    // Function to convert seconds to a formatted string (hours, minutes, seconds)
    function convertSecondsToTimeFormat(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const remainingSeconds = seconds % 60;

        let timeString = '';

        if (hours > 0) {
            timeString += `${hours} hr${hours > 1 ? 's' : ''} `;
        }
        if (minutes > 0) {
            timeString += `${minutes} min${minutes > 1 ? 's' : ''} `;
        }
        if (remainingSeconds > 0) {
            timeString += `${remainingSeconds} sec${remainingSeconds > 1 ? 's' : ''}`;
        }

        return timeString.trim();
    }

    // Function to convert minutes to hours and minutes format
    function convertMinutesToHoursAndMinutes(minutes) {
        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;

        let timeString = '';

        if (hours > 0) {
            timeString += `${hours} hr${hours > 1 ? 's' : ''} `;
        }
        if (remainingMinutes > 0) {
            timeString += `${remainingMinutes} min${remainingMinutes > 1 ? 's' : ''}`;
        }

        return timeString.trim();
    }

    // Function to format numbers
    function number_format(number, decimals) {
        if (typeof number !== 'number' || isNaN(number)) {
            console.log(number);
            number = 0; // Default to 0 if the value is not a number
        }
        return number.toFixed(decimals);
        // return number;
    }

    function validateAndFormatNumber(value) {
        // Convert value to a number
        const number = typeof value === 'string' ? parseFloat(value) : value;

        // Validate the number
        if (typeof number !== 'number' || isNaN(number)) {
            console.error('Invalid number:', value);
            return null; // Handle invalid numbers
        }

        // Format the number to two decimal places
        return number.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', function() {

    const today = new Date().toISOString().split('T')[0]; // Format as 'YYYY-MM-DD'
    let yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1); // Subtract 1 day
    yesterday = yesterday.toISOString().split('T')[0]; // Format as 'YYYY-MM-DD'
    document.getElementById('startDate').value = yesterday;
    document.getElementById('endDate').value = today;
    document.getElementById('startDate_overview').value = yesterday;
    document.getElementById('endDate_overview').value = today;

    const devicesData = @json($data);

    function getConfig(viewType, devicesData) {
        const monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        const datasets = [
            ...devicesData.map((device, index) => ({
                label: device.name + " Rate",
                data: device.monthlyRates,
                backgroundColor: 'rgb(126, 183, 237)', // Add transparency back
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: {
                topLeft: 5, // Add border radius to top-left corner
                topRight: 5, // Add border radius to top-right corner
                },
                yAxisID: 'y',
                order: 2 + index, // Stagger bar order
            })),
            ...devicesData.map((device, index) => ({
                label: device.name + " Usage",
                data: device.monthlyUsage,
                type: 'line',
                backgroundColor: 'rgb(66, 66, 71)', // Line fill color with transparency
                borderColor: 'rgb(66, 66, 71)', // Red line
                borderWidth: 2,
                pointRadius: 4, // Increase node size
                pointBackgroundColor: 'rgb(66, 66, 71)', // Red node
                yAxisID: 'y1',
                order: 1, // Ensure line is in front
                tension: 0.4, // Smooth line
                cubicInterpolationMode: 'monotone'
            })),
        ];

        return {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: datasets,
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false, // Hide the legend
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Rate (PHP)'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false // Only grid lines for the first y-axis
                        },
                        title: {
                            display: true,
                            text: 'Usage (minutes)'
                        }
                    }
                }
            }
        };
    }

    const tblOverview = document.getElementById("tblOverview");
    const tblDetailed = document.getElementById("tblDetailed");

    document.getElementById("detailedButton").addEventListener("click", () => {
        tblDetailed.classList.add("!block");
        tblDetailed.classList.remove("!hidden");
        tblOverview.classList.remove("!block");
        tblOverview.classList.add("!hidden");
        document.getElementById("detailedButton").classList.add("bg-blue-500", "text-white");
        document.getElementById("overviewButton").classList.remove("bg-blue-500", "text-white");
        document.getElementById("detailedButton").classList.remove("bg-gray-200", "text-gray-800");
        document.getElementById("overviewButton").classList.add("bg-gray-200", "text-gray-800");
    });

    document.getElementById("overviewButton").addEventListener("click", () => {
        tblOverview.classList.add("!block");
        tblOverview.classList.remove("!hidden");
        tblDetailed.classList.remove("!block");
        tblDetailed.classList.add("!hidden");
        document.getElementById("overviewButton").classList.add("bg-blue-500", "text-white");
        document.getElementById("detailedButton").classList.remove("bg-blue-500", "text-white");
        document.getElementById("overviewButton").classList.remove("bg-gray-200", "text-gray-800");
        document.getElementById("detailedButton").classList.add("bg-gray-200", "text-gray-800");
    });

    function scrollToTop() {
        const container = document.getElementById('tableContainer');
        if (container) {
            container.scrollTop = 0;
        }
    }

    document.getElementById('applyFilters').addEventListener('click', function() {
        showLoading();
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const deviceNames = $('#deviceFilter').val(); // Get selected values as array
        const transactionTypes = $('#transactionFilter').val(); // Get selected values as array
        const triggeredBy = $('#triggeredByFilter').val(); // Get selected values as array

        // Construct URL with query parameters
        const url = new URL('/reports/transactions/filter', window.location.origin);
        url.searchParams.append('startDate', startDate);
        url.searchParams.append('endDate', endDate);

        // Append array parameters properly
        deviceNames.forEach(name => url.searchParams.append('deviceNames[]', name));
        transactionTypes.forEach(type => url.searchParams.append('transactionTypes[]', type));
        triggeredBy.forEach(userId => url.searchParams.append('triggeredBy[]', userId));

        fetch(url, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            const tbody = document.querySelector('#transactionTable tbody');
            const tfoot = document.querySelector('#transactionTable tfoot');
            let totalRate = 0;
            let totalDuration = 0;

            tbody.innerHTML = ''; // Clear existing rows
            tfoot.innerHTML = ''; // Clear existing rows

            data.forEach(transaction => {
                

                if (transaction.TransactionType == 'End'){
                    totalRate += parseFloat(transaction.Rate);
                    totalDuration = transaction.Duration;
                }
                // if (transaction.TransactionType == 'Start' || transaction.TransactionType == 'Extend'){
                //     totalDuration += transaction.Duration;
                // }

                const creatorName = transaction.CreatedByUserId === 999999 ? 'Device' : (transaction.creator ? `${transaction.creator.FirstName} ${transaction.creator.LastName}` : 'N/A');
                const formattedTime = new Date(transaction.Time).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: 'numeric',
                    second: 'numeric',
                    hour12: true
                });
                
                if (!transaction.device || transaction.device.ExternalDeviceName == null) {
                    if (!transaction.device) {
                        transaction.device = {};
                    }
                    transaction.device.ExternalDeviceName = "Missing Device Name";
                }

                let rowHtml = `<tr><td>${transaction.device.ExternalDeviceName}</td>`;

                if (transaction.TransactionType === 'End') {
                    rowHtml += `
                        <td>${transaction.TransactionType}</td>
                        <td></td>
                        <td>${formattedTime}</td>
                        <td colspan="2" class="font-bold">${transaction.StoppageType} Stoppage</td>
                        <td>${creatorName}</td>
                    </tr>
                    <tr class="font-bold">
                        <td colspan="4" class="font-bold">Total Duration and Rate</td>
                        <td>${convertSecondsToTimeFormat(totalDuration)}</td>
                        <td colspan="3">PHP ${validateAndFormatNumber(transaction.Rate, 2)}</td>
                    </tr>`;
                } else if (transaction.TransactionType === 'Pause' && transaction.Reason === null) {
                    rowHtml += `
                        <td>${transaction.TransactionType}</td>
                        <td></td>
                        <td>${formattedTime}</td>
                        <td colspan="2" class="font-bold">Remaining time: ${convertSecondsToTimeFormat(transaction.Duration)}</td>
                        <td>${creatorName}</td>
                    </tr>`;
                } else if (transaction.TransactionType === 'Pause' && transaction.Reason !== null) {
                    rowHtml += `
                        <td>${transaction.TransactionType}</td>
                        <td></td>
                        <td>${formattedTime}</td>
                        <td colspan="2">
                            <a href="javascript:void(0);" onclick="showReasonModal('${transaction.Reason} with remaining time ${convertSecondsToTimeFormat(transaction.Duration)}')">Show reason</a>
                        </td>
                        <td>${creatorName}</td>
                    </tr>`;
                }  
                else if (transaction.TransactionType === 'Start Free') {
                    rowHtml += `
                        <td>${transaction.TransactionType}</td>
                        <td></td>
                        <td>${formattedTime}</td>
                        <td colspan="2">
                            <a href="javascript:void(0);" onclick="showReasonModal('${transaction.Reason}')">Show reason</a>
                        </td>
                        <td>${creatorName}</td>
                    </tr>`;
                } else if (transaction.TransactionType === 'End Free') {
                    rowHtml += `
                        <td>${transaction.TransactionType}</td>
                        <td></td>
                        <td>${formattedTime}</td>
                        <td colspan="2">Duration: ${convertSecondsToTimeFormat(transaction.Duration)}</td>
                        <td>${creatorName}</td>
                    </tr>`;
                } else if (transaction.TransactionType === 'Resume') {
                    rowHtml += `
                        <td>${transaction.TransactionType}</td>
                        <td></td>
                        <td colspan="3">${formattedTime}</td>
                        <td>${creatorName}</td>
                    </tr>`;
                } else {
                    if (transaction.TransactionType === 'Start') {
                        rowHtml += `
                        <td>${transaction.TransactionType}</td>
                        <td>{{ isset($transaction->IsOpenTime) && $transaction->IsOpenTime == 1 ? "Yes" : "No" }}</td>
                        <td>${formattedTime}</td>
                        <td>${convertSecondsToTimeFormat(transaction.Duration)}</td>
                        <td>${validateAndFormatNumber(transaction.Rate, 2)}</td>
                        <td>${creatorName}</td>
                    </tr>`;
                    }
                    else {
                        rowHtml += `
                        <td>${transaction.TransactionType}</td>
                        <td></td>
                        <td>${formattedTime}</td>
                        <td>${convertSecondsToTimeFormat(transaction.Duration)}</td>
                        <td>${validateAndFormatNumber(transaction.Rate, 2)}</td>
                        <td>${creatorName}</td>
                    </tr>`;
                    }
                }

                tbody.innerHTML += rowHtml;
            });

            let rowFoorHtml = `<tr class="!font-bold bg-cyan-100 h-20">
                <td colspan="4" class="!font-bold">Overall Duration and Rate Total</td>
                <td class="!font-bold">${convertSecondsToTimeFormat(totalDuration)}</td>
                <td colspan="3" class="!font-bold">PHP ${validateAndFormatNumber(totalRate, 2)}</td>
                </tr>`;

                tfoot.innerHTML += rowFoorHtml;
                hideLoading();
                scrollToTop();
        })
        .catch(error => {
            console.log('Fetch error:', error);
            showToast('An error occurred. Please try again.', 'error');
            hideLoading();
        });
    });

    document.getElementById('applyOverviewFilters').addEventListener('click', function() {
        showLoading();
        const startDate = document.getElementById('startDate_overview').value;
        const endDate = document.getElementById('endDate_overview').value;
        const deviceNames = $('#deviceFilter_overview').val(); // Get selected values as array

        // Construct URL with query parameters
        const url = new URL('/reports/transactions/filter/overview', window.location.origin);
        url.searchParams.append('startDate', startDate);
        url.searchParams.append('endDate', endDate);

        // Append array parameters properly
        deviceNames.forEach(name => url.searchParams.append('deviceNames[]', name));

        fetch(url, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                console.log(response);
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const tbody = document.querySelector('#gameSessionsTable tbody');
            const tfoot = document.querySelector('#gameSessionsTable tfoot');
            tbody.innerHTML = ''; // Clear existing rows
            tfoot.innerHTML = ''; // Clear existing rows
            let footerDuration = 0;
            let footerRate = 0;

            data.sessions.forEach(session => {
                const startTime = session.startTime ? new Date(session.startTime).toLocaleString() : 'N/A';
                const endTime = session.endTime ? new Date(session.endTime).toLocaleString() : 'N/A';
                const isOpenTime = session.isOpenTime;
                const totalDuration = session.totalDuration ? session.totalDuration : 0;
                const totalRate = session.totalRate ? parseFloat(session.totalRate) : 0.00;
                
                footerDuration += totalDuration;
                footerRate += totalRate;
                
                const formattedTime_Start = new Date(startTime).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: 'numeric',
                    second: 'numeric',
                    hour12: true
                });

                const formattedTime_End = new Date(endTime).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: 'numeric',
                    second: 'numeric',
                    hour12: true
                });

                // Safely stringify and encode the transactions array for use in JavaScript
                const transactions = encodeURIComponent(JSON.stringify(session.transactions));

                const rowHtml = `
                    <tr>
                        <td>${session.deviceName}</td>
                        <td>${formattedTime_Start}</td>
                        <td>${formattedTime_End}</td>
                        <td>${isOpenTime === 1 ? "Yes" : "No"}</td>
                        <td>${convertSecondsToTimeFormat(totalDuration)}</td>
                        <td>PHP ${validateAndFormatNumber(totalRate,2)}</td>
                        <td><a href="javascript:void(0);" onclick="showSessionDetailsModal('${transactions}')">View Summary</a></td>
                    </tr>
                `;

                tbody.innerHTML += rowHtml;
            });
            let rowFoorHtml = `<tr class="!font-bold bg-cyan-100 h-20">
                <td colspan="4" class="!font-bold">Overall Duration and Rate Total</td>
                <td class="!font-bold">${convertSecondsToTimeFormat(footerDuration)}</td>
                <td colspan="2" class="!font-bold">PHP ${validateAndFormatNumber(footerRate, 2)}</td>
                </tr>`;

                tfoot.innerHTML += rowFoorHtml;

            hideLoading();
            scrollToTop();
        })
        .catch(error => {
            console.log('Fetch error:', error);
            showToast('An error occurred. Please try again.', 'error');
            hideLoading();
        });
    });


    document.getElementById('applyOverviewFilters').click();
    document.getElementById('applyFilters').click();
    
    const ctx = document.getElementById('financialChart').getContext('2d');
    const myChart = new Chart(ctx, getConfig('monthly', devicesData));
});
</script>