<?php

declare(strict_types = 1);

namespace Bootstrap\Handler;

use Bootstrap\BootEvent;
use Plattry\Event\Dispatch\HandlerInterface;
use Plattry\Kit\Config\RepositoryInterface;
use Plattry\Kit\Route\Router;
use Plattry\Kit\Route\RouterInterface;
use Psr\Container\ContainerInterface;

/**
 * A route initialization handler instance.
 */
class RouteHandler implements HandlerInterface
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
        $config = $this->repository->get("route");

        $router = new Router();

        foreach ($config["http"] as $item) {
            foreach ($item["methods"] as $method) {
                $router->register(
                    strtolower($method) . "/" . $item["path"],
                    $item["middlewares"],
                    $item["target"]
                );
            }

            if (is_string($item["target"])) {
                $controller = strchr($item["target"], "@", true);
                $this->container::setBundle($controller, $controller);
            }

            foreach ($item["middlewares"] as $middleware) {
                $this->container::setBundle($middleware, $middleware);
            }
        }

        $this->container->set(RouterInterface::class, $router);

        return $event;
    }
}
