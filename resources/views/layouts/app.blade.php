<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="bg-white h-screen flex flex-col overflow-hidden" style="font-family: 'Inter', sans-serif;">

    <x-partials.header />

    <div class="flex flex-1 overflow-hidden">

        <x-partials.sidebar />

        <main class="flex-1 overflow-y-auto bg-white p-8 relative">
            <div class="max-w-full mx-auto">
                {{ $slot }}
            </div>
        </main>

    </div>

    <!-- Global Modals -->
    <x-modals.create-project />
    <x-modals.edit-project />
    <x-modals.delete-project-confirmation />

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>

    @stack('scripts')
</body>

</html>
