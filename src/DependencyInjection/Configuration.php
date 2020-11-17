<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Jalismrs\Symfony\Bundle\JalismrsAuthenticationBundle\DependencyInjection
 */
class Configuration implements
    ConfigurationInterface
{
    public const CONFIG_ROOT = 'jalismrs_authentication';
    
    public function getConfigTreeBuilder() : TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::CONFIG_ROOT);
        
        // @formatter:off
        $treeBuilder
            ->getRootNode()
            ->children()
                ->scalarNode('application')
                    ->info('Salactite application name')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('url')
                    ->info('Stalactite URL')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();
        // @formatter:on
        
        return $treeBuilder;
    }
}
