<?php

namespace App\Service\Api;

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
use App\Service\IService;
use Hyperf\DbConnection\Db;
use Psr\SimpleCache\CacheInterface;

final class MemberService extends IService implements CheckTokenInterface
{
    /**
     * @var string jwt场景
     */
    private string $jwt = 'default';

    public function __construct(
        protected readonly MemberRepository $repository,
        protected readonly Factory $jwtFactory,
        protected readonly CacheInterface $cache
    ) {}

    /**
     * @return array<string,int|string>
     */
    public function register(string $account, string $phone, string $password, string $ip): array
    {
        $code = 0;
        $msg = '';
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
        $params = [
            'account' => $account,
            'password' => $password,
            'vip_level_id' => 0,
            'phone' => $phone,
            'avatar' => '',
            'status' => 1,
            'login_ip' => $ip,
            'login_time' => time(),
            'remark' => ''
        ];
        $member = parent::create($params);

        $jwt = $this->getJwt();

        $code = 1;
        $msg = '';
        return [
            'code' => $code,
            'msg' => $msg,
            'data' => [
                'access_token' => $jwt->builderAccessToken((string) $member->id)->toString(),
                'refresh_token' => $jwt->builderRefreshToken((string) $member->id)->toString(),
                'expire_at' => (int) $jwt->getConfig('ttl', 0),
            ]
        ];
    }

    /**
     * @return array<string,int|string>
     */
    public function login(string $account, string $password): array
    {
        $member = $this->repository->findByAccount($account);
        if (!$member) {
            throw new BusinessException(ResultCode::USER_NOT_FOUND, trans('auth.password_error'));
        }
        if (! $member->verifyPassword($password)) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, trans('auth.password_error'));
        }
        if ($member->status->isDisable()) {
            throw new BusinessException(ResultCode::DISABLED);
        }
        $jwt = $this->getJwt();
        return [
            'access_token' => $jwt->builderAccessToken((string) $member->id)->toString(),
            'refresh_token' => $jwt->builderRefreshToken((string) $member->id)->toString(),
            'expire_at' => (int) $jwt->getConfig('ttl', 0),
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

    public function getInfo(int $id): ?Member
    {
        if ($this->cache->has((string) $id)) {
            return $this->cache->get((string) $id);
        }
        $user = $this->repository->findById((string) $id);
        $this->cache->set((string) $id, $user, 60);
        return $user;
    }

    public function resetPassword(?int $id): bool
    {
        if ($id === null) {
            return false;
        }
        $entity = $this->repository->findById($id);
        $entity->resetPassword();
        $entity->save();
        return true;
    }

    public function create(array $data): mixed
    {
        return Db::transaction(function () use ($data) {
            /** @var Member $entity */
            $entity = parent::create($data);
            return $entity;
        });
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
