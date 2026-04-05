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
use App\Schema\MemberAuthSchema;
use Hyperf\Validation\Request\FormRequest;
use Mine\Support\Request\ClientIpRequestTrait;

#[\Mine\Swagger\Attributes\FormRequest(
    schema: MemberAuthSchema::class,
    only: [
        'member_id',
        'card_front_url',
        'card_back_url',
        'status',
    ]
)]
class MemberAuthRequest extends FormRequest
{
    use ClientIpRequestTrait;
    use NoAuthorizeTrait;

    public function rules(): array
    {
        return [
            'member_id' => 'required',
            'card_front_url' => 'required',
            'card_back_url' => 'required',
            'status' => 'required',
        ];
    }

    public function attributes(): array
    {
        return [
            'level' => trans('memberauth.level'),
            'card_front_url' => trans('memberauth.card_front_url'),
            'card_back_url' => trans('memberauth.card_back_url'),
            'status' => trans('memberauth.status'),
        ];
    }
}
