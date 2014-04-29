# MandrillSwiftMailerBundle

A Symfony2 bundle that provides a Mandrill Transport implementation based on Mandrill's API

Currently this is only a Development build and should not be used for live projects.

## Requirments

Mandrill API Key - https://mandrillapp.com/

## Installation

### Add bundle to composer.json

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/AccordGroup/MandrillSwiftMailerBundle.git"
        }
    ],
    "require": {
        "php": ">=5.3.2",
        "symfony/symfony": "~2.1",
        "_comment": "your other packages",
    
        "accord/mandrill-swiftmailer-bundle": "dev-master",
    }

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

### Configure Swiftmailer to use this new transport 

    // app/config.php
    swiftmailer:
        transport: accord_mandrill
