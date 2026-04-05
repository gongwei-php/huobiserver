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

use Hyperf\HttpServer\Router\Router;
use Hyperf\HttpMessage\Server\Response;
use Hyperf\HttpMessage\Stream\SwooleStream;

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
