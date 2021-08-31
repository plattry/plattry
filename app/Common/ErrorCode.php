<?php

declare(strict_types = 1);

namespace App\Common;

/**
 * 错误码及原因
 * Class ErrorCode
 * @package App\Common
 */
class ErrorCode
{
    /**
     * 错误码：成功
     * @var int
     */
    public const SUCCESS = 0;

    /**
     * 错误码默认原因
     * @var array
     */
    protected static array $phrase = [
        self::SUCCESS => "OK"
    ];

    /**
     * 获取错误码默认原因
     * @param int $code
     * @return string
     */
    public static function phrase(int $code): string
    {
        !isset(static::$phrase[$code]) &&
        throw new \InvalidArgumentException("Unknown error code.");

        return static::$phrase[$code];
    }
}
