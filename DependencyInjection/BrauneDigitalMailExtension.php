<?php

namespace BrauneDigital\MailBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BrauneDigitalMailExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);
		$container->setParameter('braune_digital_mail', $config);

        $container->setParameter('braunedigital.mail.service.layouts', $config['layouts'] ? $config['layouts'] : array());

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @param ContainerBuilder $container
     * Add UserInterface to Doctrine ORM Configuration
     */
    public function prepend(ContainerBuilder $container) {
        // process the configuration of AcmeHelloExtension
        $configs = $container->getExtensionConfig($this->getAlias());

        $config = $this->processConfiguration(new Configuration(), $configs);

        if (isset($config['user_class'])) {

            $userClass = $config['user_class'];

            $doctrineConfig = call_user_func_array('array_replace_recursive', $container->getExtensionConfig('doctrine'));

            $base = array();

            if(array_key_exists('orm', $doctrineConfig)) {
                if(array_key_exists('resolve_target_entities', $doctrineConfig['orm']) && count($doctrineConfig['orm']['resolve_target_entities'])) {
                    $base = $doctrineConfig['orm']['resolve_target_entities'];
                }
            } else  {
                $doctrineConfig['orm'] = array();
            }

            $base['BrauneDigital\MailBundle\Model\UserInterface'] = $userClass;

            $doctrineConfig['orm']['resolve_target_entities'] = $base;

            $container->prependExtensionConfig('doctrine', $doctrineConfig);
        }
    }
}