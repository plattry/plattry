<?php

declare(strict_types = 1);

namespace App\Http;

use App\Common\Kernel;
use Plattry\Network\Server;

/**
 * Http应用
 */
class Application extends Kernel
{
    /**
     * socket服务器
     * @var Server
     */
    protected Server $server;

    /**
     * @inheritdoc
     */
    protected static function getPreBundles(): array
    {
        return array_merge(parent::getPreBundles(), [
            // 事件分发器
            \Psr\EventDispatcher\ListenerProviderInterface::class => \Plattry\Dispatcher\Provider::class,
            \Psr\EventDispatcher\EventDispatcherInterface::class => \Plattry\Dispatcher\Dispatcher::class,

            // 网络服务器、Http协议及Http路由器
            \Plattry\Network\Server::class => \Plattry\Network\Server::class,
            \Plattry\Network\Protocol\ProtocolInterface::class => \Plattry\Http\Protocol::class,
            \Plattry\Http\Routing\RouterInterface::class => \Plattry\Http\Routing\Router::class,

            # 日志
            \Psr\Log\LoggerInterface::class => \Plattry\Log\Logger::class,
            \Plattry\Log\Driver\DriverInterface::class => \Plattry\Log\Driver\FileDriver::class
        ]);
    }

    /**
     * 初始化加载路由
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function loadRoutes(): void
    {
        $routes = $this->container->get(\Plattry\Config\Repository::class)->get("routes");

        foreach ($routes['attribute']['directory'] as $dir) {
            $dir = $this->formatPath($dir);
            $this->container->get(\Plattry\Http\Routing\RouterInterface::class)->loadDir($dir);
        }
    }

    /**
     * 初始化Http事件
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function loadEvent(): void
    {
        $server = $this->container->get(\Plattry\Config\Repository::class)->get("server");

        $provider = $this->container->get(\Psr\EventDispatcher\ListenerProviderInterface::class);

        foreach ($server['event'] as $event) {
            $handler = $this->container->get($event);
            $provider->addListener($handler->getName(), [$handler, "handle"], $handler->getPriority());
        }
    }

    /**
     * 运行Http服务
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function run(): void
    {
        parent::run();

        $this->loadRoutes();
        $this->loadEvent();

        $server = $this->container->get(\Plattry\Config\Repository::class)->get("server");

        $dispatcher = $this->container->get(\Psr\EventDispatcher\EventDispatcherInterface::class);
        $protocol = $this->container->get(\Plattry\Network\Protocol\ProtocolInterface::class);

        $this->server = new Server($server['ip'], $server['port']);
        $this->server->setDispatcher($dispatcher);
        $this->server->setProtocol($protocol);
        $this->server->listen();
    }
}