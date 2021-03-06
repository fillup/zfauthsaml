zfauthsaml
==========

## Goal ##
The goal of this project is to provide a Zend\Authentication adapter that wraps simpleSAMLphp to provide SAML authentication.
At this point I'm not sure if simpleSAMLphp can be used strictly as a library or if it requires you to use its defined configuration files and such. Initially I'm working on the adapter to use an existing configured instance of simpleSAMLphp and then hope to refactor to wrap the library itself to be fully inclusive and support standard ZF application configuration strategies.

## Todo ##
- [x] Implement support for an existing simpleSAMLphp install and use APIs to check if user is authenticated and persist identity information if so.
- [x] Implement support for BjyAuthorize to grant/deny access based on groups returned by SAML
- [x] Move return url path to config file and enable dynamic return url based on originally requested url
- [x] Refactor user entity to actually be populated based on SAML data
- [x] Implement local account provisioning on successful first login
- [x] Find better way to manage role list/config to prevent error when SAML returns a group/role not already configured. Perhaps support pulling from a RESTful API?
- [ ] Further abstract user entity and mapper classes to support user defined entity models that can be persisted


## Needs ##
If you have expertise with simpleSAMLphp or writing extensions/adapters/customizations for ZfcUser I would love some help, connect with me through github.

## Setup ##
1) Update your composer to require these modules (if not already requiring them):

```json
"require": {
    "php": ">=5.3.3",
    "zendframework/zendframework": "~2.2",
    "zf-commons/zfc-user": "dev-master",
    "bjyoungblood/bjy-authorize": "~1.2",
    "fillup/zfauthsaml": "dev-master"
}
```

2) Copy ```vendor/zf-commons/zfc-user/config/zfcuser.global.php.dist``` to ```config/autoload/zfcuser.global.php```

3) Change two settings within zfcuser.global.php:

```php
$settings = array(
  'user_entity_class' => 'ZfAuthSaml\Entity\User',
  'auth_adapters' => array( 100 => 'ZfAuthSaml\Authentication\Adapter' ),
);
```

4) Copy ```vendor/bjyoungblood/bjy-authorize/config/module.config.php``` to ```config/autoload/module.bjyauthorize.global.php```

5) Change four settings in module.bjyauthorize.global.php:

```php
return array(
  'identity_provider'  => 'ZfAuthSaml\Provider\Identity\SamlIdentityProvider',
  'role_providers'        => array(
        // format: user_role(role_id(varchar), parent(varchar))
        'BjyAuthorize\Provider\Role\Config' => array(
            'guest' => array(),
            'user'  => array(),
            // List any groups that from SMAL that you want to identify with 
            // in your application. You could also load them from a database.
            // The SamlIdentityProvider will only return roles that are defined
            // here and are part of the user's identity from the IdP
        ),
  ),
  'guards'                => array(
    // Setup your rules for various controllers/actions, these are just some examples.
    'BjyAuthorize\Guard\Controller' => array(
        array('controller' => 'Application\Controller\Index', 'roles' => array('users')),
        array('controller' => 'zfauthsaml', 'roles' => array('users')),
        // Make sure you allow guests access to these two actions so they can actually login:
        array('controller' => 'zfauthsaml', 'action' => array('login','return'), 'roles' => array('guest')), 
        
    ),
  ),
  'unauthorized_strategy' => 'ZfAuthSaml\View\RedirectionStrategy',
);
```

6) Enable modules in ```config/application.config.php```:

```php
return array(
  'modules' => array(
    //...
    'ZfcBase',
    'ZfcUser',
    'BjyAuthorize',
    'ZfAuthSaml',
  );
);
```
7) Update your init_autoloader.php to autoload simpleSAMLphp. For my dev area this looks like:
```php
// simpleSAMLphp autoloading
if (file_exists('vendor/simplesamlphp/lib/_autoload.php')) {
    $loader = include_once 'vendor/simplesamlphp/lib/_autoload.php';
}
```

8) Apply schema changes to your user table. This assumes you created the initial user table defined with ZfcUser. Schema file located at ```data/schema.sql```

That should be it, users who are not logged in and do not have access to requested resources should be redirected to /login which will redirect them to the IdP you have configured to login. After login they will come back to simplesaml which will them redirect them to /return on your application which will load their identity into persistence and create a local user one does not already exist.
