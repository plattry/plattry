<?php

declare(strict_types = 1);

namespace App\Bootstrap\Handler;

use App\Bootstrap\BootEvent;
use Plattry\Dispatcher\EventHandlerInterface;
use Plattry\Utils\Filesystem;

/**
 * Load bundle resources from config.
 */
class ParseBundleHandler implements EventHandlerInterface
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return BootEvent::class;
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
     * @param BootEvent $event
     * @return BootEvent
     * @throws \Plattry\Ioc\Exception\ContainerException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function handle(object $event): object
    {
        // Get bundles configuration.
        $config = $event->getContainer()->get(\Plattry\Config\Repository::class)->get('bundles');

        // Load.
        foreach ($config as $item) {
            // Is class.
            if (class_exists($item)) {
                $event->getContainer()::setBundle($item, $item);

                continue;
            }

            $full = $event->getFullPath($item);

            // Is file.
            if (is_file($full) && file_exists($full)) {
                $class = Filesystem::findClass($full);
                if (class_exists($class)) {
                    $event->getContainer()::setBundle($class, $class);
                }

                continue;
            }

            // Is directory.
            if (is_dir($full) && file_exists($full)) {
                foreach (Filesystem::scanDir($full, true, "/.php$/") as $file) {
                    $class = Filesystem::findClass($file);
                    if (class_exists($class)) {
                        $event->getContainer()::setBundle($class, $class);
                    }
                }

                continue;
            }

            throw new \InvalidArgumentException("Invalid bundle resource $item");
        }

        return $event;
    }
}
