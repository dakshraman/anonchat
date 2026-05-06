<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AnonymousChat')</title>
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
                        primary: '#a78bfa',
                        secondary: '#c4b5fd',
                        darkbg: '#13111C',
                        panel: '#1E1B2E',
                        panelhover: '#2A2640',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @yield('styles')
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Custom Scrollbar for a more app-like feel */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #2A2640; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #4c1d95; }
    </style>
</head>
<body class="bg-darkbg text-gray-100 min-h-screen antialiased selection:bg-lavender-500 selection:text-white flex flex-col">
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/echo@1.22.0/dist/echo.min.js"></script>
    <script>
        window.Echo = new Echo({
            broadcaster: 'reverb',
            wsHost: '127.0.0.1',
            wsPort: 8080,
            wssPort: 8080,
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
