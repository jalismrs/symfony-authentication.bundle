<?php

declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle;

use Jalismrs\Stalactite\Client\Data\Model\User as StalactiteUser;

/**
 * Class User
 *
 * @package App\Auth
 */
class User
{
    /**
     * stalactiteUser
     *
     * @var \Jalismrs\Stalactite\Client\Data\Model\User
     */
    private StalactiteUser $stalactiteUser;
    
    /**
     * User constructor.
     *
     * @param \Jalismrs\Stalactite\Client\Data\Model\User $stalactiteUser
     */
    public function __construct(
        StalactiteUser $stalactiteUser
    ) {
        $this->stalactiteUser = $stalactiteUser;
    }
    
    /**
     * getStalactiteUser
     *
     * @return \Jalismrs\Stalactite\Client\Data\Model\User
     */
    public function getStalactiteUser() : StalactiteUser
    {
        return $this->stalactiteUser;
    }
}
