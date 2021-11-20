<?php

declare(strict_types = 1);

namespace App\Http\Handler;

use Plattry\Http\Handler;
use Plattry\Http\Routing\RouterInterface;
use Plattry\Network\Event\MessageEvent;
use Plattry\Network\Protocol\ProtocolInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * 接收到Http包时触发
 */
class MessageHandler
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
     * @return string
     */
    public function getName(): string
    {
        return MessageEvent::class;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return 1;
    }

    /**
     * @param MessageEvent $event
     * @return MessageEvent
     */
    public function handle(MessageEvent $event): MessageEvent
    {
        try {
            $container = clone $this->container;

            $protocol = $container->get(ProtocolInterface::class);

            $request = $protocol->getRequestFromConnection($event->getConnection());
            $rule = $container->get(RouterInterface::class)->parse($request);

            $handler = new Handler($rule);
            $handler->setContainer($container);

            $response = $handler->handle($request);
            $protocol->backResponseToConnection($event->getConnection(), $response);
        } catch (\Throwable $t) {
            $event->getConnection()->close();
            $this->logger->error($t->getMessage());
        } finally {
            return $event;
        }
    }
}
