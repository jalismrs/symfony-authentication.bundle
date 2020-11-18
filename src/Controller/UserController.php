<?php

declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\Controller;

use Jalismrs\Symfony\Bundle\JalismrsApiMiddlewareBundle\IsApiControllerInterface;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService\UserControllerService;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\IsAuthenticatedControllerInterface;
use Jalismrs\Symfony\Common\ControllerAbstract;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 *
 * @package Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\Controller
 */
class UserController extends
    ControllerAbstract implements
    IsAuthenticatedControllerInterface,
    IsApiControllerInterface
{
    /**
     * controllerService
     *
     * @var \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService\UserControllerService
     */
    private UserControllerService $controllerService;
    
    /**
     * UserController constructor.
     *
     * @param \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService\UserControllerService $controllerService
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        UserControllerService $controllerService
    ) {
        $this->controllerService = $controllerService;
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
     * @throws \UnexpectedValueException
     */
    public function index(
        Request $request
    ) : JsonResponse {
        $data = $this->controllerService->index();
        
        return $this->returnJson(
            $request,
            $data,
        );
    }
}
