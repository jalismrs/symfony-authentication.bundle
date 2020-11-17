<?php

declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\Controller;

use ArrayObject;
use Jalismrs\Symfony\Bundle\JalismrsApiMiddlewareBundle\IsApiControllerInterface;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService;
use Jalismrs\Symfony\Common\ControllerAbstract;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use function vsprintf;

/**
 * Class AuthenticationController
 *
 * @package Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\Controller
 */
final class AuthenticationController extends
    ControllerAbstract implements
    IsApiControllerInterface
{
    public const REQUEST_PARAMETER = 'externalUserJwt';
    
    /**
     * userService
     *
     * @var \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService
     */
    private UserService $userService;
    
    /**
     * AuthenticationController constructor.
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
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function index(
        Request $request
    ) : JsonResponse {
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
        
        $data = new ArrayObject(
            [
                'jwt' => $stalactiteJwt,
            ]
        );
        
        return $this->returnJson(
            $request,
            $data,
        );
    }
}
