<?php

namespace App\Http;

use App\Service\Api\MemberService;
use Lcobucci\JWT\Token\RegisteredClaims;
use Mine\Jwt\Traits\RequestScopedTokenTrait;

final class CurrentMember
{
    use RequestScopedTokenTrait;

    public function __construct(
        private readonly MemberService $memberService
    ) {}

    public function refresh(): array
    {
        return $this->memberService->refreshToken($this->getToken());
    }

    public function id(): int
    {
        return (int) $this->getToken()->claims()->get(RegisteredClaims::ID);
    }
}
