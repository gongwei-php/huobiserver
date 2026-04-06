@php use Hyperf\Stringable\Str;use Hyperf\DbConnection\Db;@endphp
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
    $tableName = $table['name'] ?? '';
    $modelName = $table['pascalCase'] ?? '';
    $moduleName = ucwords($codeGenerator['module'] ?? '');
    if (str_contains($moduleName, '/')) {
        $parts = explode('/', $moduleName);
        $studlyParts = array_map([Str::class, 'studly'], $parts);
        $moduleName = implode('\\', $studlyParts);
    }

    // 收集包含选项格式的字段，例如：状态: 1=启用, 2=禁用
    $statusFields = [];
    $fieldOptions = [];

    // 收集以_id结尾的字段，用于生成关联关系
    $relationFields = [];
    $softDelete = false;
    foreach ($codeGenerator['formFields'] ?? [] as $field) {
        $fieldName = $field['field'] ?? '';
        $comment = $field['label'] ?? '';
        if ($fieldName == 'deleted_at') {
            $softDelete = true;
        }

        // 判断注释中是否包含数字=文本格式的内容
        if (preg_match('/.*?[:\s]+((\d+\s*=\s*[^,]+,?\s*)+)/', $comment, $matches)) {
            $statusFields[] = $field;

            // 解析注释中的选项
            $optionsStr = $matches[1];
            $options = [];

            // 使用正则表达式提取所有的数字=文本对
            preg_match_all('/(\d+)\s*=\s*([^,]+)/', $optionsStr, $optionMatches, PREG_SET_ORDER);

            foreach ($optionMatches as $optionMatch) {
                $value = (int)$optionMatch[1];
                $label = trim($optionMatch[2]);
                $options[] = ['value' => $value, 'label' => $label];
            }

            // 如果成功提取到选项，则保存到字段选项数组中
            if (!empty($options)) {
                $fieldOptions[$fieldName] = $options;
            }
        }

        // 检查字段是否以_id结尾
        if (Str::endsWith($fieldName, '_id')) {
            // 获取关联表名 (如 goods_id -> goods)
            $relatedTable = Str::beforeLast($fieldName, '_id');

            // 检查表是否存在
            try {
                $fullRelatedTable = env('DB_PREFIX').$relatedTable;
                $tableExists = Db::select("SHOW TABLES LIKE '{$fullRelatedTable}'");

                // 表存在时添加到关联列表
                if (!empty($tableExists)) {
                    // 将字段信息添加到关联字段列表
                    $relationFields[] = [
                        'field' => $fieldName,
                        'relatedTable' => $relatedTable,
                        'relationMethod' => Str::camel($relatedTable), // 驼峰命名，如 goods_category 变为 goodsCategory
                        'relationModel' => Str::studly($relatedTable)  // 帕斯卡命名，如 goods_category 变为 GoodsCategory
                    ];
                }
            } catch (\Throwable $e) {
                // 出错时不添加关联
            }
        }
    }
    $namespace = $moduleName ? "App\\Model\\{$moduleName}" : "App\\Model";
    echo "namespace {$namespace};";
    echo PHP_EOL;
    echo PHP_EOL;
    echo 'use Carbon\Carbon;';
    echo PHP_EOL;
    if ($softDelete) {
    echo 'use Hyperf\Database\Model\SoftDeletes;';
    echo PHP_EOL;
    }
    echo 'use Hyperf\DbConnection\Model\Model as MineModel;';
    echo PHP_EOL;
@endphp

/**
 * {{$table['comment'] ?? $table['name'] ?? ''}}模型.
 *
@foreach($codeGenerator['formFields'] ?? [] as $field)
@php
    $phpType = 'string';
    if (in_array($field['dbType'] ?? '', ['int', 'tinyint', 'smallint', 'mediumint', 'bigint'])) {
        $phpType = 'int';
    } elseif (in_array($field['dbType'] ?? '', ['decimal', 'float', 'double'])) {
        $phpType = 'float';
    } elseif (in_array($field['dbType'] ?? '', ['json', 'array'])) {
        $phpType = 'array';
    } elseif (in_array($field['dbType'] ?? '', ['boolean', 'bool'])) {
        $phpType = 'bool';
    } elseif (in_array($field['dbType'] ?? '', ['datetime', 'timestamp', 'date'])) {
        $phpType = 'Carbon';
    }
@endphp
 * @property {{$phpType}} ${{$field['field']}} {{$field['label'] ?? ''}}
@endforeach
@foreach($statusFields as $field)
 * @property string ${{$field['field']}}_text {{$field['label'] ?? ''}}
@endforeach
 */
