<?php

declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService;

use ArrayObject;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use function vsprintf;

/**
 * Class AuthenticationControllerService
 *
 * @package Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService
 */
class AuthenticationControllerService
{
    public const REQUEST_PARAMETER = 'externalUserJwt';
    
    /**
     * userService
     *
     * @var \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService
     */
    private UserService $userService;
    
    /**
     * AuthenticationControllerService constructor.
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \ArrayObject
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function index(
        Request $request
    ) : ArrayObject {
        $externalUserJwt = $request->request->get(self::REQUEST_PARAMETER);
        
        if ($externalUserJwt === null) {
            $message = vsprintf(
                'Missing required POST parameter: %s',
                [
                    self::REQUEST_PARAMETER,
                ]
            );
            
            throw new BadRequestHttpException(
                $message,
            );
        }
        
        $stalactiteJwt = $this->userService->login($externalUserJwt);
        
        return new ArrayObject(
            [
                'jwt' => $stalactiteJwt,
            ]
        );
    }
}
