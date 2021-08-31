<?php

declare(strict_types = 1);

namespace App;

use Closure;
use Plattry\Ioc\Container;
use Plattry\Network\Connection\ConnectionInterface;
use Plattry\Network\Server;
use Plattry\Utils\Filesystem;
use Psr\Container\ContainerInterface;

/**
 * Http内核
 * Class HttpKernel
 * @package App
 */
class HttpKernel
{
    /**
     * 环境
     * @var string
     */
    protected string $env;

    /**
     * 容器
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * socket服务器
     * @var Server
     */
    protected Server $server;

    /**
     * 默认绑定类
     * @var array
     */
    protected array $bundles = [
        \Plattry\Config\Repository::class => \Plattry\Config\Repository::class,
        \Plattry\Network\Connection\Event::class => \Plattry\Network\Connection\Event::class,
        \Plattry\Network\Protocol\ProtocolInterface::class => \Plattry\Http\Protocol::class,
        \Plattry\Http\Routing\RouterInterface::class => \Plattry\Http\Routing\Router::class
    ];

    /**
     * Http服务器内核构造函数
     * @param string $env
     */
    public function __construct(string $env = "dev")
    {
        $this->env = $env;
        $this->container = new Container();

        // 默认绑定
        foreach ($this->bundles as $id => $class) {
            $this->container::setBundle($id, $class);
        }

        // 基础配置、环境配置
        $this->container->get(\Plattry\Config\Repository::class)->import(__DIR__ . "/../config/base.yaml");
        $this->container->get(\Plattry\Config\Repository::class)->import(__DIR__ . "/../config/$this->env", true);
    }

    /**
     * 格式化文件/文件夹路径
     * @param string $path
     * @return string
     */
    protected static function formatPath(string $path): string
    {
        $path = sprintf("/%s/%s", BASE_DIR, str_replace("\\", "/", $path));

        return (string)preg_replace(["# #", "#/+#"], ["", "/"], $path);
    }

    /**
     * 初始化绑定配置
     * @param array $bundles
     * @return void
     */
    public function parseBundles(array $bundles): void
    {
        foreach ($bundles['class'] as $id => $class) {
            $this->container::setBundle($id, $class);
        }

        foreach ($bundles['directory'] as $dir) {
            $dir = static::formatPath($dir);
            foreach (Filesystem::scanDir($dir, true, "/.php$/") as $file) {
                $class = Filesystem::findClass($file);
                if (class_exists($class))
                    $this->container::setBundle($class, $class);
            }
        }
    }

    /**
     * 初始化路由配置
     * @param array $routes
     * @return void
     */
    public function parseRoutes(array $routes): void
    {
        foreach ($routes['attribute']['directory'] as $dir) {
            $dir = static::formatPath($dir);
            $this->container->get(\Plattry\Http\Routing\RouterInterface::class)->loadDir($dir);
        }
    }

    /**
     * 初始化socket服务器配置
     * @param array $server
     * @return void
     */
    public function parseServer(array $server): void
    {
        $event = $this->container->get(\Plattry\Network\Connection\Event::class);
        $event->register($event::MESSAGE, Closure::fromCallable($server['onMessage']));

        $protocol = $this->container->get(\Plattry\Network\Protocol\ProtocolInterface::class);

        $this->server = new Server($server['ip'], $server['port']);
        $this->server->setEvent($event);
        $this->server->setProtocol($protocol);
    }

    /**
     * 运行内核
     * @return void
     */
    public function run(): void
    {
        $bundles = $this->container->get(\Plattry\Config\Repository::class)->get("bundles");
        $this->parseBundles($bundles);

        $routes = $this->container->get(\Plattry\Config\Repository::class)->get("routes");
        $this->parseRoutes($routes);

        $server = $this->container->get(\Plattry\Config\Repository::class)->get("server");
        $this->parseServer($server);

        $this->server->listen();
    }

    /**
     * 接收http消息并返回响应
     * @param ConnectionInterface $connection
     * @return void
     */
    public function onMessage(ConnectionInterface $connection): void
    {
        $container = clone $this->container;

        $protocol = $container->get(\Plattry\Network\Protocol\ProtocolInterface::class);

        $request = $protocol->getRequestFromConnection($connection);
        $rule = $container->get(\Plattry\Http\Routing\RouterInterface::class)->parse($request);

        $handler = new \Plattry\Http\Handler($rule);
        $handler->setContainer($container);

        $response = $handler->handle($request);
        $protocol->backResponseToConnection($connection, $response);
    }
}
