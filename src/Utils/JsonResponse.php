<?php

declare(strict_types = 1);

namespace App\Utils;

use Plattry\Kit\Http\Foundation\HttpFactory;
use Psr\Http\Message\ResponseInterface;

/**
 * A http response creator with json content instance.
 */
class JsonResponse
{
    /**
     * The http-factory instance.
     * @var HttpFactory|null
     */
    protected static HttpFactory|null $factory = null;

    /**
     * Get the factory.
     * @return HttpFactory
     */
    protected static function getFactory(): HttpFactory
    {
        if (self::$factory instanceof HttpFactory) {
            return self::$factory;
        }

        return self::$factory = new HttpFactory();
    }

    /**
     * Make a new json-response.
     * @param int $code
     * @param string $message
     * @param mixed $data
     * @param array $options
     * @return ResponseInterface
     */
    public function make(int $code, string $message, mixed $data, array $options = []): ResponseInterface
    {
        $data = json_encode([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], JSON_UNESCAPED_UNICODE);

	$response = self::getFactory()->createResponse($options["code"] ?? 200, $options["message"] ?? "")
	    ->withHeader("content-type", "application/json; charset=utf-8");
        $response->getBody()->write($data);

        return $response;
    }

    /**
     * Make a json-response with success information.
     * @param mixed|null $data
     * @param ErrorCode $code
     * @param array $options
     * @return ResponseInterface
     */
    public function success(mixed $data = null, ErrorCode $code = ErrorCode::NONE, array $options = []): ResponseInterface
    {
        return $this->make($code->value, $code->message(), $data, $options);
    }

    /**
     * Make a json-response with error information.
     * @param ErrorCode $code
     * @param array $options
     * @return ResponseInterface
     */
    public function error(ErrorCode $code, array $options = []): ResponseInterface
    {
        return $this->make($code->value, $code->message(), null, $options);
    }
}
