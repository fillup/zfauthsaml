<?php

return array(
    'zfsamlauth' => array(
        'default-sp' => 'default-sp',
        'simpleSAMLphp_path' => __DIR__.'/../src/simpleSAMLphp',
    ),
    'controllers' => array(
        'invokables' => array(
            'zfauthsaml' => 'ZfAuthSaml\Controller\ZfAuthSamlController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'login' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/login',
                    'defaults' => array(
                        'controller' => 'zfauthsaml',
                        'action'     => 'login',
                    ),
                ),
                'may_terminate' => true,
            ),
            'identity' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/identity',
                    'defaults' => array(
                        'controller' => 'zfauthsaml',
                        'action'     => 'identity',
                    ),
                ),
                'may_terminate' => true,
            ),
            'logout' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                        'controller' => 'zfauthsaml',
                        'action'     => 'logout',
                    ),
                ),
                'may_terminate' => true,
            ),
            'return' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/return',
                    'defaults' => array(
                        'controller' => 'zfauthsaml',
                        'action'     => 'return',
                    ),
                ),
                'may_terminate' => true,
            ),
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'ZfAuthSaml\Adapter' => 'ZfAuthSaml\Adapter',
            'ZfAuthSaml\Provider\Identity\SamlIdentityProvider' => 'ZfAuthSaml\Provider\Identity\SamlIdentityProvider',
            'ZfAuthSaml\View\RedirectionStrategy' => 'ZfAuthSaml\View\RedirectionStrategy',
        ),
    ),
);
