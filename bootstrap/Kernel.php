<?php

declare(strict_types = 1);

namespace Bootstrap;

use Bootstrap\Handler\ConfigHandler;
use Bootstrap\Handler\Http\MessageHandler;
use Bootstrap\Handler\HttpHandler;
use Bootstrap\Handler\LogHandler;
use Bootstrap\Handler\ResourceHandler;
use Bootstrap\Handler\RouteHandler;
use Plattry\Event\Dispatch\Dispatcher;
use Plattry\Event\Dispatch\Provider;
use Plattry\Event\Notification\Looper;
use Plattry\Event\Notification\LooperInterface;
use Plattry\Kit\Config\Repository;
use Plattry\Kit\Config\RepositoryInterface;
use Plattry\Kit\Container\Container;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * A kernel instance.
 */
class Kernel
{
    /**
     * The root path.
     * @var string
     */
    protected string $root;

    /**
     * The constructor.
     */
    public function __construct()
    {
        $this->root = dirname(__DIR__);
    }

    /**
     * Bundle the core service resource.
     * @param ContainerInterface $container
     * @return void
     */
    protected function bundle(ContainerInterface $container): void
    {
        $container::setBundle([
            // Event services.
            ListenerProviderInterface::class => Provider::class,
            EventDispatcherInterface::class => Dispatcher::class,
            LooperInterface::class => Looper::class,

            // Config services.
            RepositoryInterface::class => Repository::class,
        ]);
    }

    /**
     * Register the handlers in container, and trigger a boot-event.
     * @param LoadSpace $space
     * @param LoadMode $mode
     * @param ContainerInterface $container
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function boot(LoadSpace $space, LoadMode $mode, ContainerInterface $container): void
    {
        // The handlers, include class, load-stage and load-mode.
        $handlers = [
            [ConfigHandler::class, LoadStage::INIT, LoadMode::ALL],

            [RouteHandler::class, LoadStage::INIT, [LoadMode::HTTP]],

            [HttpHandler::class, LoadStage::INIT, [LoadMode::HTTP]],
            [MessageHandler::class, LoadStage::EXEC, [LoadMode::HTTP]],

            [LogHandler::class, LoadStage::INIT, LoadMode::ALL],

            [ResourceHandler::class, LoadStage::INIT, LoadMode::ALL]
        ];

        $provider = $container->get(ListenerProviderInterface::class);

        // Register init-handler in the container.
        // They can use the default resource in container.
        foreach ($handlers as $item) {
            if (LoadStage::INIT->shouldLoad($item[1]) && $mode->shouldLoad($item[2])) {
                $container::setBundle($item[0], $item[0]);
                $provider->addHandler($container->get($item[0]));
            }
        }

        // Dispatch boot-event, that will initialize services.
        $dispatch = $container->get(EventDispatcherInterface::class);
        $dispatch->dispatch(new BootEvent($this->root, $space));

        // Register exec-handler in the container.
        // They can use the default resource and the service in container.
        foreach ($handlers as $item) {
            if (LoadStage::EXEC->shouldLoad($item[1]) && $mode->shouldLoad($item[2])) {
                $container::setBundle($item[0], $item[0]);
                $provider->addHandler($container->get($item[0]));
            }
        }
    }

    /**
     * Run the kernel.
     * @param LoadMode $mode
     * @param LoadSpace $space
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function run(LoadSpace $space, LoadMode $mode): void
    {
        $container = Container::getGlobal();

        // Set the core resource in container.
        $this->bundle($container);

        // Trigger boot-event for initializing the base resource.
        $this->boot($space, $mode, $container);

        // Run looper.
        $looper = $container->get(LooperInterface::class);
        $looper->watch();
    }
}
