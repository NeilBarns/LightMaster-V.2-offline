<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite( ['resources/css/app.css', 'resources/js/app.js'])

    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('imgs/lightmastericon.png') }}">
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

<body class="antialiased flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl p-6"
        style="box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);">
        <div class="flex flex-col justify-center items-center mb-8">
            <img src="{{ asset('imgs/lightmastericon.png') }}" alt="LightMaster" class="h-20 mb-4">
            <h2 class="text-xl font-semibold text-gray-800">LightMaster</h2>
            <p class="text-gray-600 italic">"Lighting the way, the smart way."</p>
        </div>
        <h4 class="ui divider"></h4>
        <form action="{{ route('auth.login') }}" method="POST" class="ui form">
            @csrf
            <div class="field">
                <label for="username">User Name</label>
                <input type="text" name="username" id="username" placeholder="Enter your username" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>
            <div class="flex flex-col mt-6">
                @error('failed')
                <div class="ui negative message">
                    {{ $message }}
                </div>
                @enderror
                <button type="submit" class="ui fluid primary button">
                    Login
                </button>
            </div>
        </form>
    </div>

    <!-- Styling for logo animation -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }

        .ui.form .field label {
            font-weight: bold;
            color: #4a5568;
        }

        .primary.button {
            background-color: #4caf50 !important;
            border-radius: 0.375rem;
            color: white;
        }

        .primary.button:hover {
            background-color: #388e3c !important;
        }

        .shadow-md {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .bg-gray-100 {
            background-color: #f7fafc;
        }

        /* Add animation to the logo */
        .h-20 {
            transition: transform 0.3s ease-in-out;
        }

        .h-20:hover {
            transform: scale(1.1);
        }
    </style>
</body>

</html>