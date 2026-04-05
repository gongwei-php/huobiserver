<?php

namespace App\Repository\Api;

use App\Model\Api\Member;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;
use Hyperf\Database\Model\Builder;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Paginator\AbstractPaginator;

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

    public function getMembersByIds(array $ids = []): array|Collection
    {
        if (empty($ids)) {
            return [];
        }

        return $this->model->newQuery()
            ->whereIn('id', $ids)
            ->get();
    }

    public function page(array $params = [], ?int $page = null, ?int $pageSize = null): array
    {
        $result = $this->perQuery($this->getQuery(), $params)->paginate(
            perPage: $pageSize,
            pageName: static::PER_PAGE_PARAM_NAME,
            page: $page,
        );
        return $this->handlePage($result);
    }

    public function handleItems(Collection $items): Collection
    {
        $memberIds = $items->pluck('member_id')->filter()->unique()->toArray();
        $members = $this->getMembersByIds($memberIds);
        $memberMap = collect($members)->keyBy('id');

        foreach ($items as $item) {
            $item->member = $memberMap->get($item->member_id);
        }

        return $items;
    }

    public function handlePage(LengthAwarePaginatorInterface $paginator): array
    {
        if ($paginator instanceof AbstractPaginator) {
            $items = $paginator->getCollection();
        } else {
            $items = Collection::make($paginator->items());
        }
        $items = $this->handleItems($items);
        return [
            'list' => $items->toArray(),
            'total' => $paginator->total(),
        ];
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
