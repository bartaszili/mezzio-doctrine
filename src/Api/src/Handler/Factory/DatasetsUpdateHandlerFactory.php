<?php

declare(strict_types=1);

namespace Api\Handler\Factory;

use Api\Handler\DatasetsUpdateHandler;
use Psr\Container\ContainerInterface;

class DatasetsUpdateHandlerFactory
{
    public function __invoke(ContainerInterface $container) : DatasetsUpdateHandler
    {
        return new DatasetsUpdateHandler(
            $container->get('config')['tokens']
        );
    }
}
