<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TaskPriority extends Enum
{
    public const LOW = 'low';
    public const MEDIUM = 'medium';
    public const HIGH = 'high';
}
