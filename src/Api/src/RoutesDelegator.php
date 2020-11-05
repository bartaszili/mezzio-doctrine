<?php

namespace Api;

use Api\Handler;
use Psr\Container\ContainerInterface;
use Mezzio\Application;

class RoutesDelegator
{
    public function __invoke(ContainerInterface $container, $serviceName, callable $callback)
    {
        /** @var $app Application */
        $app = $callback();

        // Create
        $app->post(
            '/api/properties/create[/]',
            Handler\PropertiesCreateHandler::class,
            'properties.create'
        );

        // Delete
        $app->post(
            '/api/pricelog/delete/{id:[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}}[/]',
            Handler\PricelogDeleteHandler::class,
            'pricelog.delete'
        );
        $app->post(
            '/api/properties/delete/{id:[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}}[/]',
            Handler\PropertiesDeleteHandler::class,
            'properties.delete'
        );

        // Search ~ uses collections
        $app->post(
            "/api/pricelog/search/[?page={page:\d+}]",
            Handler\PricelogSearchHandler::class,
            'pricelog.search'
        );
        $app->post(
            "/api/properties/search/[?page={page:\d+}]",
            Handler\PropertiesSearchHandler::class,
            'properties.search'
        );

        // Update
        $app->post(
            '/api/properties/update/{id:[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}}[/]',
            Handler\PropertiesUpdateHandler::class,
            'properties.update'
        );

        // View by ID
        $app->post(
            '/api/pricelog/view/{id:[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}}[/]',
            Handler\PricelogViewHandler::class,
            'pricelog.view'
        );
        $app->post(
            '/api/properties/view/{id:[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}}[/]',
            Handler\PropertiesViewHandler::class,
            'properties.view'
        );

        return $app;
    }
}
