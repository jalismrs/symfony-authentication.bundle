<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\DependencyInjection;

use Jalismrs\Stalactite\Client\Authentication\Model\ClientApp;
use Jalismrs\Stalactite\Client\Client;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * Class JalismrsAuthenticationExtension
 *
 * @package Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\DependencyInjection
 */
class JalismrsAuthenticationExtension extends
    ConfigurableExtension
{
    /**
     * loadInternal
     *
     * @param array $mergedConfig
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function loadInternal(
        array $mergedConfig,
        ContainerBuilder $container
    ) : void {
        $fileLocator = new FileLocator(
            __DIR__ . '/../Resources/config'
        );
        
        $yamlFileLoader = new YamlFileLoader(
            $container,
            $fileLocator
        );
        
        $yamlFileLoader->load('services.yaml');
        
        $definition = $container->getDefinition(Client::class);
        $definition->replaceArgument(
            '$host',
            $mergedConfig['url']
        );
    
        $definition = $container->getDefinition(ClientApp::class);
        
        $definition->removeMethodCall('setName');
        $definition->addMethodCall(
            'setName',
            [
                '$name' => $mergedConfig['application'],
            ],
        );
    }
}
