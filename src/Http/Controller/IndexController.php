<?php

declare(strict_types = 1);

namespace App\Http\Controller;

use App\Http\Logic\IndexLogic;
use App\Utils\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * An index controller instance.
 */
class IndexController
{
    /**
     * The index logic instance.
     * @var IndexLogic
     */
    protected IndexLogic $indexLogic;

    /**
     * The construct
     * @param IndexLogic $indexLogic
     */
    public function __construct(IndexLogic $indexLogic)
    {
        $this->indexLogic = $indexLogic;
    }

    /**
     * Handle index request.
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $data = $this->indexLogic->hello();

        return (new JsonResponse())->success($data);
    }
}
