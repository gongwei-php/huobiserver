@php use Hyperf\Stringable\Str;@endphp
@php

    echo '<?php'.PHP_EOL;
    echo PHP_EOL;
    echo 'declare(strict_types=1);'.PHP_EOL;
@endphp
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

@php
    // 使用传入的 codeGenerator 数据
    $table = $codeGenerator['table'] ?? [];
    $packageName = ucwords($codeGenerator['module']) ?? '';
    $namespaceName = $packageName;
    $snakePackageName = Str::snake($packageName);
    $permissionName = $snakePackageName;
    if (str_contains($packageName, '/')) {
        $parts = explode('/', $packageName);
        $studlyParts = array_map([Str::class, 'studly'], $parts);
        $lowerParts  = array_map([Str::class, 'lower'], $parts);
        $snakePackageName = Str::snake(end($studlyParts));
        $namespaceName    = implode('\\', $studlyParts);
        $permissionName   = implode(':', $lowerParts);
    }
    $controllerName = $table['pascalCase'] ?? '';
    $snakeControllerName = Str::snake($controllerName);
    $menuName = str_replace('表', '',$table['comment'] ?? '');

    echo 'namespace App\\Http\\Admin\\Controller\\'.$namespaceName .';' . PHP_EOL;
    echo PHP_EOL;
    echo 'use App\Http\Admin\Controller\AbstractController;
use App\Http\Admin\Middleware\PermissionMiddleware;';
    echo PHP_EOL;
    echo 'use App\Http\Admin\Request\\'.$namespaceName .'\\'.$controllerName.'Request as Request;';
    echo PHP_EOL;
    echo 'use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;';
    echo PHP_EOL;
    echo 'use App\Service\\'.$namespaceName .'\\'.$controllerName.'Service as Service;';
    echo PHP_EOL;
    echo 'use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Swagger\Annotation as OA;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Put;
use Mine\Access\Attribute\Permission;
use Mine\Swagger\Attributes\ResultResponse;';
    echo PHP_EOL;
    echo PHP_EOL;
@endphp

#[OA\Tag('{{$menuName}}')]
#[OA\HyperfServer('http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
#[Middleware(middleware: OperationMiddleware::class, priority: 98)]
class {{$controllerName}}Controller extends AbstractController
{
    public function __construct(
        private readonly Service $service,
        private readonly CurrentUser $currentUser
    ) {}

    #[Get(
        path: '/admin/{{$snakePackageName}}/{{$snakeControllerName}}/list',
        operationId: '{{$permissionName}}:{{$snakeControllerName}}:list',
        summary: '{{$menuName}}列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['{{$menuName}}'],
    )]
    #[Permission(code: '{{$permissionName}}:{{$snakeControllerName}}:list')]
    #[ResultResponse(instance: new Result())]
    public function pageList(): Result
    {
        return $this->success(
            $this->service->page(
                $this->getRequestData(),
                $this->getCurrentPage(),
                $this->getPageSize()
            )
        );
    }

    #[Post(
        path: '/admin/{{$snakePackageName}}/{{$snakeControllerName}}/create',
        operationId: '{{$permissionName}}:{{$snakeControllerName}}:create',
        summary: '{{$menuName}}新增',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['{{$menuName}}'],
    )]
    #[Permission(code: '{{$permissionName}}:{{$snakeControllerName}}:create')]
    #[ResultResponse(instance: new Result())]
    public function create(Request $request): Result
    {
        $this->service->create(array_merge($request->all(), [
            'created_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Put(
        path: '/admin/{{$snakePackageName}}/{{$snakeControllerName}}/save/{id}',
        operationId: '{{$permissionName}}:{{$snakeControllerName}}:save',
        summary: '{{$menuName}}保存',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['{{$menuName}}'],
    )]
    #[Permission(code: '{{$permissionName}}:{{$snakeControllerName}}:save')]
    #[ResultResponse(instance: new Result())]
    public function save(int $id, Request $request): Result
    {
        $this->service->updateById($id, array_merge($request->all(), [
            'updated_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Delete(
        path: '/admin/{{$snakePackageName}}/{{$snakeControllerName}}/delete',
        operationId: '{{$permissionName}}:{{$snakeControllerName}}:delete',
        summary: '{{$menuName}}删除',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['{{$menuName}}'],
    )]
    #[ResultResponse(instance: new Result())]
    #[Permission(code: '{{$permissionName}}:{{$snakeControllerName}}:delete')]
    public function delete(): Result
    {
        $this->service->deleteById($this->getRequestData());
        return $this->success();
    }

}
