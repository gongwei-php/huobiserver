<?php

namespace app\Repository\Api;

use App\Model\Api\Member;
use App\Repository\IRepository;

final class MemberRepository extends IRepository
{
    public function __construct(protected readonly Member $model) {}

    public function findByAccount(string $account): Member
    {
        // @phpstan-ignore-next-line
        return $this->model->newQuery()
            ->where('account', $account)
            ->firstOrFail();
    }
}