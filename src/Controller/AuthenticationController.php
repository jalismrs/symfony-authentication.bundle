<?php

declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\Controller;

use Jalismrs\Symfony\Bundle\JalismrsApiMiddlewareBundle\IsApiControllerInterface;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService\AuthenticationControllerService;
use Jalismrs\Symfony\Common\ControllerAbstract;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AuthenticationController
 *
 * @package Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\Controller
 */
class AuthenticationController extends
    ControllerAbstract implements
    IsApiControllerInterface
{
    /**
     * controllerService
     *
     * @var \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService\AuthenticationControllerService
     */
    private AuthenticationControllerService $controllerService;
    
    /**
     * AuthenticationController constructor.
     *
     * @param \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService\AuthenticationControllerService $authenticationControllerService
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        AuthenticationControllerService $authenticationControllerService
    ) {
        $this->controllerService = $authenticationControllerService;
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
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function index(
        Request $request
    ) : JsonResponse {
        $data = $this->controllerService->index($request);
        
        return $this->returnJson(
            $request,
            $data,
        );
    }
}
