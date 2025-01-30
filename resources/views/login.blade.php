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
<body class="bg-[#FFECAE] flex items-center justify-center h-screen">
    <div class="max-w-7xl w-full bg-white p-8 rounded-lg shadow-2xl flex">
        <div class="w-1/2 flex justify-center items-center rounded-lg p-6 bg-gray-50">
            {{-- <img src="{{ asset('imgs/sapiens.png') }}" alt="sapiens" class="w-full"> --}}
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
</body>
</html>
