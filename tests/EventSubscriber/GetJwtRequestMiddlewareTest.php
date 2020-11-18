<?php
declare(strict_types = 1);

namespace Tests\EventSubscriber;

use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\EventSubscriber\GetJwtRequestMiddleware;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class ConvertJsonRequestMiddlewareTest
 *
 * @package Tests\EventSubscriber
 *
 * @covers  \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\EventSubscriber\GetJwtRequestMiddleware
 */
final class GetJwtRequestMiddlewareTest extends
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
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testOnKernelRequest() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt = 'JWT';
        $mockHttpKernel = $this->createMock(HttpKernelInterface::class);
        $testRequest    = new Request(
            [],
            [],
            [],
            [],
            [],
            [
                'HTTP_' . GetJwtRequestMiddleware::HEADER_NAME => $jwt,
            ],
        );
        
        $requestEvent = new RequestEvent(
            $mockHttpKernel,
            $testRequest,
            null
        );
        
        // expect
        $this->mockUserService
            ->expects(self::once())
            ->method('setJwt')
            ->with(
                self::equalTo($jwt),
            );
        
        // act
        $output = $systemUnderTest->onKernelRequest($requestEvent);
        
        // assert
        self::assertSame(
            $requestEvent,
            $output
        );
    }
    
    /**
     * createSUT
     *
     * @return \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\EventSubscriber\GetJwtRequestMiddleware
     */
    private function createSUT() : GetJwtRequestMiddleware
    {
        return new GetJwtRequestMiddleware(
            $this->mockUserService
        );
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
