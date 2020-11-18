<?php
declare(strict_types = 1);

namespace Tests\EventSubscriber;

use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\EventSubscriber\IsAuthenticatedControllerMiddleware;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\IsAuthenticatedControllerInterface;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class ConvertJsonRequestMiddlewareTest
 *
 * @package Tests\EventSubscriber
 *
 * @covers  \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\EventSubscriber\IsAuthenticatedControllerMiddleware
 */
final class IsAuthenticatedControllerMiddlewareTest extends
    TestCase
{
    /**
     * mockUserService
     *
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService
     */
    private MockObject $mockUserService;
    
    /**
     * testOnKernelRequest
     *
     * @return void
     *
     * @throws \Jalismrs\Stalactite\Client\Exception\ClientException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     * @throws \UnexpectedValueException
     */
    public function testOnKernelRequest() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt            = 'JWT';
        $mockHttpKernel = $this->createMock(HttpKernelInterface::class);
        $testRequest    = new Request();
        
        $testController = new class() implements
            IsAuthenticatedControllerInterface {
            public function __invoke() : void
            {
            
            }
        };
        
        $controllerEvent = new ControllerEvent(
            $mockHttpKernel,
            [
                $testController,
                '__invoke',
            ],
            $testRequest,
            null
        );
        
        // expect
        $this->mockUserService
            ->expects(self::once())
            ->method('hasJwt')
            ->willReturn(true);
        $this->mockUserService
            ->expects(self::once())
            ->method('isAuthenticated')
            ->willReturn(true);
        
        // act
        $output = $systemUnderTest->onKernelController($controllerEvent);
        
        // assert
        self::assertSame(
            $controllerEvent,
            $output
        );
    }
    
    /**
     * createSUT
     *
     * @return \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\EventSubscriber\IsAuthenticatedControllerMiddleware
     */
    private function createSUT() : IsAuthenticatedControllerMiddleware
    {
        return new IsAuthenticatedControllerMiddleware(
            $this->mockUserService
        );
    }
    
    /**
     * testOnKernelControllerThrowsBadRequestHttpException
     *
     * @return void
     *
     * @throws \Jalismrs\Stalactite\Client\Exception\ClientException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     * @throws \UnexpectedValueException
     */
    public function testOnKernelControllerThrowsBadRequestHttpException() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $mockHttpKernel = $this->createMock(HttpKernelInterface::class);
        $testRequest    = new Request();
        
        $testController = new class() implements
            IsAuthenticatedControllerInterface {
            public function __invoke() : void
            {
            
            }
        };
        
        $testEvent = new ControllerEvent(
            $mockHttpKernel,
            [
                $testController,
                '__invoke',
            ],
            $testRequest,
            null
        );
        
        // expect
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('JWT must be provided with header X-API-JWT');
        $this->mockUserService
            ->expects(self::once())
            ->method('hasJwt')
            ->willReturn(false);
        
        // act
        $systemUnderTest->onKernelController($testEvent);
    }
    
    /**
     * testOnKernelControllerThrowsUnauthorizedHttpException
     *
     * @return void
     *
     * @throws \Jalismrs\Stalactite\Client\Exception\ClientException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     * @throws \UnexpectedValueException
     */
    public function testOnKernelControllerThrowsUnauthorizedHttpException() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $mockHttpKernel = $this->createMock(HttpKernelInterface::class);
        $testRequest    = new Request();
        
        $testController = new class() implements
            IsAuthenticatedControllerInterface {
            public function __invoke() : void
            {
            
            }
        };
        
        $testEvent = new ControllerEvent(
            $mockHttpKernel,
            [
                $testController,
                '__invoke',
            ],
            $testRequest,
            null
        );
        
        // expect
        $this->expectException(UnauthorizedHttpException::class);
        $this->expectExceptionMessage('You need to be authenticated');
        $this->mockUserService
            ->expects(self::once())
            ->method('hasJwt')
            ->willReturn(true);
        $this->mockUserService
            ->expects(self::once())
            ->method('isAuthenticated')
            ->willReturn(false);
        
        // act
        $systemUnderTest->onKernelController($testEvent);
    }
    
    /**
     * setUp
     *
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();
        
        $this->mockUserService = $this->createMock(UserService::class);
    }
}
