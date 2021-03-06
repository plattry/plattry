<?php

declare(strict_types = 1);

namespace App\Http\Handler;

use Plattry\Dispatcher\EventHandlerInterface;
use Plattry\Network\Event\ConnectEvent;

/**
 * Handle network connect-event.
 */
class ConnectHandler implements EventHandlerInterface
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return ConnectEvent::class;
    }

    /**
     * @inheritdoc
     */
    public function getPriority(): int
    {
        return 1;
    }

    /**
     * @inheritdoc
     * @param ConnectEvent $event
     * @return ConnectEvent
     */
    public function handle(object $event): object
    {
        return $event;
    }
}
