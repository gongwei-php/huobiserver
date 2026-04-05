<?php

namespace App\Repository\Api;

use App\Model\Api\MemberVip;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Psr\Log\LoggerInterface;
use Hyperf\Database\Query\Builder as QueryBuilder;

final class MemberVipRepository extends IRepository
{

    protected LoggerInterface $log;

    public function __construct(
        protected readonly MemberVip $model,
        LoggerInterface $log
    ) {
        $this->log = $log;
    }

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

    public function perQuery(Builder $query, array $params): Builder
    {
        $query = parent::perQuery($query, $params);
        $this->log->error('vip 搜索参数', $params);
        if ($query->getQuery() instanceof QueryBuilder) {
            $rawSql = $query->getQuery()->toSql();          // 带 ? 的 SQL
            $bindings = $query->getQuery()->getBindings();   // 绑定参数

            // 拼接成完整可执行 SQL
            foreach ($bindings as $binding) {
                $rawSql = preg_replace('/\?/', "'{$binding}'", $rawSql, 1);
            }

            // 写日志
            $this->log->error('VIP 执行 SQL：' . $rawSql);
        }
        return $query;
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query
            ->when(Arr::get($params, 'sortAsc'), static function (Builder $query, $sortAsc) {
                $query->orderBy($sortAsc, 'asc');
            })
            ->when(Arr::get($params, 'sortDesc'), static function (Builder $query, $sortDesc) {
                $query->orderBy($sortDesc, 'desc');
            })
            ->when(Arr::get($params, 'level'), static function (Builder $query, $level) {
                $query->where('level', $level);
            })
            ->when(Arr::exists($params, 'status'), static function (Builder $query) use ($params) {
                $query->where('status', Arr::get($params, 'status'));
            });
    }
}
