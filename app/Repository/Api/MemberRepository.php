<?php

namespace App\Repository\Api;

use App\Model\Api\Member;
use App\Repository\IRepository;

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
}
