<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function view(User , Task ): bool
    {
        return ->id === ->user_id || ->isAdmin();
    }

    public function create(User ): bool
    {
        return true;
    }

    public function update(User , Task ): bool
    {
        return ->id === ->user_id || ->isAdmin();
    }

    public function delete(User , Task ): bool
    {
        return ->id === ->user_id || ->isAdmin();
    }
}
