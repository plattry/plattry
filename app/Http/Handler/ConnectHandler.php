<?php

declare(strict_types = 1);

namespace App\Http\Handler;

use Plattry\Network\Event\ConnectEvent;

/**
 * 客户端连接建立时触发
 */
class ConnectHandler
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return ConnectEvent::class;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return 1;
    }

    /**
     * @param ConnectEvent $event
     * @return ConnectEvent
     */
    public function handle(ConnectEvent $event): ConnectEvent
    {
        return $event;
    }
}
