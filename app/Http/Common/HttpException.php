<?php

declare(strict_types = 1);

namespace App\Http\Common;

/**
 * Http exception.
 */
final class HttpException extends \RuntimeException
{
    /**
     * @param string $message
     * @return static
     */
    public static function ArgMissing(string $message): self
    {
        return new self($message, ErrorCode::ArgMissing->value);
    }

    /**
     * @param string $message
     * @return static
     */
    public static function ArgTypeErr(string $message): self
    {
        return new self($message, ErrorCode::ArgTypeErr->value);
    }

    /**
     * @param string $message
     * @return static
     */
    public static function ArgInvalid(string $message): self
    {
        return new self($message, ErrorCode::ArgInvalid->value);
    }

    /**
     * @param string $message
     * @return static
     */
    public static function Unknown(string $message): self
    {
        return new self($message, ErrorCode::Unknown->value);
    }
}
