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

use App\Model\Enums\Member\Status;
use Carbon\Carbon;
use Hyperf\Database\Model\Events\Creating;
use Hyperf\DbConnection\Model\Model;

/**
 * 会员模型
 * @property int $id 会员ID，主键
 * @property int $vip_level_id 会员vip等级id
 * @property string $account 会员账号
 * @property string $phone 手机
 * @property string $avatar 会员头像
 * @property Status $status 状态 (1正常 2停用)
 * @property string $login_ip 最后登陆IP
 * @property string $login_time 最后登陆时间
 * @property int $updated_by 操作员ID
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property mixed $password 密码
 */
final class Member extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'member';

    /**
     * 隐藏的字段列表.
     * @var string[]
     */
    protected array $hidden = ['password'];

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'vip_level_id', 'account', 'password', 'phone', 'avatar', 'status', 'login_ip', 'login_time', 'created_at', 'updated_at', 'updated_by', 'balance', 'total_profit'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'status' => Status::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getBalanceAttribute()
    {
        return $this->attributes['balance'] ?? 0;
    }

    public function getTotalProfitAttribute()
    {
        return $this->attributes['total_profit'] ?? 0;
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password'] = password_hash((string) $value, \PASSWORD_DEFAULT);
    }

    public function creating(Creating $event)
    {
        if (! $this->isDirty('password')) {
            $this->resetPassword();
        }
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function resetPassword(): void
    {
        $this->password = 123456;
    }
}
