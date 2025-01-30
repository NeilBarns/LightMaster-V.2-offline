@php

use App\Models\DeviceTimeTransactionsResponse;
use App\Events\DeviceTransactionUpdates;

$greetings = [
'Good day ☀️',
'Hello 👋',
'Hi 😊',
'Greetings 🙌',
'Welcome 🎉',
'Nice to see you 👏',
'Hey 😃',
'Good to see you 👍',
'Howdy 🤠',
'What’s up 🤔',
'Good to have you here 🫱',
'Hope you’re doing well 💪',
'Glad you’re back 🎊',
'Pleasure to see you 😊',
'Hiya 🤗',
'Ahoy 🏴‍☠️',
'Salutations 🖖',
'How’s it going 🚀',
'Look who it is 👀',
'A warm welcome to you 🔥',
'Good vibes only ✨',
'Happy to see you 😄',
'Cheers 🍻',
'Yo 🤟',
'How are things? 🤓',
'Great to have you 💫',
'Let’s get started! 🚀',
'Welcome back 👋',
'You’re awesome 🤩',
'Feeling good? 😎',
'Let’s make today great 🌟'
];


// Pick a random greeting
$randomGreeting = $greetings[array_rand($greetings)];
@endphp

@extends('components.layout')

@section('page-title')
@parent
<div>Device Management</div>
@endsection

@section('content')
<div class="flex flex-col h-full px-5 py-7" id="device-management-page">
    <div class="ui one column stackable grid">
        <div class="column">
            <div class="ui header">{{ $randomGreeting }}, {{ auth()->user()->FirstName }}!</div>
        </div>
    </div>

    <div class="ui divider"></div>

    @if($devices->isEmpty())
    <div class="ui flex card h-full !w-full justify-center align-middle">
        <div class="flex justify-center items-center h-full w-full">
            <img class="max-h-80 opacity-50" src="{{ asset('imgs/no-device.png') }}" alt="No devices">
        </div>
    </div>
    @else
    <div class="ui cards h-full overflow-y-auto" id="device-cards-container">
        @foreach ($devices as $device)
            <x-device-card :device="$device" />
        @endforeach
    </div>

    @endif

    {{-- <div class="fixed bottom-4 right-4">
        <button id="signalButton" onclick="location.reload();"
            class="w-16 h-16 bg-green-300 rounded-full shadow-lg flex items-center justify-center"
            title="Locate device">
            <img src="{{ asset('imgs/signal.png') }}" alt="Signal" class="w-10 h-10">
        </button>
    </div> --}}
</div>

<script>

document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'visible') {
        // Refresh the page when the user comes back to the tab
        location.reload();
    }
});
</script>

@endsection