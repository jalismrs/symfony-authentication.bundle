<?php
declare(strict_types = 1);

namespace Tests;

use Jalismrs\Stalactite\Client\Data\Model\User as StalactiteUser;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteService;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

/**
 * Class UserServiceTest
 *
 * @package Tests
 *
 * @covers  \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService
 */
final class UserServiceTest extends
    TestCase
{
    /**
     * mockStalactiteService
     *
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteService
     */
    private MockObject $mockStalactiteService;
    
    /**
     * testLogin
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testLogin() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt = UserServiceProvider::JWT;
        
        $externalUserJwt = 'externalUserJwt';
        
        // expect
        $this->mockStalactiteService
            ->expects(self::once())
            ->method('login')
            ->with(
                self::equalTo($externalUserJwt)
            )
            ->willReturn($jwt);
        
        // act
        $output = $systemUnderTest->login($externalUserJwt);
        
        // assert
        self::assertSame(
            $jwt,
            $output
        );
    }
    
    /**
     * createSUT
     *
     * @return \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\UserService
     */
    private function createSUT() : UserService
    {
        return new UserService(
            $this->mockStalactiteService,
        );
    }
    
    /**
     * testGetUser
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function testGetUser() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt            = UserServiceProvider::JWT;
        $stalactiteUser = new StalactiteUser();
        
        $systemUnderTest->setJwt($jwt);
        
        // expect
        $this->mockStalactiteService
            ->expects(self::once())
            ->method('getUser')
            ->with(
                self::equalTo($jwt)
            )
            ->willReturn($stalactiteUser);
        $this->mockStalactiteService
            ->expects(self::once())
            ->method('getLeads')
            ->with(
                self::equalTo($jwt)
            )
            ->willReturn([]);
        $this->mockStalactiteService
            ->expects(self::once())
            ->method('getPosts')
            ->with(
                self::equalTo($jwt)
            )
            ->willReturn([]);
        
        // act
        $systemUnderTest->getUser();
    }
    
    /**
     * testIsAuthenticated
     *
     * @return void
     *
     * @throws \Jalismrs\Stalactite\Client\Exception\ClientException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function testIsAuthenticated() : void {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt = UserServiceProvider::JWT;
        
        $systemUnderTest->setJwt($jwt);
        
        // expect
        $this->mockStalactiteService
            ->expects(self::once())
            ->method('validate')
            ->with(
                self::equalTo($jwt)
            )
            ->willReturn(true);
        
        // act
        $output = $systemUnderTest->isAuthenticated();
        
        // assert
        self::assertTrue(
            $output,
        );
    }
    
    /**
     * testGetStalactiteService
     *
     * @return void
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetStalactiteService() : void {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        // act
        $output = $systemUnderTest->getStalactiteService();
        
        // assert
        self::assertSame(
            $this->mockStalactiteService,
            $output,
        );
    }
    
    /**
     * testHasJwt
     *
     * @return void
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testHasJwt() : void {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        // act
        $output = $systemUnderTest->hasJwt();
        
        // assert
        self::assertFalse(
            $output,
        );
    }
    
    /**
     * testGetJwt
     *
     * @return void
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function testGetJwt() : void {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt = UserServiceProvider::JWT;
        
        $systemUnderTest->setJwt($jwt);
        
        // act
        $output = $systemUnderTest->getJwt();
        
        // assert
        self::assertSame(
            $jwt,
            $output,
        );
    }
    
    /**
     * testGetJwtThrowsUnexpectedValueException
     *
     * @return void
     *
     * @throws \UnexpectedValueException
     */
    public function testGetJwtThrowsUnexpectedValueException() : void {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        // expect
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('should not be null at this point');
        
        // act
        $systemUnderTest->getJwt();
    }
    
    /**
     * setUp
     *
     * @return void
     */
    protected function setUp() : void
    {
        parent::setUp();
        
        $this->mockStalactiteService = $this->createMock(StalactiteService::class);
    }
}
