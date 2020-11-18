<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle;

use Jalismrs\Stalactite\Client\Data\Model\User as StalactiteUser;
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
     *
     * @codeCoverageIgnore
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
     * @throws \UnexpectedValueException
     */
    public function isAuthenticated() : bool
    {
        $jwt = $this->getJwt();
        
        return $this->stalactiteService->validate($jwt);
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
                'should not be null at this point',
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
     * hasJwt
     *
     * @return bool
     */
    public function hasJwt() : bool
    {
        return $this->jwt !== null;
    }
    
    /**
     * getUser
     *
     * @return \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\User
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function getUser() : User
    {
        if ($this->user === null) {
            $jwt = $this->getJwt();
            
            $stalactiteUser = $this->fetchStalactiteUser($jwt);
            
            $this->user = $this->createUser($stalactiteUser);
        }
        
        return $this->user;
    }
    
    /**
     * fetchStalactiteUser
     *
     * @param string $jwt
     *
     * @return \Jalismrs\Stalactite\Client\Data\Model\User
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    protected function fetchStalactiteUser(
        string $jwt
    ) : StalactiteUser {
        $stalactiteUser  = $this->stalactiteService->getUser($jwt);
        $stalactiteLeads = $this->stalactiteService->getLeads($jwt);
        $stalactitePosts = $this->stalactiteService->getPosts($jwt);
        
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
