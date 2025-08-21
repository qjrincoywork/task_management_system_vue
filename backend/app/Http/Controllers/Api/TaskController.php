<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\{DeleteTaskRequest, StoreTaskRequest, TaskListRequest, UpdateTaskRequest};
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    /**
     * Task model instance.
     *
     * @var Task
     */
    protected $task;

    /**
     * TaskController constructor.
     *
     * @param Task $task
     *
     * @return void
     */
    public function __construct(Task $task) {
        $this->task = $task;
    }

    /**
     * Display a listing of the tasks.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(TaskListRequest $request): AnonymousResourceCollection
    {
        $tasks = $this->task->getTasks($request->validated());

        return TaskResource::collection($tasks);
    }

    /**
     * Create a new task in the database.
     *
     * @param StoreTaskRequest $request
     * @return JsonResponse
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $task = $this->task->saveUserTask($request->validated());
            $this->clearUserTaskCache(auth()->id());

            // Commit transaction
            DB::commit();

            return response()->json([
                'message' => 'Task created successfully',
                'task' => new TaskResource($task)
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Catch and handle any unexpected errors
            DB::rollBack();

            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified task.
     *
     * @param int $id The ID of the task to retrieve.
     * @return TaskResource The task resource instance.
     */
    public function show(int $id): TaskResource
    {
        return new TaskResource(auth()->user()->tasks()->findOrFail($id));
    }

    /**
     * Update the specified task in the database.
     *
     * @param UpdateTaskRequest $request The request instance with validated task data.
     * @return JsonResponse The response with the updated task instance.
     */
    public function update(UpdateTaskRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $task = $this->task->saveUserTask($request->validated());
            $this->clearUserTaskCache(auth()->id());

            // Commit transaction
            DB::commit();

            return response()->json(['message' => 'Task updated successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Catch and handle any unexpected errors
            DB::rollBack();

            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete the specified task in the database.
     *
     * @param DeleteTaskRequest $request The request instance with validated task ID.
     * @return JsonResponse The response with the deletion message.
     */
    public function destroy(DeleteTaskRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            auth()->user()->tasks()->find($request['id'])->delete();
            $this->clearUserTaskCache(auth()->id());

            // Commit transaction
            DB::commit();

            return response()->json(['message' => __('Task deleted successfully.')], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Catch and handle any unexpected errors
            DB::rollBack();

            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Reorders the tasks in the database.
     *
     * @param Request $request The request containing the tasks to reorder.
     *                         The tasks should be an array of objects with 'id' and 'order' properties.
     * @return JsonResponse The response with the reorder message.
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:tasks,id',
            'tasks.*.order' => 'required|integer|min:0',
        ]);

        $userId = $request->user()->id;

        DB::transaction(function () use ($request, $userId) {
            foreach ($request->tasks as $taskData) {
                $task = Task::where('id', $taskData['id'])
                    ->where('user_id', $userId)
                    ->first();

                if ($task) {
                    $task->update(['order' => $taskData['order']]);
                }
            }
        });

        $this->clearUserTaskCache($userId);

        return response()->json(['message' => 'Tasks reordered successfully']);
    }

    /**
     * Toggles the status of a task between pending and completed.
     *
     * @param Task $task The task to toggle the status of.
     * @return JsonResponse The response with the updated task and a success message.
     */
    public function toggleStatus(Task $task): JsonResponse
    {
        $task->update([
            'status' => $task->status === TaskStatus::PENDING
                ? TaskStatus::COMPLETED
                : TaskStatus::PENDING
        ]);

        $this->clearUserTaskCache($task->user_id);

        return response()->json([
            'message' => 'Task status updated successfully',
            'task' => new TaskResource($task)
        ]);
    }

    /**
     * Clears the cache for the given user's tasks.
     *
     * This is necessary when the user's tasks are updated, so that the updated
     * tasks are reflected in the cached results.
     *
     * @param int $userId The ID of the user whose tasks should be cleared from the cache.
     */
    private function clearUserTaskCache(int $userId): void
    {
        $cacheKeys = [
            "tasks_user_{$userId}_status_all_priority_all_search_",
            "tasks_user_{$userId}_status_pending_priority_all_search_",
            "tasks_user_{$userId}_status_completed_priority_all_search_",
            "tasks_user_{$userId}_status_all_priority_low_search_",
            "tasks_user_{$userId}_status_all_priority_medium_search_",
            "tasks_user_{$userId}_status_all_priority_high_search_",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }
}
