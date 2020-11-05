<?php

declare(strict_types=1);

namespace Api\Handler;

use Api\Entity\Property;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PropertiesCreateHandler implements RequestHandlerInterface
{
    protected $entityManager;
    protected $halResponseFactory;
    protected $resourceGenerator;
    protected $validTokens;

    public function __construct(
        EntityManager $entityManager,
        HalResponseFactory $halResponseFactory,
        ResourceGenerator $resourceGenerator,
        $validTokens
    ) {
        $this->entityManager = $entityManager;
        $this->halResponseFactory = $halResponseFactory;
        $this->resourceGenerator = $resourceGenerator;
        $this->validTokens = $validTokens;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $requestBody = $request->getParsedBody()['Request']['Properties'];

        if (empty($requestBody))
        {
            $result['_error']['error'] = 'missing_request';
            $result['_error']['error_description'] = 'No request body sent.';

            return new JsonResponse($result, 400);
        }

        if (!isset($requestBody['token']) || empty($requestBody['token']) || (array_search($requestBody['token'], array_column($this->validTokens, 'token')) === false))
        {
            $result['_error']['error'] = 'forbidden';
            $result['_error']['error_description'] = 'Access validation error.';

            return new JsonResponse($result, 400);
        }

        if (!isset($requestBody['create']) || empty($requestBody['create']))
        {
            $result['_error']['error'] = 'missing_attribute';
            $result['_error']['error_description'] = 'No create body sent.';

            return new JsonResponse($result, 400);
        }

        $create = $requestBody['create'];

        $entity = new Property();

        try {
            $entity->setProperty($create);
            $entity->setCreated(new \DateTime("now"));
            $entity->setModified(new \DateTime("now"));

            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch(ORMException $e) {
            $result['_error']['error'] = 'not_created';
            $result['_error']['error_description'] = $e->getMessage();

            return new JsonResponse($result, 400);
        }

        $resource = $this->resourceGenerator->fromObject($entity, $request);

        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
