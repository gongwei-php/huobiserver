<?php

namespace App\Service\Api;

use App\Exception\BusinessException;
use App\Repository\Api\MemberVipRepository;
use App\Http\Common\ResultCode;
use App\Model\Api\MemberVip;
use App\Service\IService;
use Hyperf\DbConnection\Db;
use Psr\SimpleCache\CacheInterface;

final class MemberVipService extends IService
{
    public function __construct(
        protected readonly MemberVipRepository $repository,
        protected readonly CacheInterface $cache
    ) {}

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
        return $this->repository->page($params, $page, $pageSize);
    }

    public function getInfo(int $id): ?MemberVip
    {
        if ($this->cache->has((string) $id)) {
            return $this->cache->get((string) $id);
        }
        $user = $this->repository->findById((string) $id);
        $this->cache->set((string) $id, $user, 60);
        return $user;
    }

    public function create(array $data): mixed
    {
        return Db::transaction(function () use ($data) {
            /** @var MemberVip $entity */
            $entity = parent::create($data);
            return $entity;
        });
    }

    public function updateById(mixed $id, array $data): mixed
    {
        return Db::transaction(function () use ($id, $data) {
            /** @var null|MemberVip $entity */
            $entity = $this->repository->findById($id);
            if (empty($entity)) {
                throw new BusinessException(ResultCode::NOT_FOUND);
            }
            $entity->fill($data)->save();
        });
    }
}
