<?php

declare(strict_types=1);

namespace Api;

use Api\Entity\Property;
use Api\Entity\Collection\PropertyCollection;
use Api\Entity\Pricelog;
use Api\Entity\Collection\PricelogCollection;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Laminas\Hydrator\ReflectionHydrator;
use Mezzio\Application;
use Mezzio\Hal\Metadata\MetadataMap;
use Mezzio\Hal\Metadata\RouteBasedCollectionMetadata;
use Mezzio\Hal\Metadata\RouteBasedResourceMetadata;

/**
 * The configuration provider for the Properties module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
            'doctrine'     => $this->getDoctrineEntities(),
            MetadataMap::class => $this->getHalMetadataMap(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'delegators' => [
                Application::class => [
                    RoutesDelegator::class
                ]
            ],
            'invokables' => [
            ],
            'factories'  => [
                Handler\PricelogDeleteHandler::class => Handler\Factory\PricelogDeleteHandlerFactory::class,
                Handler\PricelogSearchHandler::class => Handler\Factory\PricelogSearchHandlerFactory::class,
                Handler\PricelogViewHandler::class => Handler\Factory\PricelogViewHandlerFactory::class,
                Handler\PropertiesCreateHandler::class => Handler\Factory\PropertiesCreateHandlerFactory::class,
                Handler\PropertiesDeleteHandler::class => Handler\Factory\PropertiesDeleteHandlerFactory::class,
                Handler\PropertiesSearchHandler::class => Handler\Factory\PropertiesSearchHandlerFactory::class,
                Handler\PropertiesUpdateHandler::class => Handler\Factory\PropertiesUpdateHandlerFactory::class,
                Handler\PropertiesViewHandler::class => Handler\Factory\PropertiesViewHandlerFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates() : array
    {
        return [
            'paths' => [
                'api'    => [__DIR__ . '/../templates/'],
            ],
        ];
    }

    /**
     * Returns Doctrine configuration
     */
    public function getDoctrineEntities() : array
    {
        return [
            'driver' => [
                'orm_default' => [
                    'class' => MappingDriverChain::class,
                    'drivers' => [
                        'Api\Entity' => 'api_entity',
                    ],
                ],
                'api_entity' => [
                    'class' => AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => [__DIR__ . '/Entity'],
                ],
            ],
        ];
    }

    /**
     * Returns HAL MetadataMap configuration
     */
    public function getHalMetadataMap()
    {
        return [
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => Pricelog::class,
                'route' => 'pricelog.view',
                'extractor' => ReflectionHydrator::class,
            ],
            [
                '__class__' => RouteBasedCollectionMetadata::class,
                'collection_class' => PricelogCollection::class,
                'collection_relation' => 'pricelog',
                'route' => 'pricelog.search',
            ],
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => Property::class,
                'route' => 'properties.view',
                'extractor' => ReflectionHydrator::class,
            ],
            [
                '__class__' => RouteBasedCollectionMetadata::class,
                'collection_class' => PropertyCollection::class,
                'collection_relation' => 'property',
                'route' => 'properties.search',
            ],
        ];
    }
}
