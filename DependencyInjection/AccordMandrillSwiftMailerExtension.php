<?php

namespace Accord\MandrillSwiftMailerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AccordMandrillSwiftMailerExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $transportDefinition = $container->getDefinition('swiftmailer.mailer.transport.accord_mandrill');
        $transportDefinition->addMethodCall('setApiKey', array( $config['api_key'] ));
        $transportDefinition->addMethodCall('setAsync', array( $config['async'] ));

        $container->setAlias('accord_mandrill', 'swiftmailer.mailer.transport.accord_mandrill');
    }
}
