<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\EventSubscriber;

use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\IsAuthenticatedControllerInterface;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService;
use Jalismrs\Symfony\Common\Helpers\EventHelpers;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class IsAuthenticatedControllerMiddleware
 *
 * @package Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\EventSubscriber
 */
final class IsAuthenticatedControllerMiddleware implements
    EventSubscriberInterface
{
    /**
     * userService
     *
     * @var \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService
     */
    private UserService $userService;
    
    /**
     * IsAuthenticatedControllerMiddleware constructor.
     *
     * @param \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService $userService
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
            KernelEvents::CONTROLLER => [
                'onKernelController',
                -1,
            ],
        ];
    }
    
    /**
     * onKernelController
     *
     * @param \Symfony\Component\HttpKernel\Event\ControllerEvent $controllerEvent
     *
     * @return \Symfony\Component\HttpKernel\Event\ControllerEvent
     *
     * @throws \Jalismrs\Stalactite\Client\Exception\ClientException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
    public function onKernelController(
        ControllerEvent $controllerEvent
    ) : ControllerEvent {
        $controller = EventHelpers::getController($controllerEvent);
        
        if (
            $controller instanceof IsAuthenticatedControllerInterface
            &&
            !$this->userService->isAuthenticated()
        ) {
            throw new UnauthorizedHttpException(
                '',
                'You need to be authenticated'
            );
        }
        
        return $controllerEvent;
    }
}
