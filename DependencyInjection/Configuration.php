<?php

namespace PhpInk\Nami\ContactFormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nami_contact_form');
        $rootNode
            ->children()
                ->scalarNode('mail_to')->defaultValue('email@provider.com')->end()
                ->scalarNode('mail_template')->defaultValue('@NamiContactFormBundle/Resources/views/mail.html.twig')->end()
                ->scalarNode('block_template')->defaultValue('@NamiContactFormBundle/Resources/views/block.html.twig')->end()
            ->end();

        return $treeBuilder;
    }
}
