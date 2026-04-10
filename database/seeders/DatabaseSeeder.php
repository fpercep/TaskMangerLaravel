<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Team;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear el usuario de prueba
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // 2. Crear equipos y vincular al usuario
        $teams = Team::factory(2)->create();
        foreach ($teams as $team) {
            $user->teams()->attach($team->id, ['role' => 'owner']);
        }

        // 3. Crear proyectos para los equipos
        foreach ($teams as $team) {
            $projects = Project::factory(2)->create(['team_id' => $team->id]);
            
            // Vincular usuario a proyectos
            foreach ($projects as $project) {
                $user->projects()->attach($project->id, ['role' => 'admin']);
                
                // 4. Crear tareas para cada proyecto
                $tasks = Task::factory(5)->create(['project_id' => $project->id]);
                
                // Asignar tareas al usuario
                foreach ($tasks as $task) {
                    $user->tasks()->attach($task->id);
                }
            }
        }

        // 5. Crear algunas tareas personales (sin proyecto)
        $personalTasks = Task::factory(3)->create([
            'project_id' => null,
            'name' => 'Tarea Personal ' . rand(1, 100),
        ]);
        foreach ($personalTasks as $task) {
            $user->tasks()->attach($task->id);
        }
    }
}
