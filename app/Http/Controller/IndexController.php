<?php

declare(strict_types = 1);

namespace App\Http\Controller;

use App\Common\JsonResponse;
use Plattry\Http\Cookie\CookieInterface;
use Plattry\Http\Cookie\CookieProcessor;
use Plattry\Http\Routing\Route;
use Plattry\Http\Session\SessionInterface;
use Plattry\Http\Session\SessionProcessor;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Index控制器
 * Class IndexController
 * @package App\Controller
 */
#[Route("index")]
class IndexController
{
    /**
     * Session对象
     * @var SessionInterface
     */
    protected SessionInterface $session;

    /**
     * Cookie对象
     * @var CookieInterface
     */
    protected CookieInterface $cookie;

    /**
     * Index控制器构造方法
     * @param SessionInterface $session
     * @param CookieInterface $cookie
     */
    public function __construct(SessionInterface $session, CookieInterface $cookie)
    {
        $this->session = $session;
        $this->cookie = $cookie;
    }

    /**
     * Index方法
     * @param ServerRequestInterface $request 客户端请求实例
     * @param array $args 路由参数
     * @return ResponseInterface
     */
    #[Route("index", methods: ["get", "post"], middlewares: [CookieProcessor::class, SessionProcessor::class])]
    public function index(ServerRequestInterface $request, array $args = []): ResponseInterface
    {
        return JsonResponse::create("Hello, this is IndexController::index()!");
    }
}
