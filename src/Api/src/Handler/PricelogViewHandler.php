<?php

declare(strict_types=1);

namespace Api\Handler;

use Api\Entity\Pricelog;
use Doctrine\ORM\EntityManager;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class PricelogViewHandler
 * @package Api\Handler
 */
class PricelogViewHandler implements RequestHandlerInterface
{
    protected $entityManager;
    protected $halResponseFactory;
    protected $resourceGenerator;
    protected $validTokens;

    /**
     * PricelogViewHandler constructor.
     * @param EntityManager $entityManager
     * @param HalResponseFactory $halResponseFactory
     * @param ResourceGenerator $resourceGenerator
     * @param array $validTokens;
     */
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

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        // Error handling: 400 Bad Request
        if (
            empty($request) ||
            empty($request->getParsedBody()) ||
            !isset($request->getParsedBody()['Request']) ||
            !isset($request->getParsedBody()['Request']['Pricelog'])
        ) {
            $result['_error']['error'] = 'bad_request';
            $result['_error']['error_description'] = 'Wrongly formated or missing request body.';

            return new JsonResponse($result, 400);
        }

        $requestBody = $request->getParsedBody()['Request']['Pricelog'];

        // Error handling: 400 Forbidden
        if (!isset($requestBody['token']) || empty($requestBody['token']) || (array_search($requestBody['token'], array_column($this->validTokens, 'token')) === false))
        {
            $result['_error']['error'] = 'forbidden';
            $result['_error']['error_description'] = 'Access validation error.';

            return new JsonResponse($result, 400);
        }

        // Include or not related property in response
        $recursive = false;
        if (isset($requestBody['recursive']) && !empty($requestBody['recursive']))
        {
            $recursive = true;
        }

        $repository = $this->entityManager->getRepository(Pricelog::class);
        $id = $request->getAttribute('id', null);
        $entity = $repository->find($id);

        // Error handling: 404 Not Found
        if (!empty($entity))
        {
            // $entity->getPricelog($recursive); // Object
            $entity = $entity->getPricelog($recursive); // Array
        } else {
            $result['_error']['error'] = 'not_found';
            $result['_error']['error_description'] = 'Record not found.';

            return new JsonResponse($result, 404);
        }

        // $resource = $this->resourceGenerator->fromObject($entity, $request); // Object
        $resource = $this->resourceGenerator->fromArray($entity); // Array

        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
