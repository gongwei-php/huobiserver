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

namespace App\Service;

use App\Model\Api\Member;
use App\Repository\Api\MemberRepository;
use Hyperf\Collection\Collection;

final class MemberService  extends IService
{
    public function __construct(
        protected readonly MemberRepository $repository
    ) {}

    public function handleItems(Collection $items): Collection
    {
        $memberIds = $items->pluck('member_id')->filter()->unique()->toArray();
        $members = $this->repository->getMembersByIds($memberIds);
        $memberMap = collect($members)->keyBy('id');

        foreach ($items as $item) {
            $item->member = $memberMap->get($item->member_id);
        }
        return $items;
    }

    public function page(array $params, int $page = 1, int $pageSize = 10, string $sort = '', string $order = ''): array
    {
        if (!empty($sort) && !empty($order)) {
            if ($order == 'desc') {
                $params['sortDesc'] = $sort;
            } elseif ($order == 'asc') {
                $params['sortAsc'] = $sort;
            } else {
                $params['sortAsc'] = $sort;
            }
        }
        return parent::page($params, $page, $pageSize);
    }

    public function resetPassword(?int $id): bool
    {
        if ($id === null) {
            return false;
        }
        $entity = $this->repository->findById($id);
        $entity->resetPassword();
        $entity->save();
        return true;
    }
}
