<?php

declare(strict_types=1);

namespace Api\Handler\Factory;

use Api\Handler\PricelogSearchHandler;
use Doctrine\ORM\EntityManager;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Psr\Container\ContainerInterface;

/**
 * Class PricelogSearchHandlerFactory
 * @package Api\Handler
 */
class PricelogSearchHandlerFactory
{
    /**
     * @param ContainerInterface $container
     * @return PricelogSearchHandler
     */
    public function __invoke(ContainerInterface $container) : PricelogSearchHandler
    {
        return new PricelogSearchHandler(
            $container->get(EntityManager::class),
            $container->get(HalResponseFactory::class),
            $container->get(ResourceGenerator::class),
            $container->get('config')['tokens'],
            $container->get('config')['page_size']
        );
    }
}
