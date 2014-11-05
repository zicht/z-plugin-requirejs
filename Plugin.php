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
                        ->scalarNode('config')->isRequired()->end()
                    ->end()
                ->end()
            ->end();
    }

    public function setContainer(Container $container)
    {
        $config = \Symfony\Component\Yaml\Yaml::parse($container->resolve('requirejs.config'));

        $container->method('requirejs.cmd', function($container, $root) use($config) {
            $out = rtrim($root, '/')
                . '/' . $config['web_root']
                . '/' . $config['target_dir']
                . '/' . $config['out']
            ;

            $baseUrl = rtrim($root, '/')
                . '/' . $config['web_root']
                . '/' . $config['src_dir']
                . '/' . $config['base_url']
            ;

            $mainConfigFile = rtrim($root, '/')
                . '/' . $config['web_root']
                . '/' . $config['src_dir']
                . '/' . $config['main_config_file']
            ;

            return sprintf(
                'r.js -o mainConfigFile=%s baseUrl=%s name=%s out=%s removeCombined=%s findNestedDependencies=%s',
                escapeshellcmd($mainConfigFile),
                escapeshellcmd($baseUrl),
                escapeshellcmd($config['name']),
                escapeshellcmd($out),
                ($config['remove_combined']) ? 'true' : 'false',
                ($config['find_nested_dependencies']) ? 'true' : 'false'
            );
        });
    }
}