<?php

declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService;

use ArrayObject;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService;

/**
 * Class UserControllerService
 *
 * @package Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService
 */
class UserControllerService
{
    /**
     * userService
     *
     * @var \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService
     */
    private UserService $userService;
    
    /**
     * UserControllerService constructor.
     *
     * @param \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService $userService
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
    }
    
    /**
     * index
     *
     * @return \ArrayObject
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function index() : ArrayObject
    {
        $user = $this->userService->getUser();
        
        return new ArrayObject(
            [
                'user' => $user,
            ]
        );
    }
}
