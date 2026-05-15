<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Services\ProjectMemberService;
use App\Http\Resources\ProjectMemberResource;
use App\Http\Requests\ProjectMember\StoreMemberRequest;
use App\Http\Requests\ProjectMember\UpdateMemberRoleRequest;
use App\Http\Requests\ProjectMember\SyncMembersRequest;
use App\Http\Requests\ProjectMember\BulkDestroyMembersRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectMemberController extends Controller
{
    public function __construct(
        protected ProjectMemberService $memberService
    ) {}

    /**
     * Display a listing of the project members.
     */
    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return ProjectMemberResource::collection($project->users)->response();
    }

    /**
     * Add a single user to the project.
     */
    public function store(StoreMemberRequest $request, Project $project)
    {
        $this->authorize('manageMembers', $project);

        $this->memberService->addMember(
            $project, 
            $request->validated('user_id'), 
            $request->validated('role')
        );

        return $this->respondSuccess($request, 'Miembro agregado correctamente.');
    }

    /**
     * Update a single member's role.
     */
    public function update(UpdateMemberRoleRequest $request, Project $project, User $user)
    {
        $this->authorize('manageMembers', $project);

        $this->memberService->updateMemberRole(
            $project, 
            $user->id, 
            $request->validated('role')
        );

        return $this->respondSuccess($request, 'Rol actualizado correctamente.');
    }

    /**
     * Remove a single user from the project.
     */
    public function destroy(Request $request, Project $project, User $user)
    {
        $this->authorize('manageMembers', $project);

        $this->memberService->removeMember($project, $user->id);

        return $this->respondSuccess($request, 'Miembro eliminado correctamente.');
    }

    /**
     * Add or update multiple members' roles (Bulk).
     */
    public function sync(SyncMembersRequest $request, Project $project)
    {
        $this->authorize('manageMembers', $project);

        $this->memberService->syncMembers($project, $request->validated('users'));

        return $this->respondSuccess($request, 'Miembros sincronizados correctamente.');
    }

    /**
     * Remove multiple users (Bulk).
     */
    public function destroyBulk(BulkDestroyMembersRequest $request, Project $project)
    {
        $this->authorize('manageMembers', $project);

        $this->memberService->removeMembersBulk($project, $request->validated('user_ids'));

        return $this->respondSuccess($request, 'Miembros eliminados correctamente.');
    }

    /**
     * Centraliza la respuesta exitosa.
     */
    protected function respondSuccess($request, string $message)
    {
        if ($request->wantsJson()) {
            return response()->json(['success' => $message]);
        }

        return back()->with('success', $message);
    }
}

