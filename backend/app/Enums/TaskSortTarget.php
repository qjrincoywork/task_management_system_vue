<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Represents a task sort target.
 *
 * This class defines constants for the possible sort targets of a task.
 * The available sort targets are:
 * - TITLE: Sort by task title.
 * - DATE_CREATED: Sort by task creation date.
 */
final class TaskSortTarget extends Enum
{
    public const TITLE = 'title';
    public const DATE_CREATED = 'created_at';
}
