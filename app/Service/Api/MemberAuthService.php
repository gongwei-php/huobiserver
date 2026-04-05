<?php

namespace App\Service\Api;

use App\Exception\BusinessException;
use App\Repository\Api\MemberAuthRepository;
use App\Http\Common\ResultCode;
use App\Model\Api\MemberAuth;
use App\Model\Enums\MemberAuth\Status;
use App\Service\IService;
use Hyperf\DbConnection\Db;
use Psr\SimpleCache\CacheInterface;

final class MemberAuthService extends IService
{
    public function __construct(
        protected readonly MemberAuthRepository $repository,
        protected readonly CacheInterface $cache
    ) {}

    public function page(array $params, int $page = 1, int $pageSize = 10, string $sort = '', string $order = ''): array
    {
        if (!empty($sort) && !empty($order)) {
            $params['order_by'] = $sort;
            $params['order_by_direction'] = $order;
        }
        return $this->repository->page($params, $page, $pageSize);
    }

    public function getInfo(int $id): ?MemberAuth
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
            /** @var MemberAuth $entity */
            $entity = parent::create($data);
            return $entity;
        });
    }

    public function updateById(mixed $id, array $data): mixed
    {
        return Db::transaction(function () use ($id, $data) {
            /** @var null|MemberAuth $entity */
            $entity = $this->repository->findById($id);
            if (empty($entity)) {
                throw new BusinessException(ResultCode::NOT_FOUND);
            }
            $entity->fill($data)->save();
        });
    }

    public function agreeById(mixed $id, mixed $update_by): mixed
    {
        return Db::transaction(function () use ($id, $update_by) {
            /** @var null|MemberAuth $entity */
            $entity = $this->repository->findById($id);
            if (empty($entity)) {
                throw new BusinessException(ResultCode::NOT_FOUND);
            }
            $entity->status = Status::Agree;
            $entity->update_by = $update_by;
            $entity->save();
        });
    }

    public function refuseById(mixed $id, mixed $update_by): mixed
    {
        return Db::transaction(function () use ($id, $update_by) {
            /** @var null|MemberAuth $entity */
            $entity = $this->repository->findById($id);
            if (empty($entity)) {
                throw new BusinessException(ResultCode::NOT_FOUND);
            }
            $entity->status = Status::Refuse;
            $entity->update_by = $update_by;
            $entity->save();
        });
    }
}
