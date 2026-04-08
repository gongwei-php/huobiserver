<?php

declare(strict_types=1);


namespace App\Model\Enums\MemberAuth;

use Hyperf\Constants\Annotation\Constants;
use Hyperf\Constants\Annotation\Message;
use Hyperf\Constants\EnumConstantsTrait;

#[Constants]
enum Status: int
{
    use EnumConstantsTrait;

    #[Message('memberauth.enums.status.1')]
    case Wait = 1;

    #[Message('memberauth.enums.status.2')]
    case Agree = 2;

    #[Message('memberauth.enums.status.3')]
    case Refuse = 3;

    public function isWait(): bool
    {
        return $this === self::Wait;
    }

    public function isAgree(): bool
    {
        return $this === self::Agree;
    }

    public function isRefuse(): bool
    {
        return $this === self::Refuse;
    }
}
