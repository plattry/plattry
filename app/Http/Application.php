<?php

declare(strict_types = 1);

namespace App\Http;

use App\Bootstrap\Kernel;

/**
 * Http application.
 */
class Application extends Kernel
{
    /**
     * @inheritdoc
     */
    protected function getBundles(): array
    {
        return array_merge(parent::getBundles(), [
            // http protocol, router and server.
            \Plattry\Network\Protocol\ProtocolInterface::class => \Plattry\Http\Protocol::class,
            \Plattry\Http\Routing\RouterInterface::class => \Plattry\Http\Routing\Router::class,
            \App\Bootstrap\Handler\HttpServerHandler::class => \App\Bootstrap\Handler\HttpServerHandler::class
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function getEventHandler(): array
    {
        return array_merge(parent::getEventHandler(), [
            \App\Bootstrap\Handler\HttpServerHandler::class,
        ]);
    }
}
