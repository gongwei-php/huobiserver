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
use App\Schema\MemberSchema;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation as OA;
use Mine\Swagger\Attributes\ResultResponse;
use App\Service\Api\MemberService;
use Hyperf\Collection\Arr;
use Hyperf\Engine\Contract\Http\V2\RequestInterface;
use Hyperf\HttpServer\Annotation\Middleware;
use Psr\Log\LoggerInterface;
use Mine\Jwt\Traits\RequestScopedTokenTrait;

#[HyperfServer(name: 'http')]
final class MemberController extends AbstractController
{
    use RequestScopedTokenTrait;

    public function __construct(
        private readonly MemberService   $memberService,
        private readonly CurrentMember   $currentMember,
        private readonly LoggerInterface $logger,
    ) {}

    #[Post(
        path: '/register',
        operationId: 'ApiV1Register',
        summary: '用户注册',
        tags: ['api'],
    )]
    #[ResultResponse(
        instance: new Result(data: new MemberLoginVo()),
        title: '注册成功',
        description: '注册成功返回对象',
        example: '{"code":200,"message":"成功","data":{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MjIwOTQwNTYsIm5iZiI6MTcyMjA5NDAiwiZXhwIjoxNzIyMDk0MzU2fQ.7EKiNHb_ZeLJ1NArDpmK6sdlP7NsDecsTKLSZn_3D7k","refresh_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MjIwOTQwNTYsIm5iZiI6MTcyMjA5NDAiwiZXhwIjoxNzIyMDk0MzU2fQ.7EKiNHb_ZeLJ1NArDpmK6sdlP7NsDecsTKLSZn_3D7k","expire_at":300}}'
    )]
    #[OA\RequestBody(content: new OA\JsonContent(
        ref: MemberRequest::class,
        title: '登录请求参数',
        required: ['account', 'phone', 'password', 'repassword'],
        example: '{"account":"admin","phone":"13135773645","password":"123456","repassword":"123456"}'
    ))]
    public function register(MemberRequest $request): Result
    {
        $account = (string)$request->post('account', '');
        $phone = (string)$request->post('phone', '');
        $password = (string)$request->post('password', '');
        $repassword = (string)$request->post('repassword', '');
        $account = trim($account);
        $phone = trim($phone);
        $password = trim($password);
        $repassword = trim($repassword);
        $cleanPhone = str_replace(['-', ' ', '.'], '', $phone);
        if (empty($account)) {
            return $this->error('사용자 계정은 비워둘 수 없습니다!');
        }
        if (empty($cleanPhone)) {
            return $this->error('사용자 휴대폰 번호는 비워둘 수 없습니다!');
        }
        if (empty($password)) {
            return $this->error('사용자 비밀번호는 비워둘 수 없습니다!');
        }
        if ($password !== $repassword) {
            return $this->error('비밀번호가 일치하지 않습니다!');
        }
        $pattern = '/^01[016789][-. ]?\d{3,4}[-. ]?\d{4}$/';
        if (!preg_match($pattern, $cleanPhone)) {
            return $this->error('유효한 휴대폰 번호를 입력해 주세요!');
        }

        $register = $this->memberService->register(
            $account,
            $phone,
            $password,
            $request->ip(),
        );
        $code = $register['code'] ?? 0;
        $msg = $register['msg'] ?? '';
        $data = $register['data'] ?? [];
        if ($code == 0) {
            return $this->error($msg);
        }
        return $this->success($data);
    }

    #[Post(
        path: '/login',
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
        $account = (string)$validated['account'];
        $password = (string)$validated['password'];

        return $this->success(
            $this->memberService->login(
                $account,
                $password,
            )
        );
    }

    #[Post(
        path: '/logout',
        operationId: 'ApiV1Logout',
        summary: '退出',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['admin:passport']
    )]
    #[ResultResponse(instance: new Result(), example: '{"code":200,"message":"成功","data":[]}')]
    public function logout(): Result
    {
        $this->memberService->logout($this->getToken());
        return $this->success();
    }

    #[OA\Get(
        path: '/getInfo',
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
                ['account', 'avatar', 'phone', 'vip_level_id', 'login_ip', 'login_time']
            )
        );
    }

    #[Post(
        path: '/refresh',
        operationId: 'refresh',
        summary: '刷新token',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['admin:passport']
    )]
    #[Middleware(TokenMiddleware::class)]
    #[ResultResponse(
        instance: new Result(data: new MemberLoginVo())
    )]
    public function refresh(CurrentMember $member): Result
    {
        return $this->success($member->refresh());
    }
}
