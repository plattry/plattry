<?php

declare(strict_types = 1);

namespace Bootstrap;

use Plattry\Event\Dispatch\StoppableEvent;
use Psr\Container\ContainerInterface;

/**
 * A boot-event instance.
 */
class BootEvent extends StoppableEvent
{
    /**
     * The container instance.
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * The root path.
     * @var string
     */
    protected string $root;

    /**
     * The load-space.
     * @var LoadSpace
     */
    protected LoadSpace $space;

    /**
     * The constructor.
     * @param string $root
     * @param LoadSpace $space
     */
    public function __construct(string $root, LoadSpace $space)
    {
        $this->root = $root;
        $this->space = $space;
    }

    /**
     * Get the container.
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Get a directory path base on root.
     * @param string|null $name
     * @return string
     */
    public function getDir(string $name = null): string
    {
        return $this->root . ($name === null ? "" : ("/" . trim($name, "/")));
    }

    /**
     * Get the load-space.
     * @return LoadSpace
     */
    public function getSpace(): LoadSpace
    {
        return $this->space;
    }
}
