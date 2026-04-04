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
        $user = $this->memberService->getInfo($this->id());
        Context::set('current_member', $user);
        return $user;
    }

    public function refresh(): array
    {
        return $this->memberService->refreshToken($this->getToken());
    }

    public function id(): int
    {
        return (int) $this->getToken()->claims()->get(RegisteredClaims::ID);
    }
}