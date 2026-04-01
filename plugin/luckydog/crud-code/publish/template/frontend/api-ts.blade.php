@php
    use Hyperf\Stringable\Str;
    // 使用传入的 codeGenerator 数据
    $table = $codeGenerator['table'] ?? [];
    $packageName = strtolower($codeGenerator['module'] ?? '');
    $componentName = $table['pascalCase'] ?? '';
    $snakeTableName = Str::snake($componentName);
    $menuName = $table['comment'] ?? '';
    if (str_contains($packageName, '/')) {
        $parts = explode('/', $packageName);
        $studlyParts = array_map([Str::class, 'studly'], $parts);
        $lowerParts  = array_map([Str::class, 'lower'], $parts);
        $packageName = Str::snake(end($studlyParts));
    }
@endphp
import type { ResponseStruct } from '#/global'

export interface {{ $componentName }}Vo {
  {{ $table['primaryKey'] }}: number
@foreach($codeGenerator['formFields'] ?? [] as $field)
  // {{$field['label'] ?? ''}}
  {{ $field['field'] }}: {{ in_array($field['dbType'], ['int', 'tinyint', 'smallint', 'bigint', 'decimal', 'float', 'double']) ? 'number' : 'string' }}
@endforeach
}

// {{$menuName}}查询
export function page(params: {{ $componentName }}Vo): Promise<ResponseStruct<{{$componentName}}Vo[]>> {
  return useHttp().get('/admin/{{$packageName}}/{{$snakeTableName}}/list', { params })
}

// {{$menuName}}新增
export function create(data: {{ $componentName }}Vo): Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/{{$packageName}}/{{$snakeTableName}}/create', data)
}

// {{$menuName}}编辑
export function save(id: number, data: {{ $componentName }}Vo): Promise<ResponseStruct<null>> {
  return useHttp().put(`/admin/{{$packageName}}/{{$snakeTableName}}/save/${id}`, data)
}

// {{$menuName}}删除
export function deleteByIds(ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().delete('/admin/{{$packageName}}/{{$snakeTableName}}/delete', { data: ids })
}