<?php

declare(strict_types = 1);

namespace App\Utils;

/**
 * A error code enum instance.
 */
enum ErrorCode: int
{
    /**
     * No error.
     */
    case NONE = 0;

    /**
     * Get the error message.
     * @return string
     */
    public function message(): string
    {
        return match ($this) {
            self::NONE => "OK",
        };
    }
}
