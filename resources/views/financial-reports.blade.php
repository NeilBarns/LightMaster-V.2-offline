@php
use App\Enums\DeviceStatusEnum;
@endphp


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
                    <canvas id="canvasFinanceLevelRateAndUsage" width="400" height="160"></canvas>
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
                        class="btnFinanceLevelOverview active px-4 py-2 bg-blue-500 text-white rounded-l text-sm hover:bg-blue-600 focus:outline-none">Overview</button>
                    <button id="detailedButton"
                        class="btnFinanceLevelDetailed px-4 py-2 bg-gray-200 text-gray-800 rounded-r text-sm hover:bg-gray-300 focus:outline-none">Detailed</button>
                </div>
            </div>
            <div class="column">
                <div class="float-right">
                    <button
                        id="btnFinanceLevelExportCsv"
                        title="Download CSV"
                        class="w-9 h-9 p-1 bg-transparent border border-gray-400 rounded-full hover:border-gray-600 hover:bg-gray-100 transition duration-200">
                        <img src="{{ asset('imgs/csv-file.png') }}"
                            alt="edit"
                            class="w-full h-full object-contain rounded-full" />
                    </button>
                </div>
            </div>
        </div>

        <div class="divFinanceLevelOverview row !block">
            <div class="column">
               <div class="filter-section mb-4">
                   <div class="ui form">
                       <div class="four fields">
                           <div class="field">
                               <label>Start Date</label>
                               <input type="date" 
                                      value="{{ \Carbon\Carbon::today()->subDays(2)->format('Y-m-d') }}" 
                                      id="dateStartDateOverview" 
                                      name="startDate_overview">
                           </div>
                           <div class="field">
                               <label>End Date</label>
                               <input type="date" 
                                      value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" 
                                      id="dateEndDateOverview" 
                                      name="endDate_overview">
                           </div>
                           <div class="field">
                               <label>Device</label>
                               <select id="ddlDeviceOverview" name="deviceFilter_overview"
                                   class="ui fluid dropdown" multiple>
                                   <option value="">All Devices</option>
                                   @foreach ($devices as $device)
                                   <option value="{{ $device->DeviceID }}">{{ $device->DeviceName }} {{ $device->DeviceStatusID === DeviceStatusEnum::DELETED_ID ? '(Deleted)' : '' }}
                                   </option>
                                   @endforeach
                               </select>
                           </div>
                           <div class="field">
                               <label class="!text-white">Apply</label>
                               <button class="btnApplyFilterOverview ui small button primary" id="applyOverviewFilters">Apply Filters</button>
                           </div>
                       </div>
                   </div>
               </div>
               
               <div id="grdFinanceLevelOverview" 
                    class="grdFinanceLevelOverview ag-theme-alpine" 
                    style="height: 500px; width: 100%; display: block;"></div>
                <div id="grdFinanceLevelOverviewExport" 
                    class="grdFinanceLevelOverviewExport ag-theme-alpine !hidden" 
                    style="height: 500px; width: 100%; display: block;"></div>
            </div>
       </div>

        <div class="divFinanceLevelDetailed row !hidden">
            <div class="column">
                <div class="filter-section mb-4">
                    <div class="ui form">
                        <div class="four fields">
                            <div class="field">
                                <label>Start Date</label>
                                <input type="date" 
                                       value="{{ \Carbon\Carbon::today()->subDays(2)->format('Y-m-d') }}" 
                                       id="dateStartDateDetailed" 
                                       name="startDate_overview">
                            </div>
                            <div class="field">
                                <label>End Date</label>
                                <input type="date" 
                                       value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" 
                                       id="dateEndDateDetailed" 
                                       name="endDate_overview">
                            </div>
                            <div class="field">
                                <label>Device</label>
                                <select id="ddlDeviceDetailed" name="deviceFilter_overview"
                                    class="ui fluid dropdown" multiple>
                                    <option value="">All Devices</option>
                                    @foreach ($devices as $device)
                                    <option value="{{ $device->DeviceID }}">{{ $device->DeviceName }} {{ $device->DeviceStatusID === DeviceStatusEnum::DELETED_ID ? '(Deleted)' : '' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label class="!text-white">Apply</label>
                                <button class="btnApplyFilterDetailed ui small button primary" id="applyOverviewFilters">Apply Filters</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="grdFinanceLevelDetailed" 
                        class="grdFinanceLevelDetailed ag-theme-alpine" 
                        style="height: 500px; width: 100%; display: block;">
                </div>
                <div id="grdFinanceLevelDetailedExport" 
                    class="grdFinanceLevelDetailedExport ag-theme-alpine !hidden" 
                    style="height: 500px; width: 100%; display: block;"></div>
            </div>
        </div>
    </div>
</div>

<div id="sessionDetailsModal" class="ui modal">
    <div class="header">Time Transaction Details</div>
    <div class="content">
        <div id="grdSessionDetails" 
             class="grdSessionDetails ag-theme-alpine" 
             style="height: 300px; width: 100%; overflow-y: auto;"></div>
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