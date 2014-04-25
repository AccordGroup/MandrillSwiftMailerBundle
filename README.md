MandrillSwiftMailerBundle
=========================

A Symfony2 bundle that provides a Mandrill Transport implementation based on Mandrill's API

Currently this is only a Development build and should not be used for live projects.

Requirments
=========================

Mandrill API Key - https://mandrillapp.com/

Usage
=========================

Add your API key to the config.yml

```
# app/config/config.yml
accord_mandrill_swift_mailer:
    api_key: MANDRILL_API_KEY
    
```

Set swiftmailer to use this new transport 

```
# app/config.php
swiftmailer:
    transport: accord_mandrill
    
```
