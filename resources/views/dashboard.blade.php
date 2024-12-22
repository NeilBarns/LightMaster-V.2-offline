@extends('components.layout')

@section('page-title')
@parent
<div>Dashboard</div>
@endsection

@section('content')
<div id="dashboard-view" class="flex flex-col h-full">
    <div class="flex flex-row h-full" style="display: none">
        <div class="basis-3/4 px-5 pb-0 pt-10 overflow-y-auto">
            <h4 class="ui horizontal left aligned divider header">
                Time In and Time Out Counts
            </h4>
            <canvas id="myChart"></canvas>
            <div class="ui horizontal cards h-1/4 flex justify-center !mt-5 !mb-10">
                <div class="ui small card">
                    <div class="center aligned content !bg-[#daf2f2]">
                        <div class="header !text-lg">Count of registered owners</div>
                        <div class="description h-full flex justify-center items-center text-5xl !mt-0">
                            <span id="spanRegisteredOwners"></span>
                        </div>
                    </div>
                </div>
                <div class="ui small card !bg-[#ffe0e7]">
                    <div class="center aligned content">
                        <div class="header !text-lg">Count of unregistered cards</div>
                        <div class="description h-full flex justify-center items-center text-5xl !mt-0">
                            <span id="spanUnregistered"></span>
                        </div>
                    </div>
                </div>

            </div>
            <h4 class="ui horizontal left aligned divider header !mt-10">
                Timed In Owners
            </h4>
            <div class="grow overflow-auto h-full w-full">
                <div id="grdTimedInOwners" class="ag-theme-quartz" style="height: 500px"></div>
            </div>
        </div>
        <div id="divCardNotifList" class="basis-1/4 shadow-md p-5 overflow-y-auto">
        </div>
    </div>
</div>
@endsection