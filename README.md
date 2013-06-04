zfauthsaml
==========

## Goal ##
The goal of this project is to provide a Zend\Authentication adapter that wraps simpleSAMLphp to provide SAML authentication.
At this point I'm not sure if simpleSAMLphp can be used strictly as a library or if it requires you to use its defined configuration files and such. Initially I'm working on the adapter to use an existing configured instance of simpleSAMLphp and then hope to refactor to wrap the library itself to be fully inclusive and support standard ZF application configuration strategies.

## Status ##
After much digging and emails on the simpleSAMLphp lists I do not believe it will be possible (at least not without a lot of hacking) to fully incapsulate simpleSAMLphp into this library.
Working in an environment where simpleSAMLphp is accessible via the web, this auth adapter will register routes for /login, /logout, /identity, and /return to handle redirections, displaying identity information returned by the identity provider and ensuring the user identity is persisted using Zend\Authentication\AuthenticationService.
Next I'm planning to get it working with BjyAuthorize for his automatic ACL support.

## Needs ##
If you have expertise with simpleSAMLphp I would love some help, connect with me through github.
