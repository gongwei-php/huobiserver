<?php

namespace App\Repository\Api;

use App\Model\Api\MemberAuth;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

final class MemberAuthRepository extends IRepository
{

    public function __construct(
        protected readonly MemberAuth $model,
    ) {}

    public function findByUserId(string $user_id): MemberAuth|bool
    {
        $is_exist = $this->model->newQuery()
            ->where('user_id', $user_id)
            ->first();
        if (!$is_exist) {
            return false;
        }

        return $is_exist;
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(Arr::get($params, 'user_id'), static function (Builder $query, $user_id) {
                $query->where('user_id', $user_id);
            })
            ->when(Arr::exists($params, 'status'), static function (Builder $query) use ($params) {
                $query->where('status', Arr::get($params, 'status'));
            });
    }
}
