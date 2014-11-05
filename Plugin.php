<?php
/**
 * @author Joppe Aarts <joppe@zicht.nl>
 * @copyright Zicht Online <http://zicht.nl>
 */

namespace Zicht\Tool\Plugin\Requirejs;

use \Zicht\Tool\Plugin as BasePlugin;
use \Zicht\Tool\Container\Container;
use \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class Plugin
 *
 * @package Zicht\Tool\Plugin\Requirejs
 */
class Plugin extends BasePlugin
{
    protected $config = null;

    public function appendConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('requirejs')
                    ->children()
                        ->scalarNode('web_root')->defaultValue('web')->end()
                        ->scalarNode('target_dir')->defaultValue('js')->end()
                        ->scalarNode('src_dir')->isRequired()->end()
                        ->scalarNode('main_config_ile')->defaultValue('javascript/config.js')->end()
                        ->scalarNode('base_url')->defaultValue('javascript')->end()
                        ->scalarNode('name')->defaultValue('name')->end()
                        ->scalarNode('out')->defaultValue('site.min.js')->end()
                        ->scalarNode('remove_combined')->defaultValue(true)->end()
                        ->scalarNode('find_nested_dependencies')->defaultValue('true')->end()
                    ->end()
                ->end()
            ->end();
    }

    public function setContainer(Container $container)
    {
        $container->method('requirejs.cmd', function($container, $root) {
            $out = rtrim($root, '/')
                . '/' . $container->resolve('requirejs.web_root')
                . '/' . $container->resolve('requirejs.target_dir')
                . '/' . $container->resolve('requirejs.out')
            ;

            $baseUrl = rtrim($root, '/')
                . '/' . $container->resolve('requirejs.web_root')
                . '/' . $container->resolve('requirejs.src_dir')
                . '/' . $container->resolve('requirejs.base_url')
            ;

            $mainConfigFile = rtrim($root, '/')
                . '/' . $container->resolve('requirejs.web_root')
                . '/' . $container->resolve('requirejs.src_dir')
                . '/' . $container->resolve('requirejs.main_config_ile')
            ;

            return sprintf(
                'r.js -o mainConfigFile=%s baseUrl=%s name=%s out=%s removeCombined=%s findNestedDependencies=%s',
                escapeshellcmd($mainConfigFile),
                escapeshellcmd($baseUrl),
                escapeshellcmd($container->resolve('requirejs.name')),
                escapeshellcmd($out),
                ($container->resolve('requirejs.remove_combined')) ? 'true' : 'false',
                ($container->resolve('requirejs.find_nested_dependencies')) ? 'true' : 'false'
            );
        });
    }
}