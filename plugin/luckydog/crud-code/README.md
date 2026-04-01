# 代码生成器插件

> MineAdmin 3.0 版本命令行代码生成器插件，让您的开发效率提升数倍！

## 📋 功能介绍

* 一键生成MineAdmin项目的CRUD代码
* 包含前后端完整文件
* 根据数据库字段名称、注释等自动生成
* 支持json文件配置
* 支持多目录层级生成


## 🚀 一、安装步骤

### 1. 下载并安装插件

```bash
# 下载插件
php bin/hyperf.php mine-extension:download luckydog/crud-code

# 安装插件
php bin/hyperf.php mine-extension:install luckydog/crud-code --yes
```

### 2. 安装依赖组件

#### 安装视图组件

```bash
# 安装视图组件
composer require hyperf/view-engine

# 发布视图配置文件
php bin/hyperf.php vendor:publish hyperf/view-engine
```

#### 安装Task组件

```bash
# 安装Task组件
composer require hyperf/task
```

修改服务器配置文件 `/config/autoload/server.php`：

```php
return [
    // ... 其他配置
    'settings' => [
        // ...
        'task_worker_num' => 8,
        'task_enable_coroutine' => false,
    ],
    'callbacks' => [
        // 添加以下回调
        Event::ON_TASK => [Hyperf\Framework\Bootstrap\TaskCallback::class, 'onTask'],
        Event::ON_FINISH => [Hyperf\Framework\Bootstrap\FinishCallback::class, 'onFinish'],
    ],
];
```

## 💡 二、使用指南

### 1. 创建数据表

```bash
# 创建数据迁移文件
php bin/hyperf.php gen:migration create_shop_goods_table

# 执行迁移
php bin/hyperf.php migrate
```

### 2. 生成CRUD代码

**注：第一次生成，会在/runtime/json目录生成json文件。
如修改json文件，可再次执行一次命令，会根据json配置生成。**

```bash
php bin/hyperf.php luckydog:generate-crud --module=test --table=shop_goods --force=1 --pid=0 --sql=0
```

#### 参数说明

| 参数名    | 示例值 | 说明                           |
|--------|--------|------------------------------|
| module | test | 模块名称，支持多目录: --module=test/sub                        |
| table  | shop_goods | 数据表名                         |
| force  | 1 | 是否覆盖文件(1=是,0=否)              |
| pid    | 0 | 上级菜单ID                       |
| sql    | 0 | 菜单sql语句（0=自动执行，1=不执行） |

### 3. 后续步骤

1. 执行生成的SQL语句（文件位置：`/runtime/sql`）
2. 清理缓存：`composer dump-autoload -o`
3. 重启服务


## json配置示例

```php
{
    // 搜索字段
    "searchFields": [
        "title"
    ],
    // 表格字段
    "tableFields": [
        "title",
        "read_count",
        "status",
        "created_at"
    ],
    // 排序字段
    "sortFields": [
        "read_count",
        "sort",
        "status"
    ],
    // 表单
    "formFields": [
        {
            "prop": "title",
            "label": "标题",
            "render": "<el-input \/>",
            "requestRule": [
                "required"
            ],
            "renderProps": {
                "placeholder": "t('form.pleaseInput', { msg: '标题' })"
            }
        },
        {
            "prop": "status",
            "label": "状态",
            "render": "<el-select \/>",
            "requestRule": [
                "integer"
            ],
            "renderProps": {
                "placeholder": "t('form.pleaseSelect', { msg: '状态' })",
                "clearable": true,
                "data": [
                    {
                        "label": "上架",
                        "value": 1
                    },
                    {
                        "label": "下架",
                        "value": 2
                    }
                ]
            }
        }
    ]
}
```


## 🪄 字段注释识别

1、[required,search,hidden]

| 参数名 |  说明 |
|--------|------|
| required | 表单必填校验【前后端都校验】 |
| search | 表格搜索筛选 |
| hidden | 表格列/搜索 隐藏 |

2、状态: 1=上架, 2=下架
```php
protected array $appends = [
    'status_text',
    'is_recommend_text',
];
public function getStatusTextAttribute(): string
{
    $options = [
        1 => '上架',
        2 => '下架',
    ];
    
    return $options[$this->status] ?? '';
}
```

数据表示例：
```php
Schema::create('shop_goods', function (Blueprint $table) {
    $table->comment('商品表');
    $table->bigIncrements('id');
    $table->string('name', 100)->default('')->comment('名称[required,search]');
    $table->string('image', 255)->default('')->comment('图片[required]');
    $table->decimal('price', 10, 2)->default(0)->comment('价格[required]');
    $table->bigInteger('brand_id')->default(0)->comment('品牌ID');
    $table->integer('stock_count')->default(0)->comment('库存');
    $table->json('goods_attr')->nullable()->comment('商品属性');
    $table->string('description', 255)->default('')->comment('描述[hidden]');
    $table->string('content', 255)->default('')->comment('内容[hidden]');
    $table->integer('sort')->default(0)->comment('排序');
    $table->tinyInteger('status')->default(1)->comment('状态: 1=上架, 2=下架');
    $table->tinyInteger('is_recommend')->default(1)->comment('是否推荐:1=是, 2=否');
    $table->string('remark', 255)->default('')->comment('备注');
    $table->timestamps();
    $table->softDeletes();
});
```


### 常见问题
1、生成的sql文件内容为html代码？

答：请检查是否已安装hyperf/view-engine 视图组件，如未安装请执行
```bash
# 安装视图组件
composer require hyperf/view-engine

# 发布视图配置文件
php bin/hyperf.php vendor:publish hyperf/view-engine
```
