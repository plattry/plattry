<?php

declare(strict_types = 1);

namespace Bootstrap\Handler;

use Bootstrap\BootEvent;
use Plattry\Event\Dispatch\HandlerInterface;
use Plattry\Event\Network\Server;
use Plattry\Event\Notification\LooperInterface;
use Plattry\Kit\Config\RepositoryInterface;
use Plattry\Kit\Http\Protocol;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * A http initialization handler instance.
 */
class HttpHandler implements HandlerInterface
{
    /**
     * The container instance.
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * The config repository instance.
     * @var RepositoryInterface
     */
    protected RepositoryInterface $repository;

    /**
     * The event-dispatcher instance.
     * @var EventDispatcherInterface
     */
    protected EventDispatcherInterface $dispatcher;

    /**
     * The looper instance.
     * @var LooperInterface
     */
    protected LooperInterface $looper;

    /**
     * The constructor.
     * @param ContainerInterface $container
     * @param RepositoryInterface $repository
     * @param EventDispatcherInterface $dispatcher
     * @param LooperInterface $looper
     */
    public function __construct(
        ContainerInterface       $container,
        RepositoryInterface      $repository,
        EventDispatcherInterface $dispatcher,
        LooperInterface          $looper
    )
    {
        $this->container = $container;
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
        $this->looper = $looper;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return BootEvent::class;
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return 10;
    }

    /**
     * @inheritDoc
     * @param BootEvent $event
     */
    public function handle(object $event): object
    {
        /**
         * @var Server $server
         */
        $config = $this->repository->get("http.address");

        $server = new Server();
        $server->setProtocol(new Protocol());
        $server->setDispatcher($this->dispatcher);
        $server->setLooper($this->looper);
        $server->listen($config);

        $this->container->set(Server::class, $server);

        return $event;
    }
}
