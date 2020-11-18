<?php
declare(strict_types = 1);

namespace Tests\ControllerService;

use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService\AuthenticationControllerService;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class AuthenticationControllerServiceTest
 *
 * @package Tests\ControllerService
 *
 * @covers  \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService\AuthenticationControllerService
 */
final class AuthenticationControllerServiceTest extends
    TestCase
{
    /**
     * mockUserService
     *
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService\AuthenticationUserService
     */
    private MockObject $mockUserService;
    
    /**
     * testIndex
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testIndex() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $externalUserJwt = 'externalUserJwt';
        $stalactiteJwt   = 'stalactiteJwt';
        
        $request = new Request(
            [],
            [
                AuthenticationControllerService::REQUEST_PARAMETER => $externalUserJwt,
            ],
        );
        
        // expect
        $this->mockUserService
            ->expects(self::once())
            ->method('login')
            ->with(
                self::equalTo($externalUserJwt)
            )
            ->willReturn($stalactiteJwt);
        
        // act
        $output = $systemUnderTest
            ->index($request)
            ->getArrayCopy();
        
        // assert
        self::assertCount(
            1,
            $output,
        );
        self::assertArrayHasKey(
            'jwt',
            $output,
        );
        self::assertSame(
            $stalactiteJwt,
            $output['jwt'],
        );
    }
    
    /**
     * testIndexThrowsBadRequestHttpException
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function testIndexThrowsBadRequestHttpException() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $request = new Request();
        
        // expect
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Missing required POST parameter: externalUserJwt');
        
        // act
        $systemUnderTest->index($request);
    }
    
    /**
     * createSUT
     *
     * @return \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService\AuthenticationControllerService
     */
    private function createSUT() : AuthenticationControllerService
    {
        return new AuthenticationControllerService(
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
