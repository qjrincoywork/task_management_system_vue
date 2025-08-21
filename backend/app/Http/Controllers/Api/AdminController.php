<?php

namespace App\Http\Controllers\Api;

use App\Enums\{TaskStatus, TaskPriority};
use App\Http\Controllers\Controller;
use App\Http\Requests\{UserRequest, UserSearchRequest};
use App\Http\Resources\UserResource;
use App\Models\{User, Task};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    /**
     * Return statistics for the admin dashboard.
     *
     * The statistics are returned in the following format:
     *
     * [
     *     'message' => 'Admin dashboard data retrieved successfully',
     *     'stats' => [
     *         'total_users' => int,
     *         'total_tasks' => int,
     *         'completed_tasks' => int,
     *         'pending_tasks' => int,
     *         'tasks_by_priority' => [
     *             'low' => int,
     *             'medium' => int,
     *             'high' => int,
     *         ],
     *     ],
     * ]
     *
     * @return JsonResponse
     */
    public function dashboard(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'total_tasks' => Task::count(),
            'completed_tasks' => Task::byStatus(TaskStatus::COMPLETED)->count(),
            'pending_tasks' => Task::byStatus(TaskStatus::PENDING)->count(),
            'tasks_by_priority' => [
                'low' => Task::byPriority(TaskPriority::LOW)->count(),
                'medium' => Task::byPriority(TaskPriority::MEDIUM)->count(),
                'high' => Task::byPriority(TaskPriority::HIGH)->count(),
            ],
        ];

        return response()->json(['stats' => $stats]);
    }

    /**
     * Retrieves a list of users filtered by the given search query.
     * The results are paginated with the given page size.
     *
     * @param UserSearchRequest $request The request instance with validated search query and per_page parameters.
     * @return AnonymousResourceCollection The collection of user resources.
     */
    public function users(UserSearchRequest $request): AnonymousResourceCollection
    {
        $users = User::with('tasks')
            ->filter($request->search)
            ->paginate($request->per_page ?? config('tms.default_per_page_count'));

        return UserResource::collection($users);
    }

    /**
     * Retrieves a user's tasks and task statistics.
     *
     * @param int $id The ID of the user to retrieve tasks for.
     *
     * @return JsonResponse The response containing the user's tasks and task statistics.
     */
    public function userTasks(UserRequest $request): JsonResponse
    {
        $user = User::findOrFail($request->id);
        $tasks = $user->tasks()->ordered()->get();
        $stats = $user->getTaskStats();

        return response()->json([
            'message' => 'User tasks retrieved successfully',
            'user' => new UserResource($user),
            'tasks' => $tasks,
            'stats' => $stats,
        ]);
    }

    public function deleteUser(UserRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($request->id);
            // Check if the user is an admin, bail early if so
            if ($user->is_admin) {
                return response()->json([
                    'message' => 'Cannot delete admin user',
                ], Response::HTTP_FORBIDDEN);
            }

            $user->delete();

            // Commit transaction
            DB::commit();

            return response()->json([
                'message' => 'User deleted successfully',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Catch and handle any unexpected errors
            DB::rollBack();

            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
