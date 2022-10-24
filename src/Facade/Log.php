<?php

declare(strict_types = 1);

namespace App\Facade;

use Plattry\Kit\Container\FacadeAbstract;
use Psr\Log\LoggerInterface;

/**
 * A facade of log service instance.
 */
class Log extends FacadeAbstract
{
    /**
     * @inheritDoc
     */
    public static function getCallName(): string
    {
        return LoggerInterface::class;
    }

    /**
     * @inheritDoc
     */
    public static function getCallClass(): string|object
    {
        return LoggerInterface::class;
    }
}
