<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.partials.sidebar', function ($view) {
            $proyectosSidebar = collect([]);
            
            if (Auth::check()) {
                $user = Auth::user();
                $cacheKey = $user->sidebarCacheKey();
                
                $proyectosSidebar = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($user) {
                    // Obtener proyectos directamente cargados por el usuario
                    $proyectos = $user->projects()->get();
                    $colores = ['bg-emerald-400', 'bg-indigo-400', 'bg-orange-400', 'bg-rose-400', 'bg-sky-400'];
                    
                    // Mapeo puro (transformación de datos antes de inyectar) -> Regla cumplida
                    return $proyectos->map(function ($project, $index) use ($colores) {
                        return (object) [
                            'id' => $project->id,
                            'name' => $project->name,
                            'description' => $project->description,
                            'color' => $colores[$index % count($colores)]
                        ];
                    });
                });
            }

            $view->with('proyectosSidebar', $proyectosSidebar);
        });
    }
}
