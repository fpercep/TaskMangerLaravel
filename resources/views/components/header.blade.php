<header class="flex items-center h-16 bg-white border-b border-gray-200 shrink-0 relative z-20">

    <div class="w-64 min-w-[16rem] h-full flex items-center px-4 border-r border-gray-100 hidden md:flex">
        <button
            class="w-full flex items-center justify-center gap-2 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 rounded-md py-2 px-3 transition-colors text-sm font-medium shadow-sm">
            <i data-lucide="plus" class="w-4 h-4 text-orange-500"></i>
            <span>Añadir Tarea</span>
        </button>
    </div>

    <div class="flex-1 flex items-center justify-between px-6 h-full">

        <div class="relative w-full max-w-xl mx-auto md:mx-0">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
            </div>
            <input type="text"
                class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-orange-200 focus:border-orange-400 sm:text-sm transition-all"
                placeholder="Buscar... ">
        </div>

        <div
            class="flex items-center gap-3 ml-4 cursor-pointer hover:bg-gray-50 p-1.5 rounded-lg transition-colors group">
            <div
                class="h-9 w-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 overflow-hidden border border-gray-200 group-hover:border-gray-300">
                <i data-lucide="user" class="w-5 h-5"></i>
            </div>
            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 group-hover:text-gray-600"></i>
        </div>
    </div>
</header>