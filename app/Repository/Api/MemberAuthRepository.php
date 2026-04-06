<?php

namespace App\Repository\Api;

use App\Model\Api\Member;
use App\Model\Api\MemberAuth;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Hyperf\Collection\Collection;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Paginator\AbstractPaginator;

final class MemberAuthRepository extends IRepository
{

    public function __construct(
        protected readonly MemberAuth $model,
    ) {}

    public function findByUserId(string $member_id): MemberAuth|bool
    {
        $is_exist = $this->model->newQuery()
            ->where('member_id', $member_id)
            ->first();
        if (!$is_exist) {
            return false;
        }

        return $is_exist;
    }

    public function handleItems(Collection $items): Collection
    {
        $memberModel = new Member();
        $memberRepository = new MemberRepository($memberModel);
        $memberIds = $items->pluck('member_id')->filter()->unique()->toArray();
        $members = $memberRepository->getMembersByIds($memberIds);
        $memberMap = collect($members)->keyBy('id');

        foreach ($items as $item) {
            $item->member = $memberMap->get($item->member_id);
        }

        return $items;
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(Arr::get($params, 'member_id'), static function (Builder $query, $member_id) {
                $query->where('member_id', $member_id);
            })
            ->when(Arr::exists($params, 'status'), static function (Builder $query) use ($params) {
                $query->where('status', Arr::get($params, 'status'));
            });
    }
}
