<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - Task Manager</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        window.AppUserId = {{ auth()->id() ?? 'null' }};
    </script>

    @stack('styles')
</head>

<body class="bg-[#fdfdfd] h-screen flex flex-col overflow-hidden" style="font-family: 'Inter', sans-serif;">

    <!-- Renderizamos solo el header, sin sidebar -->
    <x-partials.header :title="$title ?? 'Administración'" />

    <!-- El contenido ocupa todo el ancho restante -->
    <main class="flex-1 overflow-y-auto p-4 sm:p-6 md:p-8 relative w-full">
        <div class="w-full">
            {{ $slot }}
        </div>
    </main>

    <!-- Modales Globales Esenciales -->
    <x-modals.manage-users />
    <x-modals.save-user />
    <x-modals.delete-user-confirmation />

    @stack('scripts')
</body>

</html>
