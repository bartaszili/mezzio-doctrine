<?php

declare(strict_types=1);

namespace Api\Handler\Factory;

use Api\Handler\PropertiesDeleteHandler;
use Doctrine\ORM\EntityManager;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Psr\Container\ContainerInterface;

class PropertiesDeleteHandlerFactory
{
    public function __invoke(ContainerInterface $container) : PropertiesDeleteHandler
    {
        return new PropertiesDeleteHandler(
            $container->get(EntityManager::class),
            $container->get(HalResponseFactory::class),
            $container->get(ResourceGenerator::class),
            $container->get('config')['tokens']
        );
    }
}
