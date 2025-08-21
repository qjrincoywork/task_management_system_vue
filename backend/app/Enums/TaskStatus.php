<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TaskStatus extends Enum
{
    public const PENDING = 'pending';
    public const COMPLETED = 'completed';
}
