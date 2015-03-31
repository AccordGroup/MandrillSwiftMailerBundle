<?php

namespace Accord\MandrillSwiftMailerBundle\Tests;

class BundleTest extends BundleTestCase{

    public function testTransport(){

        $container = $this->createContainerBuilder();

        $mailer = $container->get('mailer');

        echo get_class($mailer);

    }

}