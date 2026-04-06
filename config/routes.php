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

use FastRoute\Route;
use Hyperf\HttpServer\Router\Router;
use Hyperf\HttpMessage\Server\Response;
use Hyperf\HttpMessage\Stream\SwooleStream;

Router::addGroup('/api/v1', function () {
    //注册会员
    Router::post('/register', [\App\Http\Api\Controller\V1\MemberController::class, 'register']);
    //会员登录
    Router::post('/login', [\App\Http\Api\Controller\V1\MemberController::class, 'login']);
    //会员退出登录
    Router::post('/logout', [\App\Http\Api\Controller\V1\MemberController::class, 'logout']);
    //获取会员信息
    Router::post('/getInfo', [\App\Http\Api\Controller\V1\MemberController::class, 'getInfo']);
    //刷新会员Token
    Router::post('/refresh', [\App\Http\Api\Controller\V1\MemberController::class, 'refresh']);
});


Router::get('/uploads/{file:.+}', function ($file) {
    // 真实文件路径
    $file = urldecode($file);
    $realPath = BASE_PATH . '/storage/uploads/' . $file;

    // 文件不存在返回404
    if (!is_file($realPath)) {
        return (new Response())->withStatus(404)->withBody(new SwooleStream('File not found'));
    }

    // 获取MIME类型（自动识别图片、文档、视频、压缩包等）
    $mimeType = mime_content_type($realPath) ?: 'application/octet-stream';

    // 读取文件内容
    $content = file_get_contents($realPath);

    // 返回文件流
    return (new Response())
        ->withHeader('Content-Type', $mimeType)
        ->withHeader('Content-Length', (string)strlen($content))
        ->withHeader('Cache-Control', 'public, max-age=31536000')
        ->withBody(new SwooleStream($content));
});

Router::get('/', static function () {
    return 'welcome use mineAdmin';
});

Router::get('/favicon.ico', static function () {
    return '';
});
