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

    public function agreeById(mixed $id, mixed $updated_by): mixed
    {
        return Db::transaction(function () use ($id, $updated_by) {
            /** @var null|MemberAuth $entity */
            $entity = $this->repository->findById($id);
            if (empty($entity)) {
                throw new BusinessException(ResultCode::NOT_FOUND);
            }
            $entity->status = Status::Agree->value;
            $entity->updated_by = $updated_by;
            $entity->save();
        });
    }

    public function refuseById(mixed $id, mixed $updated_by): mixed
    {
        return Db::transaction(function () use ($id, $updated_by) {
            /** @var null|MemberAuth $entity */
            $entity = $this->repository->findById($id);
            if (empty($entity)) {
                throw new BusinessException(ResultCode::NOT_FOUND);
            }
            $entity->status = Status::Refuse->value;
            $entity->updated_by = $updated_by;
            $entity->save();
        });
    }

    public function agreeAllByIds(mixed $ids, mixed $updated_by): mixed
    {
        return Db::transaction(function () use ($ids, $updated_by) {
            $this->repository->getModel()::query()
                ->whereIn('id', (array)$ids)
                ->update([
                    'status' => Status::Agree->value,
                    'updated_by' => $updated_by,
                ]);

            return true;
        });
    }

    public function refuseAllByIds(mixed $ids, mixed $updated_by): mixed
    {
        return Db::transaction(function () use ($ids, $updated_by) {
            $this->repository->getModel()::query()
                ->whereIn('id', (array)$ids)
                ->update([
                    'status' => Status::Refuse->value,
                    'updated_by' => $updated_by,
                ]);

            return true;
        });
    }
}
