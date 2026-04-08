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

namespace App\Model\Api;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * 用户钱包模型
 * @property int $id 主键
 * @property int $user_id 用户ID
 * @property string $balance 当前资产余额
 * @property string $total_profit 累计总盈亏
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 */
final class MemberWallet extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'member_wallet';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'user_id', 'balance', 'total_profit', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'balance' => 'string',
        'total_profit' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
