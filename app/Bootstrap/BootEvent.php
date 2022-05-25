<?php

declare(strict_types = 1);

namespace App\Bootstrap;

use Plattry\Dispatcher\StoppableEvent;
use Plattry\Ioc\ContainerAwareTrait;
use Psr\Container\ContainerInterface;

/**
 * Application boot event.
 */
class BootEvent extends StoppableEvent
{
    use ContainerAwareTrait;

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
     * @param $root
     * @param $env
     */
    public function __construct($root, $env)
    {
        $this->root = $root;
        $this->env = $env;
    }

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }

    /**
     * @return string
     */
    public function getEnv(): string
    {
        return $this->env;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Get full path.
     * @param string $path
     * @return string
     */
    public function getFullPath(string $path): string
    {
        $path = sprintf("/%s/%s", $this->getRoot(), str_replace("\\", "/", $path));

        return (string)preg_replace(["# #", "#/+#"], ["", "/"], $path);
    }
}
