<x-app-layout>
    <div class="max-w-full mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            @foreach ($estadisticas as $stat)
                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-{{ $stat['color'] }}-50 rounded-lg text-{{ $stat['color'] }}-500">
                            <i data-lucide="{{ $stat['icono'] }}" class="size-icon-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ $stat['titulo'] }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stat['valor'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

        <div
            class="border-2 border-dashed border-gray-200 rounded-xl h-64 flex flex-col items-center justify-center text-gray-400">
            <i data-lucide="bar-chart-3" class="size-icon-avatar mb-3 text-gray-300"></i>
            <p class="text-sm">Aquí irían las gráficas de rendimiento general.</p>
        </div>
    </div>
</x-app-layout>