<?php

declare(strict_types = 1);

namespace App\Bootstrap\Handler;

use App\Bootstrap\BootEvent;
use Plattry\Dispatcher\EventHandlerInterface;
use Plattry\Log\LoggerFactory;

/**
 * Load logger.
 */
class LoggerHandler implements EventHandlerInterface
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return BootEvent::class;
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
     * @param BootEvent $event
     * @return BootEvent
     */
    public function handle(object $event): object
    {
        $event->getContainer()->set(\Psr\Log\LoggerInterface::class, LoggerFactory::createStdLogger());

        return $event;
    }
}
