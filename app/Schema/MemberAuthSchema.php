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

namespace App\Schema;

use App\Model\Enums\MemberAuth\Status;
use App\Model\Api\MemberAuth;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

#[Schema]
final class MemberAuthSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '编号', type: 'int')]
    public ?int $id;

    #[Property(property: 'user_id', title: '等级', type: 'int')]
    public ?int $user_id;

    #[Property(property: 'card_front_url', title: '证件照正面照片地址', type: 'string')]
    public ?string $card_front_url;

    #[Property(property: 'card_back_url', title: '证件照反面照片地址', type: 'string')]
    public ?string $card_back_url;

    #[Property(property: 'status', title: '状态 (1正常 2停用)', type: 'int')]
    public ?Status $status;

    #[Property(property: 'updated_by', title: '操作员ID', type: 'int')]
    public ?int $updated_by;

    #[Property(property: 'created_at', title: '创建时间', type: 'string')]
    public mixed $createdAt;

    #[Property(property: 'updated_at', title: '更新时间', type: 'string')]
    public mixed $updatedAt;

    public function __construct(MemberAuth $model)
    {
        $this->id = $model->id;
        $this->user_id = $model->user_id;
        $this->card_front_url = $model->card_front_url;
        $this->card_back_url = $model->card_back_url;
        $this->status = $model->status;
        $this->updated_by = $model->updated_by;
        $this->createdAt = $model->created_at;
        $this->updatedAt = $model->updated_at;
    }

    public function jsonSerialize(): mixed
    {
        return ['id' => $this->id, 'user_id' => $this->user_id, 'card_front_url' => $this->card_front_url, 'card_back_url' => $this->card_back_url, 'status' => $this->status, 'updated_by' => $this->updated_by, 'created_at' => $this->createdAt, 'updated_at' => $this->updatedAt];
    }
}
