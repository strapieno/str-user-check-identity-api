<?php

return [
    'service_manager' => [
        'factories' => [
            'Strapieno\Utils\Listener\ListenerManager' => 'Strapieno\Utils\Listener\ListenerManagerFactory'
        ],
        'invokables' => [
            'Strapieno\Utils\Delegator\AttachListenerDelegator' =>  'Strapieno\Utils\Delegator\AttachListenerDelegator'
        ],
        'aliases' => [
            'listenerManager' => 'Strapieno\Utils\Listener\ListenerManager'
        ]
    ],
    // Register listener to listener manager
    'service-listeners' => [
        'invokables' => [
            'Strapieno\UserCheckIdentity\Api\V1\Listener\UserListener'
                => 'Strapieno\UserCheckIdentity\Api\V1\Listener\UserListener'
        ]
    ],
    // Register listener to User rest controller
    'attach-listeners' => [
        'Strapieno\User\Api\V1\Rest\Controller' => [
            'Strapieno\UserCheckIdentity\Api\V1\Listener\UserListener'
        ]
    ],
    'router' => [
        'routes' => [
            'api-rpc' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/rpc'
                ],
                'child_routes' => [
                    'validate-identity' => [
                        'type' => 'Segment',
                        'may_terminate' => true,
                        'options' => [
                            'route' => '/validate-identity',
                            'defaults' => [
                                'controller' => 'Strapieno\UserCheckIdentity\Api\V1\RpcController',
                                'action' => 'validateIdentity'
                            ],
                        ]
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        // Attach delegetor listener to user rest controller
        'delegators' => [
            'Strapieno\User\Api\V1\Rest\Controller' => [
                'Strapieno\Utils\Delegator\AttachListenerDelegator'
            ]
        ],
        'invokables' => [
            'Strapieno\UserCheckIdentity\Api\V1\RpcController' => 'Strapieno\UserCheckIdentity\Api\V1\RpcController',
        ]
    ],
    'zf-rpc' => [
        'Strapieno\UserCheckIdentity\Api\V1\RpcController' => [
            'service_name' => 'validate-identity',
            'http_methods' => ['POST'],
            'route_name' => 'api-rpc/validate-identity',
        ],
    ],
     'zf-content-negotiation' => [
        'accept_whitelist' => [
            'Strapieno\UserCheckIdentity\Api\V1\RpcController' => [
                'application/hal+json',
                'application/json',
            ]

        ],
        'content_type_whitelist' => [
            'Strapieno\UserCheckIdentity\Api\V1\RpcController' => [
                'application/json',
            ],
        ]
    ],
    'zf-content-validation' => [
        'Strapieno\UserCheckIdentity\Api\V1\RpcController' => [
            'input_filter' => 'Strapieno\UserCheckIdentity\Api\V1\InputFiler\GenerateIdentityInputFilter'
        ]
    ]
];

