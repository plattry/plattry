<?php

declare(strict_types = 1);

namespace App\Http\Processor;

use App\Http\Common\JsonResponse;
use Plattry\Http\Processor;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Deal with cors.
 */
class CorsProcessor extends Processor
{
    /**
     * @inheritdoc
     */
    protected function before(ServerRequestInterface $request): ResponseInterface|null
    {
        if ($request->getMethod() === "OPTIONS") {
            return JsonResponse::getFactory()->createResponse(204)->withProtocolVersion("1.1");
        }

        return parent::before($request);
    }

    /**
     * @inheritdoc
     */
    protected function after(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->withAddedHeader("Access-Control-Allow-Origin", "*")
            ->withAddedHeader("Access-Control-Allow-Credentials", "true")
            ->withAddedHeader("Access-Control-Allow-Methods", "GET, POST, OPTIONS")
            ->withAddedHeader("Access-Control-Allow-Headers", "Content-Type, Authorization");

        return parent::after($request, $response);
    }
}
