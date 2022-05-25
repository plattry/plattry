<?php

declare(strict_types = 1);

namespace App\Http\Processor;

use App\Http\Common\ErrorCode;
use App\Http\Common\HttpException;
use App\Http\Common\JsonResponse;
use Plattry\Http\Processor;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Catch error.
 */
class ErrorProcessor extends Processor
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (HttpException $e) {
            return JsonResponse::create(null, $e->getCode(), $e->getMessage());
        } catch (\Throwable $t) {
            $this->logger->error(sprintf("%s %s", $t->getMessage(), $t->getTraceAsString()));
            return JsonResponse::create(null, ErrorCode::Unknown, "Unexpected Error");
        }
    }
}
