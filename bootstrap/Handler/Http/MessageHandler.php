<?php

declare(strict_types = 1);

namespace Bootstrap\Handler\Http;

use Plattry\Event\Dispatch\HandlerInterface;
use Plattry\Event\Network\Event\MessageEvent;
use Plattry\Kit\Http\Foundation\HttpFactory;
use Plattry\Kit\Http\Handler;
use Plattry\Kit\Route\RouterInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * A http-message handler instance.
 */
class MessageHandler implements HandlerInterface
{
    /**
     * The container instance.
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * The router instance.
     * @var RouterInterface
     */
    protected RouterInterface $router;

    /**
     * The logger instance.
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * The constructor.
     * @param ContainerInterface $container
     * @param RouterInterface $router
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerInterface $container, RouterInterface $router, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->router = $router;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return MessageEvent::class;
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return 1;
    }

    /**
     * @inheritDoc
     * @param MessageEvent $event
     */
    public function handle(object $event): object
    {
        try {
            /**
             * @var ServerRequestInterface $request
             */
            $request = $event->getData();

            $rule = $this->router->parse(strtolower($request->getMethod()).$request->getRequestTarget());
            if ($rule === null) {
                $response = (new HttpFactory())->createResponse(404);
                $event->getConnection()->close($response);
            } else {
                $handler = new Handler($rule->getMiddlewares(), $rule->getTarget());
                $handler->setContainer(clone $this->container);
                $response = $handler->handle($request);
                $event->getConnection()->send($response);
            }
        } catch (Throwable $t) {
            $this->logger->notice("%message%, %trace%", ["message" => $t->getMessage(), "trace" => $t->getTraceAsString()]);

            $response = (new HttpFactory())->createResponse(500);
            $event->getConnection()->close($response);
        } finally {
            return $event;
        }
    }
}
