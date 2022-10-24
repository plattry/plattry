<?php

declare(strict_types = 1);

namespace Bootstrap\Handler;

use Bootstrap\BootEvent;
use Plattry\Event\Dispatch\HandlerInterface;
use Plattry\Kit\Config\RepositoryInterface;

/**
 * A config initialization handler instance.
 */
class ConfigHandler implements HandlerInterface
{
    /**
     * The config repository instance.
     * @var RepositoryInterface
     */
    protected RepositoryInterface $repository;

    /**
     * The constructor.
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
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
        return 1;
    }

    /**
     * @inheritDoc
     * @param BootEvent $event
     */
    public function handle(object $event): object
    {
        $space = $event->getSpace();

        $paths = array_map(fn ($item) => $event->getDir($item), [
            "config/common",
            "config/$space->value",
        ]);

        $this->repository->import($paths);

        return $event;
    }
}
