<?php
declare(strict_types = 1);

namespace Tests\ControllerService;

use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService\UserControllerService;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\User;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class UserControllerServiceTest
 *
 * @package Tests\ControllerService
 *
 * @covers  \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService\UserControllerService
 */
final class UserControllerServiceTest extends
    TestCase
{
    /**
     * mockUserService
     *
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jalismrs\Symfony\Bundle\JalismrsUserBundle\UserService\UserUserService
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
     * @throws \UnexpectedValueException
     */
    public function testIndex() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $stalaciteUser = new \Jalismrs\Stalactite\Client\Data\Model\User();
        $user          = new User(
            $stalaciteUser,
        );
        
        // expect
        $this->mockUserService
            ->expects(self::once())
            ->method('getUser')
            ->willReturn($user);
        
        // act
        $output = $systemUnderTest
            ->index()
            ->getArrayCopy();
        
        // assert
        self::assertCount(
            1,
            $output,
        );
        self::assertArrayHasKey(
            'user',
            $output,
        );
        self::assertSame(
            $user,
            $output['user'],
        );
    }
    
    /**
     * createSUT
     *
     * @return \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\ControllerService\UserControllerService
     */
    private function createSUT() : UserControllerService
    {
        return new UserControllerService(
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
