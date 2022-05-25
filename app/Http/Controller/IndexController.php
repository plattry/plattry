<?php

declare(strict_types = 1);

namespace App\Http\Controller;

use App\Http\Common\JsonResponse;
use App\Http\Processor\CorsProcessor;
use App\Http\Processor\ErrorProcessor;
use Plattry\Http\Routing\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

#[Route("index", ["options"], [CorsProcessor::class, ErrorProcessor::class])]
class IndexController
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route("index", ["get", "post"])]
    public function index(ServerRequestInterface $request, array $args = []): ResponseInterface
    {
        $this->logger->info("http request is coming~");

        return JsonResponse::create("hello, plattry! ");
    }
}
