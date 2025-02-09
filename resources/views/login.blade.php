<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite( ['resources/css/app.css', 'resources/js/app.js'])

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
<body class="relative flex items-center justify-center h-screen bg-[#FFECAE] overflow-hidden">
    <div class="ripple-background">
        <span class="ripple"></span>
        <span class="ripple"></span>
        <span class="ripple"></span>
        <span class="ripple"></span>
        <span class="ripple"></span>
    </div>

    <div class="max-w-7xl w-full bg-white p-8 rounded-lg shadow-2xl flex relative z-10">
        <div class="w-1/2 flex justify-center items-center rounded-lg p-6 bg-gray-50">
            <img src="{{ asset('imgs/Humaaans.png') }}" alt="sapiens" class="w-full">
        </div>

        <div class="w-1/2 p-8">
            <div class="text-right float-right text-sm text-gray-500">
                <img class="h-[25px]" src="{{ asset('imgs/LightMaster.png') }}" alt="logo">
            </div>
            <h2 class="text-2xl font-bold mb-2 text-gray-700">Hello Again!</h2>
            <p class="text-sm text-gray-500 mb-6">Welcome back, you've been missed!</p>
            <form action="{{ route('auth.login') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <input type="text" name="username" id="username" placeholder="Enter username" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div class="mb-4 relative">
                    <input type="password" name="password" id="password" placeholder="Password" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <span class="absolute right-3 top-3 text-gray-400 cursor-pointer">👁️</span>
                </div>
                <div class="text-right text-sm text-white mb-4">
                    <span href="#">Recovery Password</span>
                    @error('failed')
                    <div class="ui negative message">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <button type="submit" class="w-full bg-[#F17141] text-white py-3 rounded-lg hover:bg-red-600 transition">Sign In</button>
            </form>
        </div>
    </div>

    <!-- Ripple Animation CSS -->
    <style>
        body {
            background-color: #FFECAE; /* Keep the original background */
            overflow: hidden;
        }

        /* Ripple container */
        .ripple-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            overflow: hidden;
        }

        /* Ripple effect */
        .ripple {
            position: absolute;
            width: 150px;
            height: 150px;
            background: rgba(194, 148, 86, 0.8); /* More visible, darker than before */
            border-radius: 50%;
            animation: rippleAnimation 7s infinite ease-in-out;
            opacity: 0;
        }

        /* Randomly position each ripple */
        .ripple:nth-child(1) {
            top: 20%;
            left: 30%;
            animation-delay: 0s;
        }
        .ripple:nth-child(2) {
            top: 50%;
            left: 70%;
            animation-delay: 1.5s;
        }
        .ripple:nth-child(3) {
            top: 80%;
            left: 40%;
            animation-delay: 3s;
        }
        .ripple:nth-child(4) {
            top: 30%;
            left: 80%;
            animation-delay: 4.5s;
        }
        .ripple:nth-child(5) {
            top: 70%;
            left: 20%;
            animation-delay: 6s;
        }

        /* Animation: Expanding and Fading Ripples */
        @keyframes rippleAnimation {
            0% {
                transform: scale(0);
                opacity: 0.8; /* More visible */
            }
            50% {
                opacity: 0.3;
            }
            100% {
                transform: scale(5);
                opacity: 0.1; /* Fades but still noticeable */
            }
        }
    </style>
</body>
</html>
