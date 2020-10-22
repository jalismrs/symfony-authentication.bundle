<?php
declare(strict_types = 1);

namespace Tests;

use Jalismrs\AuthenticationBundle\Authentication;
use Maba\GentleForce\Exception\RateLimitReachedException;
use Maba\GentleForce\RateLimitProvider;
use Maba\GentleForce\ThrottlerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

/**
 * Class AuthenticationTest
 *
 * @package Tests
 *
 * @covers  \Jalismrs\AuthenticationBundle\Authentication
 */
final class AuthenticationTest extends
    TestCase
{
    /**
     * mockRateLimitProvider
     *
     * @var \Maba\GentleForce\RateLimitProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private MockObject $mockRateLimitProvider;
    /**
     * mockThrottler
     *
     * @var \Maba\GentleForce\ThrottlerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private MockObject $mockThrottler;

    /**
     * testRegisterRateLimits
     *
     * @return void
     *
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    public function testRegisterRateLimits() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();

        $rateLimits = [];

        // expect
        $this->mockRateLimitProvider
            ->expects(self::once())
            ->method('registerRateLimits')
            ->with(
                self::equalTo(AuthenticationProvider::USE_CASE_KEY),
                self::equalTo($rateLimits)
            );

        // act
        $systemUnderTest->registerRateLimits(
            AuthenticationProvider::USE_CASE_KEY,
            $rateLimits
        );
    }

    /**
     * createSUT
     *
     * @return \Jalismrs\AuthenticationBundle\Authentication
     */
    private function createSUT() : Authentication
    {
        return new Authentication(
            $this->mockRateLimitProvider,
            $this->mockThrottler,
        );
    }

    /**
     * testWaitAndIncrease
     *
     * @return void
     *
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException
     */
    public function testWaitAndIncrease() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();

        // expect
        $this->mockThrottler
            ->expects(self::exactly(2))
            ->method('checkAndIncrease')
            ->with(
                self::equalTo(AuthenticationProvider::USE_CASE_KEY),
                self::equalTo(AuthenticationProvider::IDENTIFIER)
            )
            ->willReturnOnConsecutiveCalls(
                self::throwException(
                    new RateLimitReachedException(
                        42,
                        'Rate limit was reached'
                    )
                ),
                null
            );

        // act
        $systemUnderTest->waitAndIncrease(
            AuthenticationProvider::USE_CASE_KEY,
            AuthenticationProvider::IDENTIFIER
        );
    }

    /**
     * testWaitAndIncreaseThrowsRateLimitReachedException
     *
     * @return void
     *
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     * @throws \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException
     */
    public function testWaitAndIncreaseThrowsRateLimitReachedException() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();

        // expect
        $this->expectException(TooManyRequestsHttpException::class);
        $this->expectExceptionMessage('Loop limit was reached');
        $this->mockThrottler
            ->expects(self::once())
            ->method('checkAndIncrease')
            ->with(
                self::equalTo(AuthenticationProvider::USE_CASE_KEY),
                self::equalTo(AuthenticationProvider::IDENTIFIER)
            )
            ->willThrowException(
                new RateLimitReachedException(
                    42,
                    'Rate limit was reached'
                )
            );

        // act
        $systemUnderTest->setCap(1);
        $systemUnderTest->waitAndIncrease(
            AuthenticationProvider::USE_CASE_KEY,
            AuthenticationProvider::IDENTIFIER
        );
    }

    /**
     * testDecrease
     *
     * @return void
     *
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    public function testDecrease() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();

        // expect
        $this->mockThrottler
            ->expects(self::once())
            ->method('decrease')
            ->with(
                self::equalTo(AuthenticationProvider::USE_CASE_KEY),
                self::equalTo(AuthenticationProvider::IDENTIFIER)
            );

        // act
        $systemUnderTest->decrease(
            AuthenticationProvider::USE_CASE_KEY,
            AuthenticationProvider::IDENTIFIER
        );
    }

    /**
     * testReset
     *
     * @return void
     *
     * @throws \PHPUnit\Framework\MockObject\RuntimeException
     */
    public function testReset() : void
    {
        // arrange
        $systemUnderTest = $this->createSUT();

        // expect
        $this->mockThrottler
            ->expects(self::once())
            ->method('reset')
            ->with(
                self::equalTo(AuthenticationProvider::USE_CASE_KEY),
                self::equalTo(AuthenticationProvider::IDENTIFIER)
            );

        // act
        $systemUnderTest->reset(
            AuthenticationProvider::USE_CASE_KEY,
            AuthenticationProvider::IDENTIFIER
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

        $this->mockRateLimitProvider = $this->createMock(RateLimitProvider::class);
        $this->mockThrottler         = $this->createMock(ThrottlerInterface::class);
    }
}
