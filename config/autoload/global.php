<?php

declare(strict_types=1);

/**
 * Global configuration.
 *
 * For the sake of simplicity I've added all my custom configs here.
 */

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Mezzio\Router\FastRouteRouter;
use Mezzio\Router\RouterInterface;

return [
    'page_size' => 5,
    'tokens' => [
        ['hostname' => 'master', 'token' => 'token'],
    ],
    'dependencies' => [
        'invokables' => [
            RouterInterface::class => FastRouteRouter::class,
        ],
    ],
    'router' => [
        'fastroute' => [
            'cache_enabled' => false,
            'cache_file'    => 'data/cache/fastroute.php.cache',
        ],
    ],
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'params' => [
                    'driver' => 'pdo_mysql',
                    'host' => '127.0.0.1',
                    'port' => '3306',
                    'dbname' => 'mezzio-doctrine',
                    'user' => 'mezzio-doctrine',
                    'password' => 'mezzio-doctrine',
                    'charset'  => 'utf8mb4',
                    'collation'=> 'utf8mb4_unicode_ci',
                ],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class' => MappingDriverChain::class,
                'drivers' => [
                    'App\Entity' => 'app_entity',
                ],
            ],
            'app_entity' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../../src/App/src/Entity',
                ],
            ],
        ],
    ]
];
