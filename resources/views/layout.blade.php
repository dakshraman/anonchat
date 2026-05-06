<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AnonChat')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                    }
                }
            }
        }
    </script>
    @yield('styles')
</head>
<body class="bg-gray-900 text-white min-h-screen">
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