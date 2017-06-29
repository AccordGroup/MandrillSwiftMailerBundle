# MandrillSwiftMailerBundle

[![Build Status](https://travis-ci.org/AccordGroup/MandrillSwiftMailerBundle.svg?branch=master)](https://travis-ci.org/AccordGroup/MandrillSwiftMailerBundle) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/21a5761d-ba5e-46f2-8939-a561e12698a8/mini.png)](https://insight.sensiolabs.com/projects/21a5761d-ba5e-46f2-8939-a561e12698a8)

A Symfony bundle that provides a Mandrill Transport implementation based on Mandrill's API

## Requirments

Mandrill API Key - https://mandrillapp.com/

## Installation

### Require the package with composer

    composer require accord/mandrill-swiftmailer-bundle

### Add AccordMandrillSwiftMailerBundle to application kernel

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Accord\MandrillSwiftMailerBundle\AccordMandrillSwiftMailerBundle(),
            // ...
        );
    }

### Add your API key to the config.yml

    // app/config/config.yml
    accord_mandrill_swift_mailer:
        api_key: MANDRILL_API_KEY
        async: false # optional
        subaccount: ~ # default null

### Configure Swiftmailer to use this new transport

    // app/config/config.yml
    swiftmailer:
        transport: accord_mandrill
