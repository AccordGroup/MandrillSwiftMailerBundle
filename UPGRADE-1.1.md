# Upgrade from <=1.0.3 to 1.1

## Multipart Support

1.1.0 introduces support for multipart emails. As a result, instances of \Swift_Mime_Message which are created without a specific content type will be treated as "text/plain".
  
If you want to send HTML emails, please ensure that you set the content type of your message to "text/html".