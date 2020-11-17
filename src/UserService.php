<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle;

use Jalismrs\Stalactite\Client\Data\Model\User as StalactiteUser;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use UnexpectedValueException;

/**
 * Class UserService
 *
 * @package Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle
 */
class UserService
{
    /**
     * jwt
     *
     * @var string|null
     */
    private ?string $jwt = null;
    /**
     * @static
     * @var User|null
     */
    private ?User $user = null;
    /**
     * stalactiteService
     *
     * @var \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteService
     */
    private StalactiteService $stalactiteService;
    
    /**
     * UserService constructor.
     *
     * @param \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteService $stalactiteService
     */
    public function __construct(
        StalactiteService $stalactiteService
    ) {
        $this->stalactiteService = $stalactiteService;
    }
    
    /**
     * getStalactiteService
     *
     * @return \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteService
     */
    public function getStalactiteService() : StalactiteService
    {
        return $this->stalactiteService;
    }
    
    /**
     * login
     *
     * @param string $externalUserJwt
     *
     * @return string
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function login(
        string $externalUserJwt
    ) : string {
        return $this->stalactiteService->login($externalUserJwt);
    }
    
    /**
     * isAuthenticated
     *
     * @return bool
     *
     * @throws \Jalismrs\Stalactite\Client\Exception\ClientException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function isAuthenticated() : bool
    {
        return $this->jwt !== null
            &&
            $this->stalactiteService->validate($this->jwt);
    }
    
    /**
     * getJwt
     *
     * @return string
     *
     * @throws \UnexpectedValueException
     */
    public function getJwt() : string
    {
        if ($this->jwt === null) {
            throw new UnexpectedValueException(
                'Should not be null at this point'
            );
        }
        
        return $this->jwt;
    }
    
    /**
     * setJwt
     *
     * @param string $jwt
     *
     * @return void
     */
    public function setJwt(
        string $jwt
    ) : void {
        $this->jwt = $jwt;
    }
    
    /**
     * getUser
     *
     * @return \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\User
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     * @throws \UnexpectedValueException
     */
    public function getUser() : User
    {
        $user = $this->fetchUser();
        
        if (!$user instanceof User) {
            throw new UnauthorizedHttpException(
                '',
                'you are not connected'
            );
        }
        
        return $user;
    }
    
    /**
     * fetchUser
     *
     * @return \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\User|null
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function fetchUser() : ?User
    {
        if ($this->user === null) {
            $stalactiteUser = $this->fetchStalactiteUser();
            
            $this->user = $this->createUser(
                $stalactiteUser
            );
        }
        
        return $this->user;
    }
    
    /**
     * fetchStalactiteUser
     *
     * @return \Jalismrs\Stalactite\Client\Data\Model\User
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    protected function fetchStalactiteUser() : StalactiteUser
    {
        $stalactiteUser  = $this->stalactiteService->getUser($this->jwt);
        $stalactiteLeads = $this->stalactiteService->getLeads($this->jwt);
        $stalactitePosts = $this->stalactiteService->getPosts($this->jwt);
        
        $stalactiteUser->setLeads($stalactiteLeads);
        $stalactiteUser->setPosts($stalactitePosts);
        
        return $stalactiteUser;
    }
    
    /**
     * createUser
     *
     * @param \Jalismrs\Stalactite\Client\Data\Model\User $stalactiteUser
     *
     * @return \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\User
     */
    protected function createUser(
        StalactiteUser $stalactiteUser
    ) : User {
        return new User(
            $stalactiteUser
        );
    }
}
