<?php

declare(strict_types = 1);

namespace App\Bootstrap\Handler;

use App\Bootstrap\BootEvent;
use Plattry\Dispatcher\EventHandlerInterface;
use Plattry\Network\Server;

/**
 * Load http server.
 */
class HttpServerHandler implements EventHandlerInterface
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
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function handle(object $event): object
    {
        // Get http configuration.
        $config = $event->getContainer()->get(\Plattry\Config\Repository::class)->get('http');

        // Build http router tree by Attributes.
        foreach ($config['routing']['controller'] as $item) {
            $event->getContainer()->get(\Plattry\Http\Routing\RouterInterface::class)
                ->loadDir($event->getFullPath($item));
        }

        // Register http event handler.
        foreach ($config['server']['handler'] as $item) {
            $handler = $event->getContainer()->get($item);
            $event->getContainer()->get(\Psr\EventDispatcher\ListenerProviderInterface::class)
                ->addListener($handler->getName(), [$handler, "handle"], $handler->getPriority());
        }

        // Run server.
        $server = new Server($config['server']['ip'], $config['server']['port'], $config['server']['transport']);
        $server->setDispatcher($event->getContainer()->get(\Psr\EventDispatcher\EventDispatcherInterface::class));
        $server->setProtocol($event->getContainer()->get(\Plattry\Network\Protocol\ProtocolInterface::class));
        $server->listen();

        return $event;
    }
}
