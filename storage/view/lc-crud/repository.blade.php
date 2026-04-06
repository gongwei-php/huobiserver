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
    if (str_contains($packageName, '/')) {
        $parts = explode('/', $packageName);
        $studlyParts = array_map([Str::class, 'studly'], $parts);
        $packageName = implode('\\', $studlyParts);
    }
    $repositoryName = $table['pascalCase'] ?? '';
    
    echo 'namespace App\\Repository\\'.$packageName .';';
    echo PHP_EOL;
    echo PHP_EOL;
    echo 'use App\Model\\'.$packageName .'\\'.$repositoryName.' as Model;';
    echo PHP_EOL;
    echo 'use App\Repository\IRepository;';
    echo PHP_EOL;
    echo 'use Hyperf\Collection\Arr;';
    echo PHP_EOL;
    echo 'use Hyperf\Database\Model\Builder;';
    echo PHP_EOL;
@endphp

class {{$repositoryName}}Repository extends IRepository
{
    public function __construct(protected readonly Model $model) {}

    public function handleSearch(Builder $query, array $params): Builder
    {
@foreach($codeGenerator['formFields'] ?? [] as $field)
@php
    if(in_array($field['field'], ['password', 'pass', 'updated_at', 'deleted_at'])) {
        continue;
    }
    if(in_array($field['dbType'], ['text', 'mediumtext', 'longtext', 'decimal'])) {
        continue;
    }
    $skipKeywords = ['image', 'file', 'cover', 'avatar', 'photo', 'content'];
    if (!empty(array_filter($skipKeywords, fn($keyword) => str_contains($field['field'], $keyword)))) {
        continue;
    }
@endphp
@if(in_array($field['dbType'], ['char', 'varchar']))
        if (Arr::has($params, '{{$field['field']}}')) {
            $query->where('{{$field['field']}}', 'like', '%' . $params['{{$field['field']}}'] . '%');
        }
@elseif(in_array($field['dbType'], ['int', 'tinyint', 'smallint', 'mediumint', 'bigint']))
        if (Arr::has($params, '{{$field['field']}}')) {
            if (\is_array($params['{{$field['field']}}'])) {
                $query->whereIn('{{$field['field']}}', $params['{{$field['field']}}']);
            } else {
                $query->where('{{$field['field']}}', $params['{{$field['field']}}']);
            }
        }
@elseif(str_ends_with($field['field'], '_at') || in_array($field['dbType'], ['date', 'datetime', 'timestamp']))
        if (Arr::has($params, '{{$field['field']}}')) {
            if (\is_array($params['{{$field['field']}}'])) {
                $query->whereBetween('{{$field['field']}}', $params['{{$field['field']}}']);
            } else {
                $query->where('{{$field['field']}}', $params['{{$field['field']}}']);
            }
        }
@else
        if (Arr::has($params, '{{$field['field']}}')) {
            $query->where('{{$field['field']}}', $params['{{$field['field']}}']);
        }
@endif
@endforeach
        return $query;
    }
}