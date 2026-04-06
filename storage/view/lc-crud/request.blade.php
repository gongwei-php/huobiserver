@php use Hyperf\Stringable\Str;@endphp
@php

    echo '<?php'.PHP_EOL;
    echo PHP_EOL;
    echo 'declare(strict_types=1);'.PHP_EOL;
    // 使用传入的 codeGenerator 数据
    $table = $codeGenerator['table'] ?? [];
    $tableName = $table['name'] ?? '';
    $packageName = ucwords($codeGenerator['module']) ?? '';
    if (str_contains($packageName, '/')) {
        $parts = explode('/', $packageName);
        $studlyParts = array_map([Str::class, 'studly'], $parts);
        $packageName = implode('\\', $studlyParts);
    }
    $requestName = $table['pascalCase'] ?? '';
    echo 'namespace App\\Http\\Admin\\Request\\'.$packageName .';';
    echo PHP_EOL;
    echo PHP_EOL;
    echo 'use App\Http\Common\Request\Traits\ActionRulesTrait;';
    echo PHP_EOL;
    echo 'use Hyperf\Validation\Request\FormRequest;';
    echo PHP_EOL;
@endphp

class {{$requestName}}Request extends FormRequest
{
    use ActionRulesTrait;

    public function authorize(): bool
    {
        return true;
    }

    // 自动匹配create方法验证
    public function createRules(): array
    {
        return [
@foreach($codeGenerator['formFields'] ?? [] as $field)
@php
    if(in_array($field['field'], ['id', 'created_at', 'updated_at', 'deleted_at'])) {
        continue;
    }
    $requestRules = $field['requestRules'] ?? [];
    $requestRulesStr = "['".implode("', '", $requestRules)."']";
@endphp
            '{{$field['field']}}' => {!! $requestRulesStr !!},
@endforeach
        ];
    }

    // 自动匹配save方法验证
    public function saveRules(): array
    {
        return [
@foreach($codeGenerator['formFields'] ?? [] as $field)
@php
    if(in_array($field['field'], ['id', 'created_at', 'updated_at', 'deleted_at'])) {
        continue;
    }
    $requestRules = $field['requestRules'] ?? [];
    $requestRulesStr = "['".implode("', '", $requestRules)."']";
@endphp
            '{{$field['field']}}' => {!! $requestRulesStr !!},
@endforeach
        ];
    }

    public function attributes(): array
    {
        return [
@foreach($codeGenerator['formFields'] ?? [] as $field)
@php
    if(in_array($field['field'], ['id', 'created_at', 'updated_at', 'deleted_at'])) {
        continue;
    }
    $label = $field['label'] ?? '';
    
    // 处理标签中的格式为 "xxx: 1=yyy, 2=zzz" 的内容，只保留冒号前的部分
    if (strpos($label, ':') !== false) {
        $parts = explode(':', $label, 2);
        $label = trim($parts[0]);
    }
@endphp
            '{{$field['field']}}' => trans('{{$tableName}}.{{$field['field']}}') ?: '{{$label}}',
@endforeach
        ];
    }

    /**
     * 获取验证错误的自定义消息.
     */
    public function messages(): array
    {
        return [
            // 可以在这里添加自定义的错误消息
        ];
    }
}