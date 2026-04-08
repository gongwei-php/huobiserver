<?php

namespace App\Service\Api;

use Closure;
use Mine\JwtAuth\Interfaces\CheckTokenInterface;
use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\UnencryptedToken;
use Mine\Jwt\Factory;
use Mine\Jwt\JwtInterface;
use App\Exception\BusinessException;
use App\Exception\JwtInBlackException;
use App\Repository\Api\MemberRepository;
use App\Http\Common\ResultCode;
use App\Model\Api\Member;
use App\Model\Api\MemberVip;
use App\Model\Api\MemberWallet;
use App\Service\IService;
use Hyperf\DbConnection\Db;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

final class MemberService extends IService implements CheckTokenInterface
{
    /**
     * @var string jwt场景
     */
    private string $jwt = 'default';

    public function __construct(
        protected readonly MemberRepository $repository,
        protected readonly Factory          $jwtFactory,
        protected readonly CacheInterface   $cache
    ) {}

    /**
     * @return array<string,int|string>
     */
    public function register(string $account, string $phone, string $password, string $ip): array
    {
        $code = 0;
        $data = [];
        $is_exists = $this->repository->findByAccount($account);
        if ($is_exists) {
            $msg = '해당 사용자가 등록되어 있으니 직접 로그인해 주세요';
            return [
                'code' => $code,
                'msg' => $msg,
                'data' => $data
            ];
        }
        $is_exists = $this->repository->findByPhone($phone);
        if ($is_exists) {
            $msg = '해당 휴대폰 번호는 이미 등록된 번호입니다';
            return [
                'code' => $code,
                'msg' => $msg,
                'data' => $data
            ];
        }

        $params = [
            'account' => $account,
            'password' => $password,
            'vip_level_id' => 0,
            'phone' => $phone,
            'avatar' => '',
            'status' => 1,
            'login_ip' => $ip,
            'login_time' => date('Y-m-d H:i:s'),
        ];
        $member = Db::transaction(function () use ($params) {
            /** @var Member $entity */
            $entity = $this->repository->create($params);
            $balance = 0;
            $profit = 0;
            $o_wallet = new MemberWallet();
            $o_wallet->member_id = $entity->id;
            $o_wallet->balance = $balance;
            $o_wallet->total_profit = $profit;
            $o_wallet->save();
            return $entity;
        });


        $jwt = $this->getJwt();
        $code = 1;
        $msg = '';
        return [
            'code' => $code,
            'msg' => $msg,
            'data' => [
                'access_token' => $jwt->builderAccessToken((string)$member->id)->toString(),
                'refresh_token' => $jwt->builderRefreshToken((string)$member->id)->toString(),
                'expire_at' => (int)$jwt->getConfig('ttl', 0),
            ]
        ];
    }

    /**
     * @return array<string,int|string>
     */
    public function login(string $account, string $password): array
    {
        $code = 0;
        $data = [];
        $member = $this->repository->findByAccount($account);
        if (!$member) {
            $msg = '사용자가 등록되지 않았습니다';
            return [
                'code' => $code,
                'msg' => $msg,
                'data' => $data
            ];
        }
        if (!$member->verifyPassword($password)) {
            $msg = '계정 또는 비밀번호가 잘못되었습니다';
            return [
                'code' => $code,
                'msg' => $msg,
                'data' => $data
            ];
        }
        if ($member->status->isDisable()) {
            $msg = '계정이 비활성화되었습니다';
            return [
                'code' => $code,
                'msg' => $msg,
                'data' => $data
            ];
        }
        $jwt = $this->getJwt();
        return [
            'access_token' => $jwt->builderAccessToken((string)$member->id)->toString(),
            'refresh_token' => $jwt->builderRefreshToken((string)$member->id)->toString(),
            'expire_at' => (int)$jwt->getConfig('ttl', 0),
        ];
    }

    public function checkJwt(UnencryptedToken $token): void
    {
        $this->getJwt()->hasBlackList($token) && throw new JwtInBlackException();
    }

    public function logout(UnencryptedToken $token): void
    {
        $this->getJwt()->addBlackList($token);
    }

    public function getJwt(): JwtInterface
    {
        return $this->jwtFactory->get($this->jwt);
    }

    /**
     * @return array<string,int|string>
     */
    public function refreshToken(UnencryptedToken $token): array
    {
        return value(static function (JwtInterface $jwt) use ($token) {
            $jwt->addBlackList($token);
            return [
                'access_token' => $jwt->builderAccessToken($token->claims()->get(RegisteredClaims::ID))->toString(),
                'refresh_token' => $jwt->builderRefreshToken($token->claims()->get(RegisteredClaims::ID))->toString(),
                'expire_at' => (int) $jwt->getConfig('ttl', 0),
            ];
        }, $this->getJwt());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getInfo(int $id): ?Member
    {

        if ($this->cache->has((string) $id)) {
            return $this->cache->get((string) $id);
        }

        $member = $this->repository->findById((string) $id);
        if (! $member) {
            return null;
        }

        $vip_level_id = $member->vip_level_id;
        $vip = MemberVip::where('vip_level_id', $vip_level_id)->first();
        $vip_level = $vip->level;
        $wallet = MemberWallet::where('member_id', $member->id)->first();
        $balance = $wallet->balance ?? 0;
        $total_profit = $wallet->total_profit ?? 0;

        $member->setAttribute('vip_level', $vip_level);
        $member->setAttribute('balance', $balance);
        $member->setAttribute('total_profit', $total_profit);

        $this->cache->set((string) $id, $member, 60);

        return $member;
    }

    public function updateById(mixed $id, array $data): mixed
    {
        return Db::transaction(function () use ($id, $data) {
            /** @var null|Member $entity */
            $entity = $this->repository->findById($id);
            if (empty($entity)) {
                throw new BusinessException(ResultCode::NOT_FOUND);
            }
            $entity->fill($data)->save();
        });
    }
}
