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
            return Context::get('current_member');
        }

        try {
            $id = $this->id();
            $user = $this->memberService->getInfo($id);
            Context::set('current_member', $user);
            return $user;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function refresh(): ?array
    {
        try {
            return $this->memberService->refreshToken($this->getToken());
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function id(): int
    {
        try {
            $token = $this->getToken();
            if (! $token) {
                return 0;
            }
            return (int) $token->claims()->get(RegisteredClaims::ID);
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
