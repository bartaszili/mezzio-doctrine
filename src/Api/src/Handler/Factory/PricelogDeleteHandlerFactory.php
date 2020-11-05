<?php

declare(strict_types=1);

namespace Api\Handler\Factory;

use Api\Handler\PricelogDeleteHandler;
use Doctrine\ORM\EntityManager;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Psr\Container\ContainerInterface;

/**
 * Class PricelogDeleteHandlerFactory
 * @package Api\Handler
 */
class PricelogDeleteHandlerFactory
{
    /**
     * @param ContainerInterface $container
     * @return PricelogDeleteHandler
     */
    public function __invoke(ContainerInterface $container) : PricelogDeleteHandler
    {
        return new PricelogDeleteHandler(
            $container->get(EntityManager::class),
            $container->get(HalResponseFactory::class),
            $container->get(ResourceGenerator::class),
            $container->get('config')['tokens']
        );
    }
}
