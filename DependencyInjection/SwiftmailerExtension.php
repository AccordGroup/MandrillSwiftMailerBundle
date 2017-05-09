<?php

namespace Accord\MandrillSwiftMailerBundle\DependencyInjection;

use Symfony\Bundle\SwiftmailerBundle\DependencyInjection\SwiftmailerExtension as BaseSwiftmailerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Swiftmailer bridge: declare Mandrill configuration through Swiftmailer configuration.
 *
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
class SwiftmailerExtension extends Extension
{
    /**
     * @var ExtensionInterface|BaseSwiftmailerExtension
     */
    protected $extension;

    /**
     * @param ExtensionInterface|BaseSwiftmailerExtension $extension
     */
    public function __construct($extension)
    {
        $this->extension = $extension;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->extension->getAlias();
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return $this->extension->getNamespace();
    }

    /**
     * @return bool|string
     */
    public function getXsdValidationBasePath()
    {
        return $this->extension->getXsdValidationBasePath();
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        foreach ($configs as $key => $config) {
            // Only 1 mailer configured
            if (!isset($config['mailers'])) {
                // It's not a Mandrill transport
                if (!isset($config['transport']) || 'accord_mandrill' !== $config['transport']) {
                    continue;
                }
                // Mandrill parameters are not set through Swiftmailer configuration
                if (!array_key_exists('api_key', $config)) {
                    continue;
                }
                // Set/erase Mandrill configuration
                $container->prependExtensionConfig('accord_mandrill_swift_mailer', [
                    'api_key'    => $config['api_key'],
                    'async'      => isset($config['async']) ? $config['async'] : false,
                    'subaccount' => isset($config['subaccount']) ? $config['subaccount'] : null,
                ]);
                unset($configs[$key]['api_key'], $configs[$key]['async'], $configs[$key]['subaccount']);
                continue;
            }
            // Multiple mailers configured
            foreach ($config['mailers'] as $name => $mailer) {
                // It's not a Mandrill transport
                if (!isset($mailer['transport']) || 'accord_mandrill' !== $mailer['transport']) {
                    continue;
                }
                // Mandrill parameters are not set through Swiftmailer configuration
                if (!array_key_exists('api_key', $mailer)) {
                    continue;
                }
                // Set/erase Mandrill configuration
                $container->prependExtensionConfig('accord_mandrill_swift_mailer', [
                    'api_key'    => $mailer['api_key'],
                    'async'      => isset($mailer['async']) ? $mailer['async'] : false,
                    'subaccount' => isset($mailer['subaccount']) ? $mailer['subaccount'] : null,
                ]);
                unset(
                    $configs[$key]['mailers'][$name]['api_key'],
                    $configs[$key]['mailers'][$name]['async'],
                    $configs[$key]['mailers'][$name]['subaccount']
                );
            }
        }
        $this->extension->load($configs, $container);
    }
}
