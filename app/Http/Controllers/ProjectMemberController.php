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
    protected ProjectMemberService $memberService;

    public function __construct(ProjectMemberService $memberService)
    {
        $this->memberService = $memberService;
    }

    /**
     * Display a listing of the project members.
     */
    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        return ProjectMemberResource::collection($project->users()->get())->response();
    }

    /**
     * Add a single user to the project.
     */
    public function store(StoreMemberRequest $request, Project $project)
    {
        $this->authorize('manageMembers', $project);

        $validated = $request->validated();

        $result = $this->memberService->addMember($project, $validated['user_id'], $validated['role']);

        return $this->handleResponse($request, $result);
    }

    /**
     * Update a single member's role.
     */
    public function update(UpdateMemberRoleRequest $request, Project $project, User $user)
    {
        $this->authorize('manageMembers', $project);

        $validated = $request->validated();

        $result = $this->memberService->updateMemberRole($project, $user->id, $validated['role']);

        return $this->handleResponse($request, $result);
    }

    /**
     * Remove a single user from the project.
     */
    public function destroy(Request $request, Project $project, User $user)
    {
        $this->authorize('manageMembers', $project);

        $result = $this->memberService->removeMember($project, $user->id);

        return $this->handleResponse($request, $result);
    }

    /**
     * Add or update multiple members' roles (Bulk).
     */
    public function sync(SyncMembersRequest $request, Project $project)
    {
        $this->authorize('manageMembers', $project);

        $validated = $request->validated();

        $result = $this->memberService->syncMembers($project, $validated['users']);

        return $this->handleResponse($request, $result);
    }

    /**
     * Remove multiple users (Bulk).
     */
    public function destroyBulk(BulkDestroyMembersRequest $request, Project $project)
    {
        $this->authorize('manageMembers', $project);

        $validated = $request->validated();

        $result = $this->memberService->removeMembersBulk($project, $validated['user_ids']);

        return $this->handleResponse($request, $result);
    }

    /**
     * Handle the response based on the result array from service.
     */
    protected function handleResponse(Request $request, array $result)
    {
        $status = $result['status'] ?? (isset($result['error']) ? 400 : 200);
        unset($result['status']);

        if ($request->wantsJson()) {
            return response()->json($result, $status);
        }

        $type = isset($result['error']) ? 'error' : (isset($result['success']) ? 'success' : array_key_first($result));
        return back()->with($type, $result[$type] ?? '');
    }
}

