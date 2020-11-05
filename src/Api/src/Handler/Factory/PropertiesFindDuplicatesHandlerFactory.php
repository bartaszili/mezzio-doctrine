<?php

declare(strict_types=1);

namespace Api\Handler\Factory;

use Api\Handler\PropertiesFindDuplicatesHandler;
use Doctrine\ORM\EntityManager;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Psr\Container\ContainerInterface;

class PropertiesFindDuplicatesHandlerFactory
{
    public function __invoke(ContainerInterface $container) : PropertiesFindDuplicatesHandler
    {
        return new PropertiesFindDuplicatesHandler(
            $container->get(EntityManager::class),
            $container->get(HalResponseFactory::class),
            $container->get(ResourceGenerator::class),
            $container->get('config')['tokens'],
            $container->get('config')['page_size']
        );
    }
}
