<?php

declare(strict_types=1);

namespace Api\Handler\Factory;

use Api\Handler\ServicesTestHandler;
use Psr\Container\ContainerInterface;

class ServicesTestHandlerFactory
{
    public function __invoke(ContainerInterface $container) : ServicesTestHandler
    {
        return new ServicesTestHandler();
    }
}
