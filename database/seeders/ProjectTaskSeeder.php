<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

class ProjectTaskSeeder extends Seeder
{
    public function run(): void
    {
        // Utiliza firstOrFail() para arrojar una excepción clara si faltan dependencias, 
        // evitando el "fallo silencioso" del return.
        $user1 = User::where('email', 'test@example.com')->firstOrFail();
        $user2 = User::where('email', 'other@example.com')->firstOrFail();

        $projects = Project::factory(3)->create();

        // Extraemos los proyectos de forma segura usando los métodos de la Collection
        $p1 = $projects->shift();
        $p2 = $projects->shift();
        $p3 = $projects->shift();

        // Proyecto 1: Asignado a AMBOS usuarios
        $user1->projects()->attach($p1->id, ['role' => 'admin']);
        $user2->projects()->attach($p1->id, ['role' => 'editor']);

        Task::factory(2)->create(['project_id' => $p1->id, 'assigned_user_id' => $user1->id, 'name' => "Tarea de {$user1->name} en P1"]);
        Task::factory(2)->create(['project_id' => $p1->id, 'assigned_user_id' => $user2->id, 'name' => "Tarea de {$user2->name} en P1"]);

        // Proyecto 2: Solo Usuario 1
        $user1->projects()->attach($p2->id, ['role' => 'admin']);
        Task::factory(3)->create(['project_id' => $p2->id, 'assigned_user_id' => $user1->id, 'name' => "Tarea exclusiva de {$user1->name}"]);

        // Proyecto 3: Solo Usuario 2
        $user2->projects()->attach($p3->id, ['role' => 'admin']);
        Task::factory(3)->create(['project_id' => $p3->id, 'assigned_user_id' => $user2->id, 'name' => "Tarea exclusiva de {$user2->name}"]);
    }
}
