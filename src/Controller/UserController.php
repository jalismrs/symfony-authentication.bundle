<?php

declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\Controller;

use ArrayObject;
use Jalismrs\Symfony\Bundle\JalismrsApiMiddlewareBundle\IsApiControllerInterface;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\IsAuthenticatedControllerInterface;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService;
use Jalismrs\Symfony\Common\ControllerAbstract;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 *
 * @package Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\Controller
 */
final class UserController extends
    ControllerAbstract implements
    IsAuthenticatedControllerInterface,
    IsApiControllerInterface
{
    /**
     * userService
     *
     * @var \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService
     */
    private UserService $userService;
    
    /**
     * UserController constructor.
     *
     * @param \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService $userService
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
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     * @throws \UnexpectedValueException
     */
    public function index(
        Request $request
    ) : JsonResponse {
        $user = $this->userService->getUser();
        
        $data = new ArrayObject(
            [
                'user' => $user,
            ]
        );
        
        return $this->returnJson(
            $request,
            $data,
        );
    }
}
