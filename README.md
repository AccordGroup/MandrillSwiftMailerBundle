MandrillSwiftMailerBundle
=========================

A Symfony2 bundle that provides a Mandrill Transport implementation based on Mandrill's API

Currently this is only a Development build and should not be used for live projects.

Requirments
=========================

Mandrill API Key - https://mandrillapp.com/

Usage
=========================

Add your API key to the paramaters.yml

```
# app/parameters.yml
parameters:
    ...
    ...
    mandrill_api_key: 3Js91aH5NGK5KygCeuuboA
    
```

Set swiftmailer to use this new transport 

```
# app/config.php
swiftmailer:
    transport: accord_mandrill
    
```
