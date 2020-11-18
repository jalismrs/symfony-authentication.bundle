<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\EventSubscriber;

use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class GetJwtRequestMiddleware
 *
 * @package Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\EventSubscriber
 */
final class GetJwtRequestMiddleware implements
    EventSubscriberInterface
{
    public const HEADER_NAME = 'X-API-JWT';
    
    /**
     * userService
     *
     * @var \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService
     */
    private UserService $userService;
    
    /**
     * GetJwtRequestMiddleware constructor.
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
     * getSubscribedEvents
     *
     * @static
     * @return array|array[]
     *
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents() : array
    {
        return [
            KernelEvents::REQUEST => [
                'onKernelRequest',
                0,
            ],
        ];
    }
    
    /**
     * onKernelRequest
     *
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $requestEvent
     *
     * @return \Symfony\Component\HttpKernel\Event\RequestEvent
     */
    public function onKernelRequest(
        RequestEvent $requestEvent
    ) : RequestEvent {
        $request = $requestEvent->getRequest();
        $jwt     = $request->headers->get(self::HEADER_NAME);
        if ($jwt !== null) {
            $this->userService->setJwt($jwt);
        }
        
        return $requestEvent;
    }
}
