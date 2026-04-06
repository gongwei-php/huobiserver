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

    #[Post(
        path: '/admin/member/add',
        operationId: 'memberCreate',
        summary: '创建会员',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['会员管理'],
    )]
    #[RequestBody(
        content: new JsonContent(ref: MemberRequest::class)
    )]
    #[Permission(code: 'member:save')]
    #[ResultResponse(instance: new Result())]
    public function create(MemberRequest $request): Result
    {
        $this->service->create(array_merge($request->validated(), [
            'updated_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Put(
        path: '/admin/member/update/{id}',
        operationId: 'memberSave',
        summary: '保存会员',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['会员管理'],
    )]
    #[RequestBody(
        content: new JsonContent(ref: MemberRequest::class)
    )]
    #[Permission(code: 'member:update')]
    #[ResultResponse(instance: new Result())]
    public function save(int $id, MemberRequest $request): Result
    {
        $this->service->updateById($id, array_merge($request->validated(), [
            'updated_by' => $this->currentUser->id(),
        ]));
        return $this->success();
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

    #[Delete(
        path: '/admin/member/delete',
        operationId: 'memberDelete',
        summary: '删除会员',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['会员管理'],
    )]
    #[ResultResponse(instance: new Result())]
    #[Permission(code: 'member:delete')]
    public function delete(): Result
    {
        $this->service->deleteById($this->getRequestData());
        return $this->success();
    }
}
