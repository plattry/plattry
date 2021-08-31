<?php

declare(strict_types = 1);

namespace App\Common;

use Plattry\Http\Foundation\HttpFactory;
use Psr\Http\Message\ResponseInterface;

/**
 * Json响应
 * Class JsonResponse
 * @package App\Common
 */
class JsonResponse
{
    /**
     * Json flag
     * @var int
     */
    public static int $flag = JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE;

    /**
     * Http factory
     * @var HttpFactory
     */
    public static HttpFactory $httpFactory;

    /**
     * 设置json flag
     * @param int $flag
     * @return void
     */
    public static function setFlag(int $flag): void
    {
        static::$flag = $flag;
    }

    /**
     * 获取http factory
     * @return HttpFactory
     */
    public static function getFactory(): HttpFactory
    {
        if (!isset(static::$httpFactory))
            static::$httpFactory = new HttpFactory();

        return static::$httpFactory;
    }

    /**
     * 创建类Json响应
     * @param mixed $data
     * @param int $code
     * @param string $message
     * @return ResponseInterface
     */
    public static function create(mixed $data, int $code = ErrorCode::SUCCESS, string $message = ""): ResponseInterface
    {
        $content = json_encode([
            "data" => $data,
            "code" => $code,
            "message" => $message ?: ErrorCode::phrase($code)
        ], static::$flag);

        $response = static::getFactory()->createResponse();
        $response->getBody()->write($content);

        return $response;
    }
}
