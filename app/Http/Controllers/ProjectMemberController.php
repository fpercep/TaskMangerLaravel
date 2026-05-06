<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProjectMemberController extends Controller
{
    /**
     * Display a listing of the project members.
     */
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $members = $project->users()->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'initials' => $user->initials,
                'email' => $user->email,
                'role' => $user->pivot->role,
            ];
        });

        return response()->json(['data' => $members]);
    }

    /**
     * Add a single user to the project.
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:viewer,editor,manager,admin',
        ]);

        $changes = $project->users()->syncWithoutDetaching([
            $validated['user_id'] => ['role' => $validated['role']]
        ]);

        if (empty($changes['attached'])) {
            $message = 'El usuario ya es miembro de este proyecto.';
            return $request->wantsJson() 
                ? response()->json(['error' => $message], 422) 
                : back()->with('error', $message);
        }

        Cache::forget(User::getSidebarCacheKeyForId($validated['user_id']));

        $success = 'Usuario añadido correctamente.';
        return $request->wantsJson() 
            ? response()->json(['success' => $success]) 
            : back()->with('success', $success);
    }

    /**
     * Update a single member's role.
     */
    public function update(Request $request, Project $project, User $user)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'role' => 'required|in:viewer,editor,manager,admin',
        ]);

        $affectedRows = $project->users()->updateExistingPivot($user->id, ['role' => $validated['role']]);

        if ($affectedRows > 0) {
            Cache::forget(User::getSidebarCacheKeyForId($user->id));
            $success = 'Rol actualizado correctamente.';
            return $request->wantsJson() 
                ? response()->json(['success' => $success]) 
                : back()->with('success', $success);
        }

        $info = 'El usuario ya tenía asignado este rol.';
        return $request->wantsJson() 
            ? response()->json(['info' => $info]) 
            : back()->with('info', $info);
    }

    /**
     * Remove a single user from the project.
     */
    public function destroy(Request $request, Project $project, User $user)
    {
        $this->authorize('update', $project);

        if ($user->id === Auth::id()) {
            $error = 'No puedes eliminarte a ti mismo del proyecto.';
            return $request->wantsJson() 
                ? response()->json(['error' => $error], 403) 
                : back()->with('error', $error);
        }

        $project->users()->detach($user->id);

        Cache::forget(User::getSidebarCacheKeyForId($user->id));

        $success = 'Usuario eliminado del proyecto.';
        return $request->wantsJson() 
            ? response()->json(['success' => $success]) 
            : back()->with('success', $success);
    }

    /**
     * Add or update multiple members' roles (Bulk).
     */
    public function sync(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'users' => 'required|array',
            'users.*.user_id' => 'required|exists:users,id',
            'users.*.role' => 'required|in:viewer,editor,manager,admin',
        ]);

        $syncData = [];
        foreach ($validated['users'] as $userData) {
            $syncData[$userData['user_id']] = ['role' => $userData['role']];
        }

        $changes = $project->users()->syncWithoutDetaching($syncData);

        $idsToClearCache = array_merge($changes['attached'], $changes['updated']);

        if (empty($idsToClearCache)) {
            $info = 'No se realizaron cambios. Los usuarios ya existían con los mismos roles.';
            return $request->wantsJson() 
                ? response()->json(['info' => $info]) 
                : back()->with('info', $info);
        }

        foreach ($idsToClearCache as $id) {
            Cache::forget(User::getSidebarCacheKeyForId($id));
        }

        $success = 'Usuarios procesados correctamente.';
        return $request->wantsJson() 
            ? response()->json(['success' => $success]) 
            : back()->with('success', $success);
    }

    /**
     * Remove multiple users (Bulk).
     */
    public function destroyBulk(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $idsToRemove = array_diff($validated['user_ids'], [Auth::id()]);

        if (empty($idsToRemove)) {
            $error = 'No se han podido eliminar los usuarios seleccionados.';
            return $request->wantsJson() 
                ? response()->json(['error' => $error], 422) 
                : back()->with('error', $error);
        }

        $project->users()->detach($idsToRemove);

        foreach ($idsToRemove as $id) {
            Cache::forget(User::getSidebarCacheKeyForId($id));
        }

        $success = 'Usuarios eliminados correctamente.';
        return $request->wantsJson() 
            ? response()->json(['success' => $success]) 
            : back()->with('success', $success);
    }
}
