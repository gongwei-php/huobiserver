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
use App\Http\Api\Request\V1\MemberVipRequest;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use App\Schema\MemberVipSchema;
use App\Service\Api\MemberVipService;
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
class MemberVipController extends AbstractController
{
    public function __construct(
        protected readonly MemberVipService $service,
        protected readonly CurrentUser $currentUser,
    ) {}

    #[Get(
        path: '/admin/member/vip/list',
        operationId: 'memberList',
        summary: '等级列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['等级管理'],
    )]
    #[PageResponse(instance: MemberVipSchema::class)]
    #[Permission(code: 'member:vip:index')]
    public function pageList(): Result
    {
        return $this->success(
            $this->service->page(
                $this->getRequestData(),
                $this->getCurrentPage(),
                $this->getPageSize(),
                'level',
                'asc'
            )
        );
    }

    #[Post(
        path: '/admin/member/vip/add',
        operationId: 'memberCreate',
        summary: '创建等级',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['等级管理'],
    )]
    #[RequestBody(
        content: new JsonContent(ref: MemberVipRequest::class)
    )]
    #[Permission(code: 'member:vip:save')]
    #[ResultResponse(instance: new Result())]
    public function create(MemberVipRequest $request): Result
    {
        $this->service->create(array_merge($request->validated(), [
            'updated_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Put(
        path: '/admin/member/vip/update/{id}',
        operationId: 'memberSave',
        summary: '保存等级',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['等级管理'],
    )]
    #[RequestBody(
        content: new JsonContent(ref: MemberVipRequest::class)
    )]
    #[Permission(code: 'member:vip:update')]
    #[ResultResponse(instance: new Result())]
    public function save(int $id, MemberVipRequest $request): Result
    {
        $this->service->updateById($id, array_merge($request->validated(), [
            'updated_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Delete(
        path: '/admin/member/vip/delete',
        operationId: 'memberDelete',
        summary: '删除等级',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['等级管理'],
    )]
    #[ResultResponse(instance: new Result())]
    #[Permission(code: 'member:vip:delete')]
    public function delete(): Result
    {
        $this->service->deleteById($this->getRequestData());
        return $this->success();
    }
}
