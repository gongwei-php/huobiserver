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

namespace App\Http\Admin\Controller;

use App\Http\Admin\Controller\AbstractController;
use App\Http\Admin\Middleware\PermissionMiddleware;
use App\Http\Api\Request\V1\MemberAuthRequest;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use App\Schema\MemberAuthSchema;
use App\Service\Api\MemberAuthService;
use Hyperf\HttpServer\Annotation\Middleware;
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
class MemberAuthController extends AbstractController
{
    public function __construct(
        protected readonly MemberAuthService $service,
        protected readonly CurrentUser $currentUser,
    ) {}

    #[Get(
        path: '/admin/member/auth/list',
        operationId: 'memberList',
        summary: '实名列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['实名管理'],
    )]
    #[PageResponse(instance: MemberAuthSchema::class)]
    #[Permission(code: 'member::auth:index')]
    public function pageList(): Result
    {
        return $this->success(
            $this->service->page(
                $this->getRequestData(),
                $this->getCurrentPage(),
                $this->getPageSize(),
            )
        );
    }

    #[Put(
        path: '/admin/member/auth/agree/{id}',
        operationId: 'memberAuthAgree',
        summary: '同意实名',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['同意'],
    )]
    #[Permission(code: 'member::auth:agree')]
    #[ResultResponse(instance: new Result())]
    public function agree(int $id): Result
    {
        $this->service->agreeById($id, $this->currentUser->id());
        return $this->success();
    }

    #[Put(
        path: '/admin/member/auth/refuse/{id}',
        operationId: 'memberAuthRefuse',
        summary: '拒绝实名',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['拒绝'],
    )]
    #[Permission(code: 'member::auth:refuse')]
    #[ResultResponse(instance: new Result())]
    public function refuse(int $id, MemberAuthRequest $request): Result
    {
        $this->service->refuseById($id, $this->currentUser->id());
        return $this->success();
    }
}
