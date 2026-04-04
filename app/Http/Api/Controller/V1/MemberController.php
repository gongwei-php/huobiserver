<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Http\Api\Controller\V1;

use App\Http\Api\Middleware\TokenMiddleware;
use App\Http\Api\Request\V1\MemberRequest;
use App\Http\Api\Vo\MemberLoginVo;
use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Result;
use App\Http\CurrentMember;
use App\Model\Api\Member;
use app\Repository\Api\MemberRepository;
use App\Schema\MemberSchema;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation as OA;
use Mine\Swagger\Attributes\ResultResponse;
use App\Service\Api\MemberService;
use Hyperf\Collection\Arr;
use Hyperf\Engine\Contract\Http\V2\RequestInterface;
use Hyperf\HttpServer\Annotation\Middleware;
use Mine\Jwt\Jwt;
use Mine\Jwt\Factory;
use Mine\Jwt\Traits\RequestScopedTokenTrait;

#[HyperfServer(name: 'http')]
final class MemberController extends AbstractController
{
    use RequestScopedTokenTrait;

    public function __construct(
        private readonly MemberService $memberService,
        private readonly CurrentMember $currentMember
    ) {}

    #[Post(
        path: '/api/v1/login',
        operationId: 'ApiV1Login',
        summary: '用户登录',
        tags: ['api'],
    )]
    #[ResultResponse(
        instance: new Result(data: new MemberLoginVo()),
        title: '登录成功',
        description: '登录成功返回对象',
        example: '{"code":200,"message":"成功","data":{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MjIwOTQwNTYsIm5iZiI6MTcyMjA5NDAiwiZXhwIjoxNzIyMDk0MzU2fQ.7EKiNHb_ZeLJ1NArDpmK6sdlP7NsDecsTKLSZn_3D7k","refresh_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MjIwOTQwNTYsIm5iZiI6MTcyMjA5NDAiwiZXhwIjoxNzIyMDk0MzU2fQ.7EKiNHb_ZeLJ1NArDpmK6sdlP7NsDecsTKLSZn_3D7k","expire_at":300}}'
    )]
    #[OA\RequestBody(content: new OA\JsonContent(
        ref: MemberRequest::class,
        title: '登录请求参数',
        required: ['account', 'password'],
        example: '{"account":"admin","password":"123456"}'
    ))]
    public function login(MemberRequest $request): Result
    {
        $validated = $request->validated();
        $account = (string) $validated['account'];
        $password = (string) $validated['password'];
        // your login logic here
        return $this->success(
            $this->memberService->login(
                $account,
                $password,
                $request->ip(),
            )
        );
    }

    #[Post(
        path: '/api/v1/logout',
        operationId: 'passportLogout',
        summary: '退出',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['admin:passport']
    )]
    #[ResultResponse(instance: new Result(), example: '{"code":200,"message":"成功","data":[]}')]
    #[Middleware(TokenMiddleware::class)]
    public function logout(RequestInterface $request): Result
    {
        $this->memberService->logout($this->getToken());
        return $this->success();
    }

    #[OA\Get(
        path: '/api/v1/getInfo',
        operationId: 'getInfo',
        summary: '获取用户信息',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['admin:passport']
    )]
    #[Middleware(TokenMiddleware::class)]
    #[ResultResponse(
        instance: new Result(data: MemberSchema::class),
    )]
    public function getInfo(): Result
    {
        return $this->success(
            Arr::only(
                $this->currentMember->member()?->toArray() ?: [],
                ['account', 'avatar', 'phone', 'email']
            )
        );
    }

    #[Post(
        path: '/api/v1/refresh',
        operationId: 'refresh',
        summary: '刷新token',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['admin:passport']
    )]
    #[Middleware(TokenMiddleware::class)]
    #[ResultResponse(
        instance: new Result(data: new MemberLoginVo())
    )]
    public function refresh(CurrentMember $user): Result
    {
        return $this->success($user->refresh());
    }
}