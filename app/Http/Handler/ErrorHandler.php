<?php

declare(strict_types = 1);

namespace App\Http\Handler;

use Plattry\Network\Event\ErrorEvent;

/**
 * 连接异常时触发
 */
class ErrorHandler
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return ErrorEvent::class;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return 1;
    }

    /**
     * @param ErrorEvent $event
     * @return ErrorEvent
     */
    public function handle(ErrorEvent $event): ErrorEvent
    {
        return $event;
    }
}
