<?php

declare(strict_types = 1);

namespace App\Facade;

use Plattry\Kit\Config\RepositoryInterface;
use Plattry\Kit\Container\FacadeAbstract;

/**
 * A facade of config service instance.
 */
class Config extends FacadeAbstract
{
    /**
     * @inheritDoc
     */
    public static function getCallName(): string
    {
        return RepositoryInterface::class;
    }

    /**
     * @inheritDoc
     */
    public static function getCallClass(): string|object
    {
        return RepositoryInterface::class;
    }
}
