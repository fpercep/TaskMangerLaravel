<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Muestra el panel de super administración.
     */
    public function index(Request $request): View
    {
        // 1. Obtener usuarios con sus conteos
        $users = User::withCount([
            'projects', 
            'tasks', 
            'tasks as pending_tasks_count' => function ($query) {
                $query->whereIn('status', ['pending', 'in_progress']);
            },
            'tasks as completed_tasks_count' => function ($query) {
                $query->where('status', 'completed');
            }
        ])->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'initials' => $user->initials,
                'is_super_admin' => $user->is_super_admin,
                'projects_count' => $user->projects_count,
                'tasks_count' => $user->tasks_count,
                'pending_tasks_count' => $user->pending_tasks_count,
                'completed_tasks_count' => $user->completed_tasks_count,
                'created_at' => $user->created_at->format('Y-m-d'),
            ];
        });

        // 2. Obtener proyectos con sus conteos y el admin (creador)
        $projects = Project::withCount([
                'users',
                'tasks',
                'tasks as pending_tasks_count' => function ($query) {
                    $query->whereIn('status', ['pending', 'in_progress']);
                },
                'tasks as completed_tasks_count' => function ($query) {
                    $query->where('status', 'completed');
                },
            ])
            ->with(['admins' => function ($query) {
                $query->select('users.id', 'users.name', 'users.email');
            }])
            ->get()->map(function ($project) {
                $creator = $project->admins->first();
                
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'description' => $project->description,
                    'color' => $project->color ?? 'bg-orange-500',
                    'users_count' => $project->users_count,
                    'tasks_count' => $project->tasks_count,
                    'pending_tasks_count' => $project->pending_tasks_count,
                    'completed_tasks_count' => $project->completed_tasks_count,
                    'creator' => $creator ? [
                        'name' => $creator->name,
                        'email' => $creator->email,
                    ] : null,
                    'created_at' => $project->created_at->format('Y-m-d'),
                ];
            });

        return view('pages.admin.index', [
            'users' => $users,
            'projects' => $projects,
        ]);
    }
}
