<?php
declare(strict_types = 1);

namespace Tests\Controller;

use ArrayObject;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\Controller\AuthenticationController;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService\AuthenticationControllerService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AuthenticationControllerTest
 *
 * @package Tests\Controller
 *
 * @covers  \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\Controller\AuthenticationController
 */
final class AuthenticationControllerTest extends
    TestCase
{
    /**
     * mockContainer
     *
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Container\ContainerInterface
     */
    private MockObject $mockContainer;
    /**
     * mockControllerService
     *
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService\AuthenticationControllerService
     */
    private MockObject $mockControllerService;
    
    /**
     * testIndex
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
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
        
        $request = new Request();
        
        // expect
        $this->mockControllerService
            ->expects(self::once())
            ->method('index')
            ->with(
                self::equalTo($request)
            )
            ->willReturn(new ArrayObject());
        
        // act
        $output = $systemUnderTest->index($request);
        
        // assert
        self::assertSame(
            '{}',
            $output->getContent(),
        );
    }
    
    /**
     * createSUT
     *
     * @return \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\Controller\AuthenticationController
     */
    private function createSUT() : AuthenticationController
    {
        $systemUnderTest = new AuthenticationController(
            $this->mockControllerService
        );
        
        $systemUnderTest->setContainer($this->mockContainer);
        
        return $systemUnderTest;
    }
    
    /**
     * setUp
     *
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();
        
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->mockControllerService = $this->createMock(AuthenticationControllerService::class);
    }
}
