<?php

declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle;

use Jalismrs\Stalactite\Client\ApiError;
use Jalismrs\Stalactite\Client\Authentication\Model\ClientApp;
use Jalismrs\Stalactite\Client\Data\Model\User;
use Jalismrs\Stalactite\Client\Exception\ClientException;
use Jalismrs\Stalactite\Client\Service;
use Jalismrs\Stalactite\Client\Util\Response;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use Psr\Log\LoggerInterface;
use function is_array;
use function is_string;

/**
 * Class StalactiteService
 *
 * @package App\Auth
 */
class StalactiteService
{
    public const UNKNOWN_ERROR = 'unknown_error';
    
    /**
     * logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * parser
     *
     * @var \Lcobucci\JWT\Parser
     */
    private Parser $parser;
    /**
     * service
     *
     * @var \Jalismrs\Stalactite\Client\Service
     */
    private Service $service;
    /**
     * clientApp
     *
     * @var \Jalismrs\Stalactite\Client\Authentication\Model\ClientApp
     */
    private ClientApp $clientApp;
    
    /**
     * StalactiteService constructor.
     *
     * @param \Jalismrs\Stalactite\Client\Authentication\Model\ClientApp $clientApp
     * @param \Psr\Log\LoggerInterface                                   $authLogger
     * @param \Lcobucci\JWT\Parser                                       $parser
     * @param \Jalismrs\Stalactite\Client\Service                        $service
     */
    public function __construct(
        ClientApp $clientApp,
        LoggerInterface $authLogger,
        Parser $parser,
        Service $service
    ) {
        $this->logger    = $authLogger;
        $this->parser    = $parser;
        $this->service   = $service;
        $this->clientApp = $clientApp;
    }
    
    /**
     * login
     *
     * @param string $externalUserJwt
     *
     * @return string
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function login(
        string $externalUserJwt
    ) : string {
        try {
            $response = $this->service
                ->authentication()
                ->tokens()
                ->login(
                    $this->clientApp,
                    $externalUserJwt
                );
        } catch (ClientException $clientException) {
            $this->logger->critical($clientException);
            
            throw new StalactiteException(
                'Error while logging with Stalactite',
                $clientException->getCode(),
                $clientException
            );
        }
        
        $this->checkResponse($response);
        
        return (string)$response->getBody()['token'];
    }
    
    /**
     * checkResponse
     *
     * @param \Jalismrs\Stalactite\Client\Util\Response $response
     *
     * @return void
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     */
    private function checkResponse(
        Response $response
    ) : void {
        if (!$response->isSuccessful()) {
            $body = $response->getBody();
            
            if ($body instanceof ApiError) {
                $message = $body->getMessage();
            } elseif (is_string($body)) {
                $message = $body;
            } else {
                $message = self::UNKNOWN_ERROR;
            }
            $this->logger->critical($message);
            
            throw new StalactiteException(
                $message,
                $response->getCode()
            );
        }
    }
    
    /**
     * getUser
     *
     * @param string $jwt
     *
     * @return \Jalismrs\Stalactite\Client\Data\Model\User
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function getUser(
        string $jwt
    ) : User {
        try {
            $token = $this->parseJwt($jwt);
            
            $response = $this->service
                ->data()
                ->users()
                ->me()
                ->get($token);
        } catch (ClientException $clientException) {
            $this->logger->critical($clientException);
            
            throw new StalactiteException(
                'Error while querying user with Stalactite',
                $clientException->getCode(),
                $clientException
            );
        }
        
        $this->checkResponse($response);
        
        $user = $response->getBody();
        if (!$user instanceof User) {
            throw new \UnexpectedValueException(
                'not an User'
            );
        }
        
        return $user;
    }
    
    /**
     * parseJwt
     *
     * @param string $jwt
     *
     * @return \Lcobucci\JWT\Token
     */
    private function parseJwt(
        string $jwt
    ) : Token {
        return $this->parser->parse($jwt);
    }
    
    /**
     * getLeads
     *
     * @param string $jwt
     *
     * @return array
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function getLeads(
        string $jwt
    ) : array {
        try {
            $token = $this->parseJwt($jwt);
            
            $response = $this->service
                ->data()
                ->users()
                ->me()
                ->leads()
                ->all($token);
        } catch (ClientException $clientException) {
            $this->logger->critical($clientException);
            
            throw new StalactiteException(
                'Error while querying leads with Stalactite',
                $clientException->getCode(),
                $clientException
            );
        }
        
        $this->checkResponse($response);
        
        $leads = $response->getBody();
        if (!is_array($leads)) {
            throw new \UnexpectedValueException(
                'not an array'
            );
        }
        
        return $leads;
    }
    
    /**
     * getPosts
     *
     * @param string $jwt
     *
     * @return array
     *
     * @throws \Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\StalactiteException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function getPosts(
        string $jwt
    ) : array {
        try {
            $token = $this->parseJwt($jwt);
            
            $response = $this->service
                ->data()
                ->users()
                ->me()
                ->posts()
                ->all($token);
        } catch (ClientException $clientException) {
            $this->logger->critical($clientException);
            
            throw new StalactiteException(
                'Error while querying posts with Stalactite',
                $clientException->getCode(),
                $clientException
            );
        }
        
        $this->checkResponse($response);
        
        $posts = $response->getBody();
        if (!is_array($posts)) {
            throw new \UnexpectedValueException(
                'not an array'
            );
        }
        
        return $posts;
    }
    
    /**
     * validate
     *
     * @param string $jwt
     *
     * @return bool
     *
     * @throws \Jalismrs\Stalactite\Client\Exception\ClientException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function validate(
        string $jwt
    ) : bool {
        $token = $this->parseJwt($jwt);
        
        return $this
            ->service
            ->authentication()
            ->tokens()
            ->validate($token)
            ->isSuccessful();
    }
}
