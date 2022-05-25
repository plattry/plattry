<?php

declare(strict_types = 1);

namespace App\Bootstrap\Handler;

use App\Bootstrap\BootEvent;
use Plattry\Dispatcher\EventHandlerInterface;

/**
 * Load config files.
 */
class ConfigHandler implements EventHandlerInterface
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
     * @throws \Plattry\Config\Exception\InvalidResourceException
     */
    public function handle(object $event): object
    {
        $repository = new \Plattry\Config\Repository();

        // Load basic configuration.
        $baseConf = $event->getFullPath('config/base.yaml');
        $repository->import($baseConf);

        // Load environment configuration.
        $envConf = $event->getFullPath('config/' . $event->getEnv());
        if (file_exists($envConf)) {
            $repository->import($envConf, true);
        }

        $event->getContainer()->set(\Plattry\Config\Repository::class, $repository);

        return $event;
    }
}
