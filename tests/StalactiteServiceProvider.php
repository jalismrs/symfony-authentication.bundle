<?php
declare(strict_types = 1);

namespace Tests;

use Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteService;
use Jalismrs\Stalactite\Client\ApiError;

/**
 * Class StalactiteServiceProvider
 *
 * @package Tests
 */
final class StalactiteServiceProvider
{
    public const JWT = 'jwt';
    
    /**
     * provideCheckResponseThrowsStalactiteExecption
     *
     * @return array
     */
    public function provideCheckResponseThrowsStalactiteExecption() : array
    {
        $message = 'message';
        
        return [
            'ApiError' => [
                'input' => new ApiError(
                    'type',
                    500,
                    $message
                ),
                'output' => $message,
            ],
            'string'  => [
                'input' => $message,
                'output' => $message,
            ],
            'other'  => [
                'input' => 42,
                'output' => StalactiteService::UNKNOWN_ERROR,
            ],
        ];
    }
    
}
