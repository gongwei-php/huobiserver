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

use App\Model\Enums\Member\Status;
use App\Model\Api\MemberVip;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

#[Schema]
final class MemberVipSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '编号', type: 'int')]
    public ?int $id;

    #[Property(property: 'level', title: '等级', type: 'int')]
    public ?int $level;

    #[Property(property: 'status', title: '状态 (1正常 2停用)', type: 'int')]
    public ?Status $status;

    #[Property(property: 'updated_by', title: '操作员ID', type: 'int')]
    public ?int $updated_by;

    #[Property(property: 'created_at', title: '创建时间', type: 'string')]
    public mixed $createdAt;

    #[Property(property: 'updated_at', title: '更新时间', type: 'string')]
    public mixed $updatedAt;

    public function __construct(MemberVip $model)
    {
        $this->id = $model->id;
        $this->level = $model->level;
        $this->status = $model->status;
        $this->updated_by = $model->updated_by;
        $this->createdAt = $model->created_at;
        $this->updatedAt = $model->updated_at;
    }

    public function jsonSerialize(): mixed
    {
        return ['id' => $this->id, 'level' => $this->level, 'status' => $this->status, 'updated_by' => $this->updated_by, 'created_at' => $this->createdAt, 'updated_at' => $this->updatedAt];
    }
}
