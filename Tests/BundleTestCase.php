<?php

namespace Accord\MandrillSwiftMailerBundle\Tests;

use Accord\MandrillSwiftMailerBundle\DependencyInjection\AccordMandrillSwiftMailerExtension;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Bundle\SwiftmailerBundle\DependencyInjection\SwiftmailerExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Filesystem\Filesystem;

class BundleTestCase extends \PHPUnit_Framework_TestCase{

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var string
     */
    protected $logDir;

    /**
     * @var string
     */
    protected $rootDir;

    protected function setUp()
    {
        $this->rootDir = __DIR__;
        $this->cacheDir = sprintf('%s/cache', __DIR__);
        $this->logDir = sprintf('%s/log', __DIR__);

        $this->clearTempDirs();

        $fs = new Filesystem();
        $fs->mkdir($this->cacheDir);
        $fs->mkdir($this->logDir);
    }

    protected function tearDown()
    {
        $this->clearTempDirs();
    }

    protected function clearTempDirs()
    {
        $fs = new Filesystem();
        if($fs->exists($this->cacheDir)) $fs->remove($this->cacheDir);
        if($fs->exists($this->logDir)) $fs->remove($this->logDir);
    }

    protected function createContainer()
    {

        $this->clearTempDirs();

        $containerBuilder = new ContainerBuilder();

        $containerBuilder->setParameter('kernel.name', 'app');
        $containerBuilder->setParameter('kernel.environment', 'test');
        $containerBuilder->setParameter('kernel.debug', true);
        $containerBuilder->setParameter('kernel.root_dir', $this->rootDir);
        $containerBuilder->setParameter('kernel.cache_dir', $this->cacheDir);
        $containerBuilder->setParameter('kernel.log_dir', $this->logDir);
        $containerBuilder->setParameter('kernel.bundles', array());
        $containerBuilder->setParameter('kernel.charset', 'UTF8');
        $containerBuilder->setParameter('kernel.secret', 'ABCD1234');
        $containerBuilder->setParameter('kernel.container_class', get_class($containerBuilder));

        $containerBuilder->setParameter('mandrill_test_api_key', getenv('MANDRILL_TEST_API_KEY'));

        $kernel = $this->getMock('\Symfony\Component\HttpKernel\KernelInterface');
        $containerBuilder->set('kernel', $kernel);
        $kernel->expects($this->any())->method('getContainer')->willReturn($containerBuilder);
        $kernel->expects($this->any())->method('getRootDir')->willReturn($containerBuilder->getParameter('kernel.root_dir'));
        $kernel->expects($this->any())->method('getCacheDir')->willReturn($containerBuilder->getParameter('kernel.cache_dir'));
        $kernel->expects($this->any())->method('getLogDir')->willReturn($containerBuilder->getParameter('kernel.log_dir'));
        $kernel->expects($this->any())->method('getEnvironment')->willReturn($containerBuilder->getParameter('kernel.environment'));
        $kernel->expects($this->any())->method('getName')->willReturn($containerBuilder->getParameter('kernel.name'));

        $this->addFrameworkExtension($containerBuilder);
        $this->addSwiftmailerExtension($containerBuilder);
        $this->addBundleExtension($containerBuilder);

        $this->loadFrameworkExtension($containerBuilder);
        $this->loadSwiftmailerExtension($containerBuilder);
        $this->loadBundleExtension($containerBuilder);

        $containerBuilder->compile();

        return $containerBuilder;
    }

    /**
     * @param ContainerBuilder $containerBuilder
     */
    protected function addBundleExtension(ContainerBuilder $containerBuilder)
    {
        $extension = new AccordMandrillSwiftMailerExtension();
        $containerBuilder->registerExtension($extension);
    }

    /**
     * @param ContainerBuilder $containerBuilder
     */
    protected function addFrameworkExtension(ContainerBuilder $containerBuilder)
    {
        $extension = new FrameworkExtension();
        $containerBuilder->registerExtension($extension);

    }

    /**
     * @param ContainerBuilder $containerBuilder
     */
    protected function addSwiftmailerExtension(ContainerBuilder $containerBuilder)
    {
        $extension = new SwiftmailerExtension();
        $containerBuilder->registerExtension($extension);

    }

    /**
     * @param ContainerBuilder $containerBuilder
     */
    protected function loadFrameworkExtension(ContainerBuilder $containerBuilder)
    {
        $configLoader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/Resources/config'));
        $configLoader->load('framework.yml');
        $containerBuilder->getExtension('framework')->load($containerBuilder->getExtensionConfig('framework'), $containerBuilder);
    }

    /**
     * @param ContainerBuilder $containerBuilder
     */
    protected function loadSwiftmailerExtension(ContainerBuilder $containerBuilder)
    {
        $configLoader = new XmlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../vendor/symfony/swiftmailer-bundle/Resources/config'));
        $configLoader->load('swiftmailer.xml');
        $containerBuilder->getExtension('swiftmailer')->load($containerBuilder->getExtensionConfig('swiftmailer'), $containerBuilder);
    }

    /**
     * @param ContainerBuilder $containerBuilder
     */
    protected function loadBundleExtension(ContainerBuilder $containerBuilder)
    {
        $configLoader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/Resources/config'));
        $configLoader->load('bundle.yml');
        $containerBuilder->getExtension('accord_mandrill_swift_mailer')->load($containerBuilder->getExtensionConfig('accord_mandrill_swift_mailer'), $containerBuilder);
    }

}