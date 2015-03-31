<?php

namespace Accord\MandrillSwiftMailerBundle\Tests;

use Accord\MandrillSwiftMailerBundle\SwiftMailer\MandrillTransport;

class BundleTest extends BundleTestCase{

    public function testTransport(){

        $container = $this->createContainer();

        /** @var MandrillTransport $transport */
        $transport = $container->get('swiftmailer.mailer.transport.accord_mandrill');

        $this->assertNotNull($transport);
        $this->assertInstanceOf('\Accord\MandrillSwiftMailerBundle\SwiftMailer\MandrillTransport', $transport, 'Transport should be an instance of MandrillTransport');
        $this->assertEquals('AexOlO8l1E1JE_7jEXbSpQ', $transport->getApiKey(), 'Incorrect API key, should be using test key');

        /** @var \Swift_Mailer $mailer */
        $mailer = $container->get('mailer');

        $message = new \Swift_Message('TEST SUBJECT', 'test body');
        $message->setTo('to@example.com');
        $message->setFrom('from@example.com');

        $result = $mailer->send($message);

        $this->assertEquals(1, $result, 'One message should have been sent to Mandrill');

    }

}