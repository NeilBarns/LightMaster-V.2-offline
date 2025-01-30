@php
use App\Enums\NotificationLevelEnum;

$cardColorByNotifLevel = '';

switch ($notification->NotificationLevelID) {
    case NotificationLevelEnum::NORMAL_ID:
        $cardColorByNotifLevel = 'bg-white-100';
        break;
    case NotificationLevelEnum::WARNING_ID:
        $cardColorByNotifLevel = 'bg-yellow-100';
        break;
    case NotificationLevelEnum::ERROR_ID:
        $cardColorByNotifLevel = 'bg-red-300';
        break;
}
@endphp
<div data-id="{{ $notification->NotificationID }}" 
    class="relative flex items-center {{$cardColorByNotifLevel}} p-4 rounded-lg shadow-md space-x-4 mb-5 cursor-pointer"
    data-device-id="{{ $notification->DeviceID }}">
    <div class="flex items-center justify-center w-12 h-12  text-white">
        <img src="{{ asset('imgs/nodes.png') }}" alt="Bell icon" class="w-9 h-9 !block">
    </div>
    <div class="flex-1">
        <p class="text-sm font-medium text-gray-900 break-words ">
            {{ $notification->Notification ?? 'Notification text here' }}
        </p>
        <p class="text-sm text-gray-500">
            {{ $notification->created_at->format('M d, Y h:i A') ?? 'Notification Created date' }}
        </p>
    </div>
</div>
