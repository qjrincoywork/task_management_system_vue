<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    /**
     * Get the tasks associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Returns an array containing task statistics for the user.
     *
     * The returned array will contain the following keys:
     * - total: The total number of tasks the user has.
     * - completed: The number of tasks the user has completed.
     * - pending: The number of tasks the user has pending.
     *
     * @return array
     */
    public function getTaskStats(): array
    {
        return [
            'total' => $this->tasks()->count(),
            'completed' => $this->tasks()->byStatus(TaskStatus::COMPLETED)->count(),
            'pending' => $this->tasks()->byStatus(TaskStatus::PENDING)->count(),
        ];
    }

    /**
     * Method: registerUser
     * This method is used to register a new user.
     * @param array $data The data for the new user.
     *
     * @return self The newly created user instance.
     */
    public function registerUser(array $data): self
    {
        return (new User)->create($data);
    }

    /**
     * Scope a query to search for users by name or email.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, $search)
    {
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        return $query;
    }
}
