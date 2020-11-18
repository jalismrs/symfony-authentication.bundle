<?php
declare(strict_types = 1);

namespace Tests;

use Jalismrs\Stalactite\Client\Authentication\Model\ClientApp;
use Jalismrs\Stalactite\Client\Data\Model\User;
use Jalismrs\Stalactite\Client\Exception\ClientException;
use Jalismrs\Stalactite\Client\Service;
use Jalismrs\Stalactite\Client\Util\Response;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteService;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;
use UnexpectedValueException;

/**
 * Class StalactiteServiceTest
 *
 * @package Tests
 *
 * @covers  \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteService
 */
final class StalactiteServiceTest extends
    TestCase
{
    /**
     * mockClientApp
     *
     * @var \PHPUnit\Framework\MockObject\MockObject|\Jalismrs\Stalactite\Client\Authentication\Model\ClientApp
     */
    private MockObject $mockClientApp;
    /**
     * mockParser
     *
     * @var \Lcobucci\JWT\Parser|\PHPUnit\Framework\MockObject\MockObject
     */
    private MockObject $mockParser;
    /**
     * mockService
     *
     * @var \Jalismrs\Stalactite\Client\Service|\PHPUnit\Framework\MockObject\MockObject
     */
    private MockObject $mockService;
    /**
     * testLogger
     *
     * @var \Psr\Log\Test\TestLogger
     */
    private TestLogger $testLogger;
    
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
        
        $googleJwt                 = 'googleJwt';
        $jwt                       = StalactiteServiceProvider::JWT;
        $mockAuthenticationService = $this->createMock(\Jalismrs\Stalactite\Client\Authentication\Service::class);
        $mockTokenService          = $this->createMock(\Jalismrs\Stalactite\Client\Authentication\Token\Service::class);
        $response                  = new Response(
            200,
            [],
            [
                'token' => $jwt,
            ]
        );
        
        // expect
        $this->mockService
            ->expects(self::once())
            ->method('authentication')
            ->willReturn($mockAuthenticationService);
        $mockAuthenticationService
            ->expects(self::once())
            ->method('tokens')
            ->willReturn($mockTokenService);
        $mockTokenService
            ->expects(self::once())
            ->method('login')
            ->with(
                self::equalTo($this->mockClientApp),
                self::equalTo($googleJwt)
            )
            ->willReturn($response);
        
        // act
        $output = $systemUnderTest->login($googleJwt);
        
        // assert
        self::assertSame(
            $jwt,
            $output
        );
    }
    
    /**
     * createSUT
     *
     * @return \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteService
     */
    private function createSUT() : StalactiteService
    {
        return new StalactiteService(
            $this->mockClientApp,
            $this->testLogger,
            $this->mockParser,
            $this->mockService
        );
    }
    
    /**
     * testCheckResponseThrowsStalactiteException
     *
     * @param        $providedInput
     * @param string $providedOutput
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @dataProvider \Tests\StalactiteServiceProvider::provideCheckResponseThrowsStalactiteExecption
     */
    public function testCheckResponseThrowsStalactiteException(
        $providedInput,
        string $providedOutput
    ) : void {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $googleJwt                 = 'googleJwt';
        $mockAuthenticationService = $this->createMock(\Jalismrs\Stalactite\Client\Authentication\Service::class);
        $mockTokenService          = $this->createMock(\Jalismrs\Stalactite\Client\Authentication\Token\Service::class);
        $response                  = new Response(
            500,
            [],
            $providedInput
        );
        
        // expect
        $this->expectException(StalactiteException::class);
        $this->expectExceptionMessage($providedOutput);
        $this->mockService
            ->expects(self::once())
            ->method('authentication')
            ->willReturn($mockAuthenticationService);
        $mockAuthenticationService
            ->expects(self::once())
            ->method('tokens')
            ->willReturn($mockTokenService);
        $mockTokenService
            ->expects(self::once())
            ->method('login')
            ->with(
                self::equalTo($this->mockClientApp),
                self::equalTo($googleJwt)
            )
            ->willReturn($response);
        
        // act
        $systemUnderTest->login($googleJwt);
    }
    
    /**
     * testLoginThrowsStalactiteException
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testLoginThrowsStalactiteException() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $googleJwt = 'googleJwt';
        
        // expect
        $this->expectException(StalactiteException::class);
        $this->expectExceptionMessage('Error while logging with Stalactite');
        $this->mockService
            ->expects(self::once())
            ->method('authentication')
            ->willThrowException(
                new ClientException()
            );
        
        // act
        $systemUnderTest->login($googleJwt);
    }
    
    /**
     * testGetUser
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function testGetUser() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt              = StalactiteServiceProvider::JWT;
        $mockDataService  = $this->createMock(\Jalismrs\Stalactite\Client\Data\Service::class);
        $mockUsersService = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Service::class);
        $mockMeService    = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Me\Service::class);
        $user             = new User();
        $response         = new Response(
            200,
            [],
            $user
        );
        $token            = new Token();
        
        // expect
        $this->mockParser
            ->expects(self::once())
            ->method('parse')
            ->with(
                self::equalTo($jwt)
            )
            ->willReturn($token);
        $this->mockService
            ->expects(self::once())
            ->method('data')
            ->willReturn($mockDataService);
        $mockDataService
            ->expects(self::once())
            ->method('users')
            ->willReturn($mockUsersService);
        $mockUsersService
            ->expects(self::once())
            ->method('me')
            ->willReturn($mockMeService);
        $mockMeService
            ->expects(self::once())
            ->method('get')
            ->with(
                self::equalTo($token)
            )
            ->willReturn($response);
        
        // act
        $output = $systemUnderTest->getUser($jwt);
        
        // assert
        self::assertSame(
            $user,
            $output
        );
    }
    
    /**
     * testGetUserThrowsUnexpectedValueException
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function testGetUserThrowsUnexpectedValueException() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt              = StalactiteServiceProvider::JWT;
        $mockDataService  = $this->createMock(\Jalismrs\Stalactite\Client\Data\Service::class);
        $mockUsersService = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Service::class);
        $mockMeService    = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Me\Service::class);
        $response         = new Response(
            200,
            [],
            'test'
        );
        $token            = new Token();
        
        // expect
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('not an User');
        $this->mockParser
            ->expects(self::once())
            ->method('parse')
            ->with(
                self::equalTo($jwt)
            )
            ->willReturn($token);
        $this->mockService
            ->expects(self::once())
            ->method('data')
            ->willReturn($mockDataService);
        $mockDataService
            ->expects(self::once())
            ->method('users')
            ->willReturn($mockUsersService);
        $mockUsersService
            ->expects(self::once())
            ->method('me')
            ->willReturn($mockMeService);
        $mockMeService
            ->expects(self::once())
            ->method('get')
            ->with(
                self::equalTo($token)
            )
            ->willReturn($response);
        
        // act
        $systemUnderTest->getUser($jwt);
    }
    
    /**
     * testGetUserThrowsStalactiteException
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function testGetUserThrowsStalactiteException() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt              = StalactiteServiceProvider::JWT;
        $mockDataService  = $this->createMock(\Jalismrs\Stalactite\Client\Data\Service::class);
        $mockUsersService = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Service::class);
        $mockMeService    = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Me\Service::class);
        $token            = new Token();
        
        // expect
        $this->expectException(StalactiteException::class);
        $this->expectExceptionMessage('Error while querying user with Stalactite');
        $this->mockParser
            ->expects(self::once())
            ->method('parse')
            ->with(
                self::equalTo($jwt)
            )
            ->willReturn($token);
        $this->mockService
            ->expects(self::once())
            ->method('data')
            ->willReturn($mockDataService);
        $mockDataService
            ->expects(self::once())
            ->method('users')
            ->willReturn($mockUsersService);
        $mockUsersService
            ->expects(self::once())
            ->method('me')
            ->willReturn($mockMeService);
        $mockMeService
            ->expects(self::once())
            ->method('get')
            ->with(
                self::equalTo($token)
            )
            ->willThrowException(
                new ClientException()
            );
        
        // act
        $systemUnderTest->getUser($jwt);
    }
    
    /**
     * testGetLeads
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function testGetLeads() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt              = StalactiteServiceProvider::JWT;
        $mockDataService  = $this->createMock(\Jalismrs\Stalactite\Client\Data\Service::class);
        $mockUsersService = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Service::class);
        $mockMeService    = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Me\Service::class);
        $mockLeadsService = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Me\Lead\Service::class);
        $leads            = [];
        $response         = new Response(
            200,
            [],
            $leads
        );
        $token            = new Token();
        
        // expect
        $this->mockParser
            ->expects(self::once())
            ->method('parse')
            ->with(
                self::equalTo($jwt)
            )
            ->willReturn($token);
        $this->mockService
            ->expects(self::once())
            ->method('data')
            ->willReturn($mockDataService);
        $mockDataService
            ->expects(self::once())
            ->method('users')
            ->willReturn($mockUsersService);
        $mockUsersService
            ->expects(self::once())
            ->method('me')
            ->willReturn($mockMeService);
        $mockMeService
            ->expects(self::once())
            ->method('leads')
            ->willReturn($mockLeadsService);
        $mockLeadsService
            ->expects(self::once())
            ->method('all')
            ->with(
                self::equalTo($token)
            )
            ->willReturn($response);
        
        // act
        $output = $systemUnderTest->getLeads($jwt);
        
        // assert
        self::assertSame(
            $leads,
            $output
        );
    }
    
    /**
     * testGetLeadsThrowsUnexpectedValueException
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function testGetLeadsThrowsUnexpectedValueException() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt              = StalactiteServiceProvider::JWT;
        $mockDataService  = $this->createMock(\Jalismrs\Stalactite\Client\Data\Service::class);
        $mockUsersService = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Service::class);
        $mockMeService    = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Me\Service::class);
        $mockLeadsService = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Me\Lead\Service::class);
        $response         = new Response(
            200,
            [],
            'test'
        );
        $token            = new Token();
        
        // expect
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('not an array');
        $this->mockParser
            ->expects(self::once())
            ->method('parse')
            ->with(
                self::equalTo($jwt)
            )
            ->willReturn($token);
        $this->mockService
            ->expects(self::once())
            ->method('data')
            ->willReturn($mockDataService);
        $mockDataService
            ->expects(self::once())
            ->method('users')
            ->willReturn($mockUsersService);
        $mockUsersService
            ->expects(self::once())
            ->method('me')
            ->willReturn($mockMeService);
        $mockMeService
            ->expects(self::once())
            ->method('leads')
            ->willReturn($mockLeadsService);
        $mockLeadsService
            ->expects(self::once())
            ->method('all')
            ->with(
                self::equalTo($token)
            )
            ->willReturn($response);
        
        // act
        $systemUnderTest->getLeads($jwt);
    }
    
    /**
     * testGetLeadsThrowsStalactiteException
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function testGetLeadsThrowsStalactiteException() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt              = StalactiteServiceProvider::JWT;
        $mockDataService  = $this->createMock(\Jalismrs\Stalactite\Client\Data\Service::class);
        $mockUsersService = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Service::class);
        $mockMeService    = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Me\Service::class);
        $token            = new Token();
        
        // expect
        $this->expectException(StalactiteException::class);
        $this->expectExceptionMessage('Error while querying leads with Stalactite');
        $this->mockParser
            ->expects(self::once())
            ->method('parse')
            ->with(
                self::equalTo($jwt)
            )
            ->willReturn($token);
        $this->mockService
            ->expects(self::once())
            ->method('data')
            ->willReturn($mockDataService);
        $mockDataService
            ->expects(self::once())
            ->method('users')
            ->willReturn($mockUsersService);
        $mockUsersService
            ->expects(self::once())
            ->method('me')
            ->willReturn($mockMeService);
        $mockMeService
            ->expects(self::once())
            ->method('leads')
            ->willThrowException(
                new ClientException()
            );
        
        // act
        $systemUnderTest->getLeads($jwt);
    }
    
    /**
     * testGetPosts
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function testGetPosts() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt              = StalactiteServiceProvider::JWT;
        $mockDataService  = $this->createMock(\Jalismrs\Stalactite\Client\Data\Service::class);
        $mockUsersService = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Service::class);
        $mockMeService    = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Me\Service::class);
        $mockPostsService = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Me\Post\Service::class);
        $posts            = [];
        $response         = new Response(
            200,
            [],
            $posts
        );
        $token            = new Token();
        
        // expect
        $this->mockParser
            ->expects(self::once())
            ->method('parse')
            ->with(
                self::equalTo($jwt)
            )
            ->willReturn($token);
        $this->mockService
            ->expects(self::once())
            ->method('data')
            ->willReturn($mockDataService);
        $mockDataService
            ->expects(self::once())
            ->method('users')
            ->willReturn($mockUsersService);
        $mockUsersService
            ->expects(self::once())
            ->method('me')
            ->willReturn($mockMeService);
        $mockMeService
            ->expects(self::once())
            ->method('posts')
            ->willReturn($mockPostsService);
        $mockPostsService
            ->expects(self::once())
            ->method('all')
            ->with(
                self::equalTo($token)
            )
            ->willReturn($response);
        
        // act
        $output = $systemUnderTest->getPosts($jwt);
        
        // assert
        self::assertSame(
            $posts,
            $output
        );
    }
    
    /**
     * testGetPostsThrowsUnexpectedValueException
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function testGetPostsThrowsUnexpectedValueException() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt              = StalactiteServiceProvider::JWT;
        $mockDataService  = $this->createMock(\Jalismrs\Stalactite\Client\Data\Service::class);
        $mockUsersService = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Service::class);
        $mockMeService    = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Me\Service::class);
        $mockPostsService = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Me\Post\Service::class);
        $response         = new Response(
            200,
            [],
            'test'
        );
        $token            = new Token();
        
        // expect
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('not an array');
        $this->mockParser
            ->expects(self::once())
            ->method('parse')
            ->with(
                self::equalTo($jwt)
            )
            ->willReturn($token);
        $this->mockService
            ->expects(self::once())
            ->method('data')
            ->willReturn($mockDataService);
        $mockDataService
            ->expects(self::once())
            ->method('users')
            ->willReturn($mockUsersService);
        $mockUsersService
            ->expects(self::once())
            ->method('me')
            ->willReturn($mockMeService);
        $mockMeService
            ->expects(self::once())
            ->method('posts')
            ->willReturn($mockPostsService);
        $mockPostsService
            ->expects(self::once())
            ->method('all')
            ->with(
                self::equalTo($token)
            )
            ->willReturn($response);
        
        // act
        $systemUnderTest->getPosts($jwt);
    }
    
    /**
     * testGetPostsThrowsStalactiteException
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function testGetPostsThrowsStalactiteException() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt              = StalactiteServiceProvider::JWT;
        $mockDataService  = $this->createMock(\Jalismrs\Stalactite\Client\Data\Service::class);
        $mockUsersService = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Service::class);
        $mockMeService    = $this->createMock(\Jalismrs\Stalactite\Client\Data\User\Me\Service::class);
        $token            = new Token();
        
        // expect
        $this->expectException(StalactiteException::class);
        $this->expectExceptionMessage('Error while querying posts with Stalactite');
        $this->mockParser
            ->expects(self::once())
            ->method('parse')
            ->with(
                self::equalTo($jwt)
            )
            ->willReturn($token);
        $this->mockService
            ->expects(self::once())
            ->method('data')
            ->willReturn($mockDataService);
        $mockDataService
            ->expects(self::once())
            ->method('users')
            ->willReturn($mockUsersService);
        $mockUsersService
            ->expects(self::once())
            ->method('me')
            ->willReturn($mockMeService);
        $mockMeService
            ->expects(self::once())
            ->method('posts')
            ->willThrowException(
                new ClientException()
            );
        
        // act
        $systemUnderTest->getPosts($jwt);
    }
    
    /**
     * testValidate
     *
     * @return void
     *
     * @throws \Jalismrs\Stalactite\Client\Exception\ClientException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testValidate() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();
        
        $jwt                       = StalactiteServiceProvider::JWT;
        $mockAuthenticationService = $this->createMock(\Jalismrs\Stalactite\Client\Authentication\Service::class);
        $mockTokensService         = $this->createMock(\Jalismrs\Stalactite\Client\Authentication\Token\Service::class);
        $mockResponse              = $this->createMock(Response::class);
        $token                     = new Token();
        
        // expect
        $this->mockParser
            ->expects(self::once())
            ->method('parse')
            ->with(
                self::equalTo($jwt)
            )
            ->willReturn($token);
        $this->mockService
            ->expects(self::once())
            ->method('authentication')
            ->willReturn($mockAuthenticationService);
        $mockAuthenticationService
            ->expects(self::once())
            ->method('tokens')
            ->willReturn($mockTokensService);
        $mockTokensService
            ->expects(self::once())
            ->method('validate')
            ->with(
                self::equalTo($token)
            )
            ->willReturn($mockResponse);
        $mockResponse
            ->expects(self::once())
            ->method('isSuccessful')
            ->willReturn(false);
        
        // act
        $output = $systemUnderTest->validate($jwt);
        
        // assert
        self::assertFalse(
            $output
        );
        self::assertTrue(
            $this->testLogger->hasErrorRecords(),
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
        
        $this->mockClientApp = $this->createMock(ClientApp::class);
        $this->mockParser    = $this->createMock(Parser::class);
        $this->mockService   = $this->createMock(Service::class);
        $this->testLogger    = new TestLogger();
    }
}
