<?php

declare(strict_types = 1);

namespace App\Common;

use Plattry\Ioc\Container;
use Plattry\Utils\Filesystem;
use Psr\Container\ContainerInterface;

/**
 * 应用内核
 */
class Kernel
{
    /**
     * 根路径
     * @var string
     */
    protected string $root;

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
     * @param string $root
     * @param string $env
     * @throws \Plattry\Ioc\Exception\ContainerException
     */
    public function __construct(string $root, string $env = "dev")
    {
        $this->root = $root;
        $this->env = $env;
    }

    /**
     * 返回预绑定资源
     * @return string[]
     */
    protected static function getPreBundles(): array
    {
        return [
            // 配置
            \Plattry\Config\Repository::class => \Plattry\Config\Repository::class,
        ];
    }

    /**
     * 格式化文件/文件夹路径
     * @param string $path
     * @return string
     */
    protected function formatPath(string $path): string
    {
        $path = sprintf("/%s/%s", $this->root, str_replace("\\", "/", $path));

        return (string)preg_replace(["# #", "#/+#"], ["", "/"], $path);
    }

    /**
     * 初始化加载配置
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function loadConfig(): void
    {
        // 基础配置
        $confDir = "$this->root/config";

        $baseConf = "$confDir/base.yaml";
        $this->container->get(\Plattry\Config\Repository::class)->import($baseConf);

        // 环境配置
        $envConf = "$confDir/$this->env";
        if (file_exists($envConf))
            $this->container->get(\Plattry\Config\Repository::class)->import($envConf, true);
    }

    /**
     * 初始化加载容器绑定
     * @throws \Plattry\Ioc\Exception\ContainerException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function loadBundles(): void
    {
        $bundles = $this->container->get(\Plattry\Config\Repository::class)->get("bundles");

        // 绑定指定类
        foreach ($bundles['class'] as $id => $class) {
            $this->container::setBundle($id, $class);
        }

        // 绑定文件夹下所有类
        foreach ($bundles['directory'] as $dir) {
            $dir = $this->formatPath($dir);
            foreach (Filesystem::scanDir($dir, true, "/.php$/") as $file) {
                $class = Filesystem::findClass($file);
                if (class_exists($class))
                    $this->container::setBundle($class, $class);
            }
        }
    }

    /**
     * 运行内核
     * @throws \Plattry\Ioc\Exception\ContainerException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function run(): void
    {
        $this->container = new Container();
        $this->container::setBundle(static::getPreBundles());

        $this->loadConfig();
        $this->loadBundles();
    }
}
