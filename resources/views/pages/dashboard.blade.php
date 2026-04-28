<x-app-layout>
    <div class="max-w-full mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            @foreach ($estadisticas as $stat)
                <x-ui.stat-card 
                    :titulo="$stat['titulo']"
                    :valor="$stat['valor']"
                    :icono="$stat['icono']"
                    :bg-class="$stat['bg_class']"
                    :text-class="$stat['text_class']"
                />
            @endforeach

        </div>

        <div
            class="border-2 border-dashed border-gray-200 rounded-xl h-64 flex flex-col items-center justify-center text-gray-400">
            <x-lucide-bar-chart-3 class="size-icon-avatar mb-3 text-gray-300" />
            <p class="text-sm">Aquí irían las gráficas de rendimiento general.</p>
        </div>
    </div>
</x-app-layout>