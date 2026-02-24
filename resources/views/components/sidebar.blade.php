@php
    $currentRoute = request()->route()->getName();
@endphp
<aside
    class="w-64 min-w-[16rem] h-[calc(100vh-64px)] bg-gray-50 border-r border-gray-200 flex flex-col overflow-hidden hidden md:flex">

    <nav class="flex-1 py-6 px-3 space-y-1 flex flex-col overflow-hidden min-h-0">

        <a href="{{ route('dashboard') }}"
            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ $currentRoute === 'dashboard' ? 'bg-orange-400 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i data-lucide="layout-grid"
                class="w-5 h-5 mr-3 {{ $currentRoute === 'dashboard' ? 'text-orange-100' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
            Dashboard
        </a>

        <a href="{{ route('mi-dia') }}"
            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ $currentRoute === 'mi-dia' ? 'bg-orange-400 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <i data-lucide="sun"
                class="w-5 h-5 mr-3 {{ $currentRoute === 'mi-dia' ? 'text-orange-100' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
            Mi Día
        </a>

        <a href="#"
            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900">
            <i data-lucide="list-todo" class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"></i>
            Todas las Tareas
        </a>

        <a href="#"
            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-100 hover:text-gray-900">
            <i data-lucide="calendar" class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-500"></i>
            Calendario
        </a>

        <div class="pt-1 mt-1 border-t border-gray-200"></div>

        <div>
            <div
                class="px-3 py-2 flex items-center justify-between group cursor-pointer text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors">
                <h3 class="text-sm font-semibold flex items-center gap-3">
                    <span
                        class="w-7 h-7 rounded-lg bg-orange-400 flex items-center justify-center text-xs font-bold text-white shadow-sm">E1</span>
                    Mi Equipo 1
                </h3>
                <div class="flex items-center gap-2">
                    <i data-lucide="chevron-down"
                        class="w-4 h-4 text-gray-400 transition-transform group-hover:rotate-180"></i>
                </div>
            </div>
        </div>

        <div class="pt-0.5 flex-1 flex flex-col min-h-0 overflow-hidden">
            <div class="px-3 mb-1 flex items-center justify-between">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    Proyectos
                </h3>
                <button class="text-gray-400 hover:text-orange-500 transition-colors p-1 rounded-md hover:bg-orange-50">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="space-y-0.5 flex-1 overflow-y-auto min-h-0">
                @php
                    $proyectos = ['Proyecto Alpha', 'Campaña Verano', 'Rediseño Web'];
                @endphp
                @foreach ($proyectos as $proyecto)
                    <a href="#"
                        class="group flex items-center justify-between px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-white hover:shadow-sm hover:text-gray-900 transition-all">
                        <div class="flex items-center truncate">
                            <i data-lucide="network" class="w-4 h-4 mr-3 text-gray-400 group-hover:text-orange-400"></i>
                            <span class="truncate">{{ $proyecto }}</span>
                        </div>
                        <button class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-gray-600 p-1">
                            <i data-lucide="more-vertical" class="w-3 h-3"></i>
                        </button>
                    </a>
                @endforeach
            </div>
        </div>
    </nav>

    <div class="p-4 border-t border-gray-200">
        <a href="#" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">
            <i data-lucide="settings-2" class="w-4 h-4 mr-2"></i>
            Configuración
        </a>
    </div>
</aside>