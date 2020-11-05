<?php

declare(strict_types=1);

namespace Api\Handler\Factory;

use Api\Handler\PricelogViewHandler;
use Doctrine\ORM\EntityManager;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Psr\Container\ContainerInterface;

/**
 * Class PricelogViewHandlerFactory
 * @package Api\Handler
 */
class PricelogViewHandlerFactory
{
    /**
     * @param ContainerInterface $container
     * @return PricelogViewHandler
     */
    public function __invoke(ContainerInterface $container) : PricelogViewHandler
    {
        return new PricelogViewHandler(
            $container->get(EntityManager::class),
            $container->get(HalResponseFactory::class),
            $container->get(ResourceGenerator::class),
            $container->get('config')['tokens']
        );
    }
}
