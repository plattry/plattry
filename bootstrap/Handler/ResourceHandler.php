<?php

declare(strict_types = 1);

namespace Bootstrap\Handler;

use Bootstrap\BootEvent;
use Plattry\Event\Dispatch\HandlerInterface;
use Plattry\Kit\Config\RepositoryInterface;
use Psr\Container\ContainerInterface;

/**
 * A resource initialization handler instance.
 */
class ResourceHandler implements HandlerInterface
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
     * The constructor.
     * @param ContainerInterface $container
     * @param RepositoryInterface $repository
     */
    public function __construct(ContainerInterface $container, RepositoryInterface $repository)
    {
        $this->container = $container;
        $this->repository = $repository;
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
        return 5;
    }

    /**
     * @inheritDoc
     * @param BootEvent $event
     */
    public function handle(object $event): object
    {
        $config = $this->repository->get("resource");

        foreach ($config as $item) {
            // Resource item, example: xxx::class.
            if (is_string($item) && class_exists($item)) {
                $this->container::setBundle($item, $item);
                continue;
            }

            // Resource item, example: [xxx/xxx::class, xxx::class].
            if (is_array($item) && count($item) === 2) {
                $this->container::setBundle($item[0], $item[1]);
                continue;
            }
        }

        return $event;
    }
}
