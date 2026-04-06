<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Http\Api\Request\V1;

use App\Http\Common\Request\Traits\NoAuthorizeTrait;
use App\Schema\MemberSchema;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Collection\Arr;
use Mine\Support\Request\ClientIpRequestTrait;

#[\Mine\Swagger\Attributes\FormRequest(
    schema: MemberSchema::class,
    only: [
        'account',
        'password',
    ]
)]
class MemberRequest extends FormRequest
{
    use ClientIpRequestTrait;
    use NoAuthorizeTrait;

    public function rules(): array
    {
        return [
            'account' => 'required|string',
            'password' => 'required|string|max:32',
        ];
    }

    public function attributes(): array
    {
        return [
            'account' => trans('user.account'),
            'password' => trans('user.password'),
        ];
    }

    public function ip(): string
    {
        return Arr::first($this->getClientIps(), static fn($ip) => $ip, '0.0.0.0');
    }
}
