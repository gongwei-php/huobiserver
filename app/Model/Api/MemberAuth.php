<?php

declare(strict_types=1);


namespace App\Model\Api;

use App\Model\Enums\MemberAuth\Status;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 用户ID，主键
 * @property int $member_id 用户账号
 * @property string $card_front_url 证件照正面照片地址
 * @property string $card_back_url 证件照反面照片地址
 * @property Status $status 状态 (1=待审核,2=已通过,2=已拒绝)
 * @property int $updated_by 操作员ID
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property null|Member $member 会员
 */
final class MemberAuth extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'member_auth';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'member_id', 'card_front_url', 'card_back_url', 'status', 'created_at', 'updated_at', 'updated_by'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'status' => Status::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