final class {{$modelName}} extends MineModel
{
@if($softDelete)
    use SoftDeletes;

@endif
    /**
     * 数据表名称.
     */
    protected ?string $table = '{{$tableName}}';

    /**
     * 允许批量赋值的属性.
     */
    protected array $fillable = [
@foreach($codeGenerator['formFields'] ?? [] as $field)
        '{{$field['field']}}',
@endforeach
    ];

    /**
     * 数据转换设置.
     */
    protected array $casts = [
@foreach($codeGenerator['formFields'] ?? [] as $field)
@php
    $castType = 'string';
    if (in_array($field['dbType'] ?? '', ['int', 'tinyint', 'smallint', 'bigint'])) {
        $castType = 'integer';
    } elseif (in_array($field['dbType'] ?? '', ['float', 'double'])) {
        $castType = 'float';
    } elseif (in_array( $field['dbType'] ?? '', ['decimal'])) {
        $castType = 'decimal:2';
    } elseif (in_array($field['dbType'] ?? '', ['json', 'array'])) {
        $castType = 'array';
    } elseif (in_array($field['dbType'] ?? '', ['boolean', 'bool'])) {
        $castType = 'boolean';
    } elseif (in_array($field['dbType'] ?? '', ['datetime', 'timestamp'])) {
        $castType = 'datetime';
    } elseif ($field['dbType'] == 'date') {
        $castType = 'date';
    }
@endphp
        '{{$field['field']}}' => '{{$castType}}',
@endforeach
    ];

    /**
     * 隐藏的属性.
     */
    protected array $hidden = [
@foreach($codeGenerator['formFields'] ?? [] as $field)
@if(in_array($field['field'], ['password', 'passwd', 'secret']))
        '{{$field['field']}}',
@endif
@endforeach
    ];

@if(count($statusFields) > 0)
    /**
     * 追加到模型数组表单的访问器.
     */
    protected array $appends = [
@foreach($statusFields as $field)
        '{{$field['field']}}_text',
@endforeach
    ];

@foreach($statusFields as $field)
    /**
     * 获取{{$field['label'] ?? $field['field']}}文本描述.
     */
    public function get{{Str::studly($field['field'])}}TextAttribute(): string
    {
        $options = [
@if(isset($fieldOptions[$field['field']]))
@foreach($fieldOptions[$field['field']] as $option)
            {{$option['value']}} => '{{$option['label']}}',
@endforeach
@endif
        ];
        
        return $options[$this->{{$field['field']}}] ?? '';
    }

@endforeach
@endif


@php
    // 获取App\model目录下的所有目录名称
    $subDirs = make(\Hyperf\Support\Filesystem\Filesystem::class)->directories(BASE_PATH . '/app/Model');
    foreach( $relationFields as $relation){
        // 构建关联模型的完整命名空间路径
        $modelPath = '';
        foreach ($subDirs as $subDir) {
            if (file_exists($subDir . '/' . $relation['relationModel'] . '.php')) {
                $modelPath = basename($subDir);
                break;
            }
        }
            
        if(!$modelPath && !file_exists(BASE_PATH . '/app/Model/' . $relation['relationModel'] . '.php')){
            continue;
        }

        $relationModelNamespace = $modelPath ? "\\App\\Model\\{$modelPath}\\{$relation['relationModel']}" : "\\App\\Model\\{$relation['relationModel']}";
        
        echo "    /**\n";
        echo "     * 关联 {$relation['relationModel']} 模型.\n";
        echo "     */\n";
        echo "    public function {$relation['relationMethod']}(): Hyperf\Database\Model\Relations\BelongsTo\n";
        echo "    {\n";
        echo "        return \$this->belongsTo({$relationModelNamespace}::class, '{$relation['field']}', 'id');\n";
        echo "    }\n\n";
    }
@endphp
}