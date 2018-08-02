<?php

namespace BrauneDigital\MailBundle\DependencyInjection;

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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('braune_digital_mail');

		$rootNode
			->children()
				->scalarNode('user_class')->isRequired()->end()
				->variableNode('base_template_path')->defaultValue('emails')->end()
				->arrayNode('entities')
					->useAttributeAsKey('entity')
					->prototype('array')
						->children()
							->scalarNode('entity')->end()
							->scalarNode('service')->end()
							->booleanNode('check_translation')->defaultFalse()->end()
							->arrayNode('events')
								->prototype('scalar')->end()
							->end()
						->end()
					->end()
				->end()
			->end()
		;

        $this->addMessageSection($rootNode);
        $this->addLayoutsSection($rootNode);

        return $treeBuilder;
    }

    public function addMessageSection($rootNode) {
        $rootNode
            ->children()
                ->arrayNode('message')
                    ->children()
                        ->scalarNode('use_request_locale')->defaultTrue()->end()
                        ->arrayNode('headers')
                            ->normalizeKeys(false)
                            ->prototype('scalar');
    }

	public function addLayoutsSection($rootNode) {
		$rootNode
			->children()
				->arrayNode('layouts')
				//TODO: remove legacy layout values
				->defaultValue(
					array(
						'password_reset' =>  'ApplicationAppBundle:Mail:Resetting/resetPassword.html.twig',
						'confirm' =>  'ApplicationAppBundle:Mail:Registration/confirm.html.twig'
					)
				)
				->useAttributeAsKey('layout')
				->prototype('scalar');
	}
}
