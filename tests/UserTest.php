<?php
declare(strict_types = 1);

namespace Tests;

use Jalismrs\Stalactite\Client\Data\Model\User as StalactiteUser;
use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 *
 * @package Tests
 *
 * @covers  \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\User
 */
final class UserTest extends
    TestCase
{
    /**
     * testGetStalactiteUser
     *
     * @return void
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testGetStalactiteUser() : void
    {
        // arrange
        $stalactiteUser = new StalactiteUser();
        
        $systemUnderTest = $this->createSUT(
            $stalactiteUser,
        );
        
        // act
        $output = $systemUnderTest->getStalactiteUser();
        
        // assert
        self::assertSame(
            $stalactiteUser,
            $output
        );
    }
    
    /**
     * createSUT
     *
     * @param \Jalismrs\Stalactite\Client\Data\Model\User $stalactiteUser
     *
     * @return \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\User
     */
    private function createSUT(
        StalactiteUser $stalactiteUser
    ) : User {
        return new User(
            $stalactiteUser
        );
    }
}
