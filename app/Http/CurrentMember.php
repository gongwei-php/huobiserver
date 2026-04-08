<?php

namespace App\Http;

use Hyperf\Context\Context;
use App\Model\Api\Member;
use App\Service\Api\MemberService;
use Lcobucci\JWT\Token\RegisteredClaims;
use Mine\Jwt\Traits\RequestScopedTokenTrait;
use Psr\SimpleCache\InvalidArgumentException;

final class CurrentMember
{
    use RequestScopedTokenTrait;

    public function __construct(
        private readonly MemberService $memberService
    ) {}

    /**
     * @throws InvalidArgumentException
     */
    public function member(): ?array
    {
        if (Context::has('current_member')) {
            return Context::get('current_member');
        }
        $a_member = $this->memberService->getInfo($this->id());
        Context::set('current_member', $a_member);
        return $a_member;
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
