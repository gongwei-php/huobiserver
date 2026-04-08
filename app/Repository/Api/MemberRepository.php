<?php

namespace App\Repository\Api;

use App\Model\Api\Member;
use App\Model\Api\MemberVip;
use App\Model\Api\MemberWallet;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;
use Hyperf\Database\Model\Builder;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Paginator\AbstractPaginator;

final class MemberRepository extends IRepository
{
    public function __construct(protected readonly Member $model) {}

    public function handleItems(Collection $items): Collection
    {
        $a_vip_levels = collect($items)->pluck('vip_level_id')->filter()->unique()->toArray();
        $vip_repository = new MemberVipRepository(new MemberVip());
        $a_vips = $vip_repository->getVipByLevels($a_vip_levels);
        $a_vips = array_combine(array_column($a_vips, 'level'), $a_vips);

        $a_member_ids = collect($items)->keyBy('id')->toArray();
        $o_wallets = MemberWallet::whereIn('member_id', $a_member_ids)
            ->get();
        $a_walltes = $o_wallets ? $o_wallets->toArray() : [];
        $a_walltes = array_combine(array_column($a_walltes, 'member_id'), $a_walltes);

        foreach ($items as $item) {
            $vip_level_id = $item->vip_level_id;
            $member_id = $item->id;
            $vip_level = $a_vips[$vip_level_id]['level'] ?? 0;
            $balance = $a_walltes[$member_id]['balance'] ?? 0;
            $total_profit = $a_walltes[$member_id]['total_profit'] ?? 0;

            $balance = bcadd((string)$balance, '0', 3);
            $total_profit = bcadd((string)$total_profit, '0', 3);

            $balance = rtrim(rtrim($balance, '0'), '.');
            $total_profit = rtrim(rtrim($total_profit, '0'), '.');

            $item->vip_level = $vip_level;
            $item->balance = $balance;
            $item->total_profit = $total_profit;
        }

        return $items;
    }

    public function findByAccount(string $account): Member|bool
    {
        $is_exist = $this->model->newQuery()
            ->where('account', $account)
            ->first();
        if (!$is_exist) {
            return false;
        }

        return $is_exist;
    }

    public function findByPhone(string $phone): Member|bool
    {
        $is_exist = $this->model->newQuery()
            ->where('phone', $phone)
            ->first();
        if (!$is_exist) {
            return false;
        }

        return $is_exist;
    }

    public function getMembersByIds(array $ids = []): array|Collection
    {
        if (empty($ids)) {
            return [];
        }

        return $this->model->newQuery()
            ->select(['id', 'account', 'phone'])
            ->whereIn('id', $ids)
            ->get();
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(Arr::get($params, 'unique_account'), static function (Builder $query, $uniqueAccount) {
                $query->where('account', $uniqueAccount);
            })
            ->when(Arr::get($params, 'account'), static function (Builder $query, $account) {
                $query->where('account', 'like', '%' . $account . '%');
            })
            ->when(Arr::get($params, 'phone'), static function (Builder $query, $phone) {
                $query->where('phone', $phone);
            })
            ->when(Arr::get($params, 'vip_level_id'), static function (Builder $query, $vip_level_id) {
                $query->where('vip_level_id', $vip_level_id);
            })
            ->when(Arr::exists($params, 'status'), static function (Builder $query) use ($params) {
                $query->where('status', Arr::get($params, 'status'));
            });
    }
}
