<?php

declare(strict_types=1);


namespace App\Http\Admin\Controller;

use App\Http\Admin\Controller\AbstractController;
use App\Http\Admin\Middleware\PermissionMiddleware;
use App\Http\Api\Request\V1\MemberRequest;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use App\Http\currentUser;
use App\Schema\MemberSchema;
use App\Service\MemberService;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Put;
use Hyperf\Swagger\Annotation\RequestBody;
use Mine\Access\Attribute\Permission;
use Mine\Swagger\Attributes\PageResponse;
use Mine\Swagger\Attributes\ResultResponse;

#[HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
#[Middleware(middleware: OperationMiddleware::class, priority: 98)]
class MemberController extends AbstractController
{
    public function __construct(
        protected readonly currentUser $currentUser,
        protected readonly MemberService $service
    ) {}

    #[Get(
        path: '/admin/member/list',
        operationId: 'memberList',
        summary: '会员列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['会员管理'],
    )]
    #[PageResponse(instance: MemberSchema::class)]
    #[Permission(code: 'member:index')]
    public function pageList(): Result
    {
        return $this->success(
            $this->service->page(
                $this->getRequestData(),
                $this->getCurrentPage(),
                $this->getPageSize(),
                'id',
                'desc'
            )
        );
    }

    #[Put(
        path: '/admin/member/password',
        operationId: 'updatePassword',
        summary: '重置密码',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['会员管理']
    )]
    #[Permission(code: 'member:password')]
    #[ResultResponse(new Result())]
    public function resetPassword(): Result
    {
        return $this->service->resetPassword($this->getRequest()->input('id'))
            ? $this->success()
            : $this->error();
    }
}
