<?php

declare(strict_types = 1);

namespace App\Http\Handler;

use App\Http\Common\JsonResponse;
use Plattry\Dispatcher\EventHandlerInterface;
use Plattry\Http\Handler;
use Plattry\Http\Routing\RouterInterface;
use Plattry\Network\Event\MessageEvent;
use Plattry\Network\Protocol\ProtocolInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * Handle network message-event.
 */
class MessageHandler implements EventHandlerInterface
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param ContainerInterface $container
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return MessageEvent::class;
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
     * @param MessageEvent $event
     * @return MessageEvent
     */
    public function handle(object $event): object
    {
        try {
            $container = clone $this->container;

            // Get request from connection, and back response after handing.
            $protocol = $container->get(ProtocolInterface::class);

            $request = $protocol->getRequestFromConnection($event->getConnection());
            $rule = $container->get(RouterInterface::class)->parse($request);

            $handler = new Handler($rule);
            $handler->setContainer($container);

            $response = $handler->handle($request);
            $protocol->backResponseToConnection($event->getConnection(), $response);
        } catch (\Throwable $t) {
            if (isset($protocol)) {
                $response = JsonResponse::getFactory()->createResponse(500)->withProtocolVersion("1.1");
                $protocol->backResponseToConnection($event->getConnection(), $response);
            } else {
                $event->getConnection()->close();
            }
            
            $this->logger->error($t->getMessage());
        } finally {
            return $event;
        }
    }
}
