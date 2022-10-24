<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use Plattry\Kit\Http\Foundation\HttpFactory;
use Plattry\Kit\Http\Processor;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A cross-origin resource sharing processor instance.
 */
class VerifyCors extends Processor
{
    /**
     * @inheritDoc
     */
    protected function before(ServerRequestInterface $request): ResponseInterface|null
    {
        if ($request->getMethod() === "OPTIONS") {
            return (new HttpFactory())->createResponse(204)
                ->withHeader("Access-Control-Allow-Origin", "*")
                ->withHeader("Access-Control-Allow-Methods", 'GET, POST, PUT, PATCH, DELETE, HEAD, OPTIONS')
                ->withHeader("Access-Control-Allow-Headers", "Content-Type")
                ->withHeader("Access-Control-Expose-Headers", "")
                ->withHeader("Access-Control-Max-Age", "86400")
                ->withHeader("Access-Control-Allow-Credentials", "true");
        }

        return null;
    }
}
