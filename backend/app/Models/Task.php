<?php

namespace App\Models;

use App\Enums\SortOrder;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'order',
        'user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'status' => 'string',
        'priority' => 'string',
        'order' => 'integer',
        'user_id' => 'integer',
    ];

    /**
     * Get the attributes that should be hidden for arrays.
     *
     * @return array<string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Define an inverse one-to-one or many relationship with the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to filter tasks by their status.
     *
     * @param Builder $query The query builder instance.
     * @param string $status The status to filter tasks by. If 'all', no filtering is applied.
     * @return void
     */
    public function scopeByStatus(Builder $query, string $status): void
    {
        if ($status !== 'all') {
            $query->where('status', $status);
        }
    }

    /**
     * Scope a query to only include tasks of given priority.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $priority
     * @return void
     */
    public function scopeByPriority(Builder $query, string $priority): void
    {
        if ($priority !== 'all') {
            $query->where('priority', $priority);
        }
    }

    /**
     * Scope a query to only include tasks by given user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return void
     */
    public function scopeByUser(Builder $query, int $userId): void
    {
        $query->where('user_id', $userId);
    }

    /**
     * Sort tasks by order asc.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('order', SortOrder::ASC);
    }

    /**
     * Retrieves tasks for given user with given status, priority and search query.
     * The results are cached for given amount of time.
     *
     * @param array $params Parameters for the query.
     *                      - status: the status to filter tasks by. If 'all', no filtering is applied.
     *                      - priority: the priority to filter tasks by. If 'all', no filtering is applied.
     *                      - search: the search query to filter tasks by.
     *                      - per_page: the number of tasks to retrieve per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTasks(array $params)
    {
        $userId = auth()->id();
        $status = $params['status'] ?? 'all';
        $priority = $params['priority'] ?? 'all';
        $search = $params['search'] ?? '';
        $perPage = $params['per_page'] ?? config('tms.default_per_page_count');
        $cacheKey = "tasks_user_{$userId}_status_{$status}_priority_{$priority}_search_{$search}";

        $tasks = Cache::remember($cacheKey, config('tms.cache_ttl'), function () use ($userId, $status, $priority, $search, $perPage) {
            return Task::byUser($userId)
                ->byStatus($status)
                ->byPriority($priority)
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where("title", "like", "%{$search}%")
                          ->orWhere("description", "like", "%{$search}%");
                    });
                })
                ->ordered()
                ->paginate($perPage);
        });

        return $tasks;
    }

    /**
     * Saves a user task.
     * If the task data contains an 'id' field, it updates the corresponding task in the database.
     * Otherwise, it creates a new task with the provided data and assigns it to the current user.
     * @param array $data The task data.
     *      - id (int|null): The ID of the task to update. Defaults to null.
     *      - Any other task fields to update or create.
     *
     * @return mixed The updated or newly created task instance.
     * @throws \Exception If the task could not be saved.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the task with the given ID
     */
    public function saveUserTask(array $data)
    {
        if (!isset($data["order"])) {
            $maxOrder = Task::where('user_id', auth()->id())->max("order") ?? 0;
            $data["order"] = $maxOrder + 1;
        }

        if (isset($data['id'])) {
            return auth()->user()->tasks()->find($data['id'])->update($data);
        } else {
            $taskData = $data + ['user_id' => auth()->id()];
            return auth()->user()->tasks()->create($taskData);
        }
    }
}
