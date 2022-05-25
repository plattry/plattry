<?php

declare(strict_types = 1);

namespace App\Http\Handler;

use Plattry\Dispatcher\EventHandlerInterface;
use Plattry\Network\Event\CloseEvent;

/**
 * Handle network close-event.
 */
class CloseHandler implements EventHandlerInterface
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return CloseEvent::class;
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
     * @param CloseEvent $event
     * @return CloseEvent
     */
    public function handle(object $event): object
    {
        return $event;
    }
}
