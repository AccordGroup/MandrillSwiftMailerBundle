<?php

namespace Accord\MandrillSwiftMailerBundle;

use Accord\MandrillSwiftMailerBundle\DependencyInjection\SwiftmailerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AccordMandrillSwiftMailerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        if ($container->hasExtension('swiftmailer')) {
            $container->registerExtension(new SwiftmailerExtension($container->getExtension('swiftmailer')));
        }
    }
}
