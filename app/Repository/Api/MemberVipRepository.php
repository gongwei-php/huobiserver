<?php

namespace App\Repository\Api;

use App\Model\Api\MemberVip;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

final class MemberVipRepository extends IRepository
{

    public function __construct(
        protected readonly MemberVip $model,
    ) {}

    public function findByLevel(string $level): MemberVip|bool
    {
        $is_exist = $this->model->newQuery()
            ->where('level', $level)
            ->first();
        if (!$is_exist) {
            return false;
        }

        return $is_exist;
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(Arr::get($params, 'level'), static function (Builder $query, $level) {
                $query->where('level', $level);
            })
            ->when(Arr::exists($params, 'status'), static function (Builder $query) use ($params) {
                $query->where('status', Arr::get($params, 'status'));
            });
    }
}
