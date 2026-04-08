<?php

declare(strict_types=1);


namespace App\Model\Api;

use App\Model\Enums\Member\Status;
use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 用户ID，主键
 * @property int $level 等级
 * @property Status $status 状态 (1正常 2停用)
 * @property int $updated_by 操作员id
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 */
final class MemberVip extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'member_vip_level_config';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'level', 'status', 'updated_by', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'level' => 'integer',
        'status' => Status::class,
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
