<?php

declare(strict_types=1);

namespace Api\Handler\Factory;

use Api\Handler\PropertiesSearchHandler;
use Doctrine\ORM\EntityManager;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Psr\Container\ContainerInterface;

class PropertiesSearchHandlerFactory
{
    public function __invoke(ContainerInterface $container) : PropertiesSearchHandler
    {
        return new PropertiesSearchHandler(
            $container->get(EntityManager::class),
            $container->get(HalResponseFactory::class),
            $container->get(ResourceGenerator::class),
            $container->get('config')['tokens'],
            $container->get('config')['page_size']
        );
    }
}
