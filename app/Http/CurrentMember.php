<?php

namespace App\Http;

use Hyperf\Context\Context;
use App\Model\Api\Member;
use App\Service\Api\MemberService;
use Lcobucci\JWT\Token\RegisteredClaims;
use Mine\Jwt\Traits\RequestScopedTokenTrait;

final class CurrentMember
{
    use RequestScopedTokenTrait;

    public function __construct(
        private readonly MemberService $memberService
    ) {}

    public static function ctxMember(): ?Member
    {
        return Context::get('current_member');
    }

    public function member(): ?Member
    {
        if (Context::has('current_member')) {
            return self::ctxMember();
        }

        try {
            // 从 token 拿正确的用户 ID
            $token = $this->getToken();
            if (!$token) {
                return null;
            }

            $id = (int) $token->claims()->get(RegisteredClaims::ID);
            echo 'Current Member ID: ' . $id . PHP_EOL;
            if ($id <= 0) {
                return null;
            }

            $user = $this->memberService->getInfo($id);
            Context::set('current_member', $user);
            return $user;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function refresh(): \Closure
    {
        try {
            return $this->memberService->refreshToken($this->getToken());
        } catch (\Throwable $e) {
            return function () {};
        }
    }

    public function id(): int
    {
        try {
            $token = $this->getToken();
            if (!$token) {
                return 0;
            }
            $id = (int) $token->claims()->get(RegisteredClaims::ID);
            return $id > 0 ? $id : 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }
}