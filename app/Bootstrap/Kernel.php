<?php

declare(strict_types = 1);

namespace App\Bootstrap;

use Plattry\Ioc\Container;
use Psr\Container\ContainerInterface;

/**
 * Application kernel.
 */
class Kernel
{
    /**
     * Project root path.
     * @var string
     */
    protected string $root;

    /**
     * Execute environment.
     * @var string
     */
    protected string $env;

    /**
     * Container.
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @param string $root
     * @param string $env
     * @throws \Plattry\Ioc\Exception\ContainerException
     */
    public function __construct(string $root, string $env = "dev")
    {
        // Set attributes.
        $this->root = $root;
        $this->env = $env;

        // Inject resources to container.
        $this->container = Container::getGlobal();
        $this->container::setBundle($this->getBundles());
    }

    /**
     * Pre-bundle resources.
     * @return string[]
     */
    protected function getBundles(): array
    {
        return [
            // Event lister and dispatcher(core).
            \Psr\EventDispatcher\ListenerProviderInterface::class => \Plattry\Dispatcher\Provider::class,
            \Psr\EventDispatcher\EventDispatcherInterface::class => \Plattry\Dispatcher\Dispatcher::class,

            // Config, Bundles parser, and logger(base).
            \App\Bootstrap\Handler\ConfigHandler::class => \App\Bootstrap\Handler\ConfigHandler::class,
            \App\Bootstrap\Handler\ParseBundleHandler::class => \App\Bootstrap\Handler\ParseBundleHandler::class,
            \App\Bootstrap\Handler\LoggerHandler::class => \App\Bootstrap\Handler\LoggerHandler::class,
        ];
    }

    /**
     * Base boot event handler.
     * @return string[]
     */
    protected function getEventHandler(): array
    {
        // First in, first out.
        return [
            \App\Bootstrap\Handler\ConfigHandler::class,
            \App\Bootstrap\Handler\ParseBundleHandler::class,
            \App\Bootstrap\Handler\LoggerHandler::class,
        ];
    }

    /**
     * Run application.
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function run(): void
    {
        /**
         * Bind event and event handler.
         * @var \Psr\EventDispatcher\ListenerProviderInterface $provider
         * @var \Psr\EventDispatcher\EventDispatcherInterface $dispacher
         */
        $provider = $this->container->get(\Psr\EventDispatcher\ListenerProviderInterface::class);
        $dispacher = $this->container->get(\Psr\EventDispatcher\EventDispatcherInterface::class);

        foreach ($this->getEventHandler() as $event) {
            $handler = $this->container->get($event);
            $provider->addListener($handler->getName(), [$handler, "handle"], $handler->getPriority());
        }

        // Trigger a boot event.
        $event = new BootEvent($this->root, $this->env);
        $event->setContainer($this->container);

        $dispacher->dispatch($event);
    }
}
