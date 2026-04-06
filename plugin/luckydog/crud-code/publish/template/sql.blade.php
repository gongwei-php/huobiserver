@php
    use Hyperf\Stringable\Str;
    
    // 使用传入的 codeGenerator 数据
    $table = $codeGenerator['table'] ?? [];
    $moduleName = $codeGenerator['module'] ?? '';
    $packageName = strtolower($moduleName);
    $tableName = $table['name'] ?? '';
    $modelName = $table['pascalCase'] ?? '';
    $camelCaseName = $table['camelCase'] ?? '';
    $snakeModelName = Str::snake($modelName);
    $menuName = str_replace('表', '', $table['comment'] ?? $modelName);
    $pid = $table['pid'] ?? 0;

    $time = date('Y-m-d H:i:s');
@endphp

INSERT INTO `{{env('DB_PREFIX')}}menu`
(`name`, `path`, `component`, `redirect`, `created_by`, `updated_by`, `remark`, `meta`, `parent_id`, `updated_at`, `created_at`)
VALUES ('{{$camelCaseName}}', '/{{$packageName}}/{{$camelCaseName}}', '{{$packageName}}/views/{{$camelCaseName}}/index', '', '0', '0', '', '{"title":"{{$menuName}}","i18n":"{{$packageName}}.{{$modelName}}","icon":"ep:more-filled","type":"M","hidden":false,"componentPath":"modules\/","componentSuffix":".vue","breadcrumbEnable":true,"copyright":true,"cache":true,"affix":false}', {{$pid}}, '{{$time}}', '{{$time}}');

SET @LastInsertId := LAST_INSERT_ID();

INSERT INTO `{{env('DB_PREFIX')}}menu` (`name`, `meta`, `parent_id`, `updated_at`, `created_at`) VALUES (
    '{{$packageName}}:{{$snakeModelName}}:list',
    '{"title":"{{$menuName}}列表","i18n":"{{$packageName}}.{{$snakeModelName}}.list","icon":"","type":"B","hidden":false,"componentPath":"modules\/{{$packageName}}\/{{$camelCaseName}}\/","componentSuffix":".vue","breadcrumbEnable":true,"cache":true,"affix":false}',
    @LastInsertId,
    '{{$time}}',
    '{{$time}}'
);

INSERT INTO `{{env('DB_PREFIX')}}menu` (`name`, `meta`, `parent_id`, `updated_at`, `created_at`) VALUES (
    '{{$packageName}}:{{$snakeModelName}}:create',
    '{"title":"{{$menuName}}新增","i18n":"{{$packageName}}.{{$snakeModelName}}.create","icon":"","type":"B","hidden":true,"componentPath":"modules\/{{$packageName}}\/{{$camelCaseName}}\/","componentSuffix":".vue","breadcrumbEnable":true,"cache":true,"affix":false}',
    @LastInsertId,
    '{{$time}}',
    '{{$time}}'
);

INSERT INTO `{{env('DB_PREFIX')}}menu` (`name`, `meta`, `parent_id`, `updated_at`, `created_at`) VALUES (
    '{{$packageName}}:{{$snakeModelName}}:save',
    '{"title":"{{$menuName}}编辑","i18n":"{{$packageName}}.{{$snakeModelName}}.save","icon":"","type":"B","hidden":true,"componentPath":"modules\/{{$packageName}}\/{{$camelCaseName}}\/","componentSuffix":".vue","breadcrumbEnable":true,"cache":true,"affix":false}',
    @LastInsertId,
    '{{$time}}',
    '{{$time}}'
);

INSERT INTO `{{env('DB_PREFIX')}}menu` (`name`, `meta`, `parent_id`, `updated_at`, `created_at`) VALUES (
    '{{$packageName}}:{{$snakeModelName}}:delete',
    '{"title":"{{$menuName}}删除","i18n":"{{$packageName}}.{{$snakeModelName}}.delete","icon":"","type":"B","hidden":true,"componentPath":"modules\/{{$packageName}}\/{{$camelCaseName}}\/","componentSuffix":".vue","breadcrumbEnable":true,"cache":true,"affix":false}',
    @LastInsertId,
    '{{$time}}',
    '{{$time}}'
);





