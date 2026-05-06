<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AnonymousChat')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        lavender: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                            950: '#2e1065',
                        },
                        darkbg: '#0F0E17',
                        panel: '#1A1826',
                        panelhover: '#242133',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            background-color: #0F0E17;
            color: #F9FAFB;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            min-height: -webkit-fill-available;
        }
        html {
            height: -webkit-fill-available;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #2D2A45; border-radius: 10px; }
        
        /* Glass Effect */
        .glass {
            background: rgba(26, 24, 38, 0.8);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(167, 139, 250, 0.1);
        }
    </style>
    @yield('styles')
</head>
<body class="antialiased selection:bg-lavender-500 selection:text-white overflow-x-hidden">
    @yield('content')
    
    <script src="https://cdn.jsdelivr.net/npm/echo@1.22.0/dist/echo.min.js"></script>
    <script>
        window.Echo = new Echo({
            broadcaster: 'reverb',
            wsHost: window.location.hostname,
            wsPort: {{ config('reverb.apps.common.port', 8080) }},
            wssPort: {{ config('reverb.apps.common.port', 8080) }},
            forceTLS: window.location.protocol === 'https:',
            enabledTransports: ['ws', 'wss'],
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }
        });
    </script>
    @yield('scripts')
</body>
</html>