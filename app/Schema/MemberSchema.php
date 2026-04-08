<?php

declare(strict_types=1);


namespace App\Schema;

use App\Model\Enums\Member\Status;
use App\Model\Api\Member;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

#[Schema]
final class MemberSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '用户ID，主键', type: 'int')]
    public ?int $id;

    #[Property(property: 'vip_level', title: '用户VIP等级', type: 'int')]
    public ?int $vip_level;

    #[Property(property: 'account', title: '用户账号', type: 'string')]
    public ?string $account;

    #[Property(property: 'phone', title: '手机', type: 'string')]
    public ?string $phone;

    #[Property(property: 'avatar', title: '用户头像', type: 'string')]
    public ?string $avatar;

    #[Property(property: 'status', title: '状态 (1正常 2停用)', type: 'int')]
    public ?Status $status;

    #[Property(property: 'login_ip', title: '最后登陆IP', type: 'string')]
    public ?string $loginIp;

    #[Property(property: 'login_time', title: '最后登陆时间', type: 'string')]
    public mixed $loginTime;

    #[Property(property: 'balance', title: '余额', type: 'string')]
    public ?string $balance;

    #[Property(property: 'total_profit', title: '总收益', type: 'string')]
    public ?string $total_profit;

    public function __construct(Member $model)
    {
        $this->id = $model->id;
        $this->vip_level = $model->vip_level;
        $this->account = $model->account;
        $this->phone = $model->phone;
        $this->avatar = $model->avatar;
        $this->status = $model->status;
        $this->loginIp = $model->login_ip;
        $this->loginTime = $model->login_time;
        $this->balance = $model->balance;
        $this->total_profit = $model->total_profit;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'vip_level' => $this->vip_level,
            'account' => $this->account,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'status' => $this->status,
            'login_ip' => $this->loginIp,
            'login_time' => $this->loginTime,
            'balance' => $this->balance,
            'total_profit' => $this->total_profit
        ];
    }
}
