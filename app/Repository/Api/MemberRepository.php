<?php

namespace App\Repository\Api;

use App\Model\Api\Member;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

final class MemberRepository extends IRepository
{
    public function __construct(protected readonly Member $model) {}

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

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(Arr::get($params, 'sortAsc'), static function (Builder $query, $sortAsc) {
                $query->order($sortAsc, 'asc');
            })
            ->when(Arr::get($params, 'sortDesc'), static function (Builder $query, $sortDesc) {
                $query->order($sortDesc, 'desc');
            })
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
