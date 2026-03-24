<header class="flex h-[50px] md:h-[52px] bg-white border-b border-gray-200 shrink-0 w-full z-20 transition-all duration-300 relative" style="font-family: 'Inter', sans-serif;">
    
    <!-- LEFT SECTION -->
    <div class="flex items-center h-full min-w-0 flex-1">
        
        <!-- ESTADO EXPANDIDO: Mismo ancho que el sidebar (256px) SIN LA LÍNEA DEL BORDE (border-r) -->
        <div class="hidden md:flex items-center px-4 transition-all duration-300 h-full shrink-0" style="width: 256px; min-width: 256px;">
            
            <button class="flex justify-center items-center w-[30px] h-[30px] rounded-[6px] text-gray-400 hover:text-gray-800 hover:bg-gray-100 transition-colors focus:outline-none" title="Atrás">
                <i data-lucide="chevron-left" class="w-[19px] h-[19px]"></i>
            </button>
            <button class="flex justify-center items-center w-[30px] h-[30px] rounded-[6px] text-gray-400 hover:text-gray-800 hover:bg-gray-100 transition-colors focus:outline-none ml-1" title="Adelante">
                <i data-lucide="chevron-right" class="w-[19px] h-[19px]"></i>
            </button>
            
            <div class="h-4 w-px bg-gray-200 mx-2.5"></div>
            
            <a href="{{ route('dashboard') ?? '#' }}" class="flex justify-center items-center w-[30px] h-[30px] rounded-[6px] text-gray-400 hover:text-gray-800 hover:bg-gray-100 transition-colors focus:outline-none" title="Inicio">
                <i data-lucide="home" class="w-[18px] h-[18px]"></i>
            </a>

        </div>
        
    </div>

    <!-- CENTER SECTION: Título Dashboard (Letra más grande) -->
    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none flex items-center justify-center">
        <!-- Letra aumentada y estilizada -->
        <h1 class="text-[17.5px] sm:text-[19px] font-bold text-gray-900 tracking-tight whitespace-nowrap" style="letter-spacing: -0.02em;">Dashboard</h1>
    </div>

    <!-- RIGHT SECTION: Usuario -->
    <div class="flex items-center justify-end h-full px-4 sm:px-6 min-w-0 flex-1">
        <button class="flex items-center gap-2 p-1 px-2 rounded-[6px] hover:bg-gray-50 transition-colors focus:outline-none cursor-pointer group">
            
            <!-- Avatar gris básico -->
            <div class="w-[26px] h-[26px] rounded-full bg-[#E8E8E8] shrink-0 border border-gray-200 flex items-center justify-center overflow-hidden"></div>
            
            <!-- Nombre y Flecha -->
            <div class="hidden md:flex items-center gap-1.5 min-w-0">
                <span class="text-[13.5px] font-medium text-gray-500 group-hover:text-gray-800 transition-colors truncate">Francisco Pérez</span>
                <i data-lucide="chevron-down" class="w-[15px] h-[15px] text-gray-400 shrink-0"></i>
            </div>
            
        </button>
    </div>

</header>