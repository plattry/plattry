<?php

declare(strict_types = 1);

namespace App\Http\Common;

use Plattry\Http\Foundation\HttpFactory;
use Psr\Http\Message\ResponseInterface;

/**
 * Json response.
 */
final class JsonResponse
{
    /**
     * Http factory
     * @var HttpFactory
     */
    public static HttpFactory $httpFactory;

    /**
     * @return HttpFactory
     */
    public static function getFactory(): HttpFactory
    {
        if (!isset(self::$httpFactory))
            self::$httpFactory = new HttpFactory();

        return self::$httpFactory;
    }

    /**
     * Create a new json response.
     * @param mixed $data
     * @param int|ErrorCode $code
     * @param string $message
     * @param int $flags
     * @return ResponseInterface
     */
    public static function create(
        mixed     $data,
        int|ErrorCode $code = ErrorCode::None,
        string    $message = '',
        int       $flags = JSON_UNESCAPED_UNICODE
    ): ResponseInterface
    {
        $content = json_encode([
            "data" => $data,
            "code" => $code instanceof ErrorCode ? $code->value : (int)$code,
            "message" => $message
        ], $flags);
        $response = self::getFactory()->createResponse()
            ->withProtocolVersion("1.1")
            ->wiThHeaders(["Content-Type" => "application/json;charset=UTF-8"]);
        $response->getBody()->write($content);

        return $response;
    }
}
