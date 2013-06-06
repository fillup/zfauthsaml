<?php

return array(
    'zfsamlauth' => array(
        'default-sp' => 'default-sp',
        'simpleSAMLphp_path' => __DIR__.'/../src/simpleSAMLphp',
    ),
    'controllers' => array(
        'invokables' => array(
            'zfauthsaml' => 'Fillup\ZfAuthSaml\Controller\ZfAuthSamlController',
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
            'Fillup\ZfAuthSaml\Adapter' => 'Fillup\ZfAuthSaml\Adapter',
            'Fillup\ZfAuthSaml\Provider\Identity\SamlIdentityProvider' => 'Fillup\ZfAuthSaml\Provider\Identity\SamlIdentityProvider',
            'Fillup\ZfAuthSaml\View\RedirectionStrategy' => 'Fillup\ZfAuthSaml\View\RedirectionStrategy',
        ),
    ),
);
