<?php

declare(strict_types=1);


namespace App\Http\Api\Request\V1;

use App\Http\Common\Request\Traits\NoAuthorizeTrait;
use App\Schema\MemberVipSchema;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Collection\Arr;
use Mine\Support\Request\ClientIpRequestTrait;

#[\Mine\Swagger\Attributes\FormRequest(
    schema: MemberVipSchema::class,
    only: [
        'level',
        'status',
    ]
)]
class MemberVipRequest extends FormRequest
{
    use ClientIpRequestTrait;
    use NoAuthorizeTrait;

    public function rules(): array
    {
        return [
            'level' => 'required',
            'status' => 'required',
        ];
    }

    public function attributes(): array
    {
        return [
            'level' => trans('membervip.level'),
            'status' => trans('membervip.status'),
        ];
    }
}
