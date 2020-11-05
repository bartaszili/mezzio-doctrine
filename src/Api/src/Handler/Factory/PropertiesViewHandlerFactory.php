<?php

declare(strict_types=1);

namespace Api\Handler\Factory;

use Api\Handler\PropertiesViewHandler;
use Doctrine\ORM\EntityManager;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Psr\Container\ContainerInterface;

/**
 * Class PropertiesViewHandlerFactory
 * @package Api\Handler
 */
class PropertiesViewHandlerFactory
{
    /**
     * @param ContainerInterface $container
     * @return PropertiesViewHandler
     */
    public function __invoke(ContainerInterface $container) : PropertiesViewHandler
    {
        return new PropertiesViewHandler(
            $container->get(EntityManager::class),
            $container->get(HalResponseFactory::class),
            $container->get(ResourceGenerator::class),
            $container->get('config')['tokens']
        );
    }
}
