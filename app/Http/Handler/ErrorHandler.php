<?php

declare(strict_types = 1);

namespace App\Http\Handler;

use Plattry\Dispatcher\EventHandlerInterface;
use Plattry\Network\Event\ErrorEvent;

/**
 * Handle network error-event.
 */
class ErrorHandler implements EventHandlerInterface
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return ErrorEvent::class;
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
     * @param ErrorEvent $event
     * @return ErrorEvent
     */
    public function handle(object $event): object
    {
        return $event;
    }
}
