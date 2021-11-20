<?php

declare(strict_types = 1);

namespace App\Http\Handler;

use Plattry\Network\Event\CloseEvent;

/**
 * 客户端连接断开时触发
 */
class CloseHandler
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return CloseEvent::class;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return 1;
    }

    /**
     * @param CloseEvent $event
     * @return CloseEvent
     */
    public function handle(CloseEvent $event): CloseEvent
    {
        return $event;
    }
}
