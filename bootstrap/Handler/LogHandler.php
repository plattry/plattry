<?php

declare(strict_types = 1);

namespace Bootstrap\Handler;

use Bootstrap\BootEvent;
use InvalidArgumentException;
use Plattry\Event\Dispatch\HandlerInterface;
use Plattry\Kit\Config\RepositoryInterface;
use Plattry\Kit\Log\Driver\FileDriver;
use Plattry\Kit\Log\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;

/**
 * A log initialization handler instance.
 */
class LogHandler implements HandlerInterface
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
        /**
         * File logger: driver, level, date, size, path, split.
         * Std logger: driver, level, date, size.
         */
        $config = $this->repository->get("log");

        !isset($config["driver"]) &&
        throw new RuntimeException("Load logger error due to driver is not specific.");

        !in_array($config["driver"], ["std", "file"]) &&
        throw new InvalidArgumentException("Load logger error due to `{$config["driver"]}` is unsupported.");

        $logger = match ($config["driver"]) {
            "std" => (new LoggerFactory())->createStdLogger(
                $config["level"] ?? LogLevel::DEBUG,
                $config["date"] ?? "Y-m-d H:i:s.u",
                $config["size"] ?? 0,
            ),
            "file" => (new LoggerFactory())->createFileLogger(
                $config["level"] ?? LogLevel::DEBUG,
                $config["date"] ?? "Y-m-d H:i:s.u",
                $config["size"] ?? 0,
                $config["path"] ?? $event->getDir("runtime/log"),
                $config["split"] ?? FileDriver::SPLIT_MONTH
            ),
        };
        $this->container->set(LoggerInterface::class, $logger);

        return $event;
    }
}
