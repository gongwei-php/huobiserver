@php use Hyperf\Stringable\Str;@endphp
@php

    echo '<?php'.PHP_EOL;
    echo PHP_EOL;
    echo 'declare(strict_types=1);'.PHP_EOL;
    echo PHP_EOL;
    // 使用传入的 codeGenerator 数据
    $table = $codeGenerator['table'] ?? [];
    $packageName = ucwords($codeGenerator['module']) ?? '';
   if (str_contains($packageName, '/')) {
        $parts = explode('/', $packageName);
        $studlyParts = array_map([Str::class, 'studly'], $parts);
        $packageName = implode('\\', $studlyParts);
    }
    $serviceName = $table['pascalCase'] ?? '';
    
    echo 'namespace App\\Service\\'.$packageName .';' . PHP_EOL;
    echo PHP_EOL;
    echo 'use App\Service\IService;';
    echo PHP_EOL;
    echo 'use App\\Repository\\'.$packageName .'\\'.$serviceName.'Repository as Repository;';
    echo PHP_EOL;
    echo PHP_EOL;
    echo PHP_EOL;
@endphp

class {{$serviceName}}Service extends IService
{
    public function __construct(
        protected readonly Repository $repository
    ) {}
}
