<?php

namespace Accord\MandrillSwiftMailerBundle\Tests;

use Accord\MandrillSwiftMailerBundle\DependencyInjection\AccordMandrillSwiftMailerExtension;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Bundle\SwiftmailerBundle\DependencyInjection\SwiftmailerExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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

    /**
     * @param string $cmsConfigResource
     * @return ContainerBuilder
     */
    protected function createContainerBuilder()
    {

        $this->clearTempDirs();

        $frameworkExtension = new FrameworkExtension();
        $mailerExtension = new SwiftmailerExtension();
        $bundleExtension = new AccordMandrillSwiftMailerExtension();

        $containerBuilder = new ContainerBuilder();

        $containerBuilder->setParameter('kernel.name', 'app');
        $containerBuilder->setParameter('kernel.environment', 'test');
        $containerBuilder->setParameter('kernel.debug', true);
        $containerBuilder->setParameter('kernel.root_dir', $this->rootDir);
        $containerBuilder->setParameter('kernel.cache_dir', $this->cacheDir);
        $containerBuilder->setParameter('kernel.log_dir', $this->logDir);
        $containerBuilder->setParameter('kernel.bundles', array());

        $kernel = $this->getMock('\Symfony\Component\HttpKernel\KernelInterface');
        $containerBuilder->set('kernel', $kernel);
        $kernel->expects($this->any())->method('getContainer')->willReturn($containerBuilder);
        $kernel->expects($this->any())->method('getRootDir')->willReturn($containerBuilder->getParameter('kernel.root_dir'));
        $kernel->expects($this->any())->method('getCacheDir')->willReturn($containerBuilder->getParameter('kernel.cache_dir'));
        $kernel->expects($this->any())->method('getLogDir')->willReturn($containerBuilder->getParameter('kernel.log_dir'));
        $kernel->expects($this->any())->method('getEnvironment')->willReturn($containerBuilder->getParameter('kernel.environment'));
        $kernel->expects($this->any())->method('getName')->willReturn($containerBuilder->getParameter('kernel.name'));

        $resources = array('framework.yml');

        $containerBuilder->registerExtension($frameworkExtension);
        $containerBuilder->registerExtension($mailerExtension);
        $containerBuilder->registerExtension($bundleExtension);

        $configLoader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/Resources/config'));
        foreach($resources as $resource){
            $configLoader->load($resource);
        }

        $containerBuilder->getExtension('framework')->load($containerBuilder->getExtensionConfig('framework'), $containerBuilder);
        $containerBuilder->getExtension('swiftmailer')->load($containerBuilder->getExtensionConfig('swiftmailer'), $containerBuilder);
        $containerBuilder->getExtension('accord_mandrill_swift_mailer')->load($containerBuilder->getExtensionConfig('accord_mandrill_swift_mailer'), $containerBuilder);

        return $containerBuilder;
    }

}