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

/**
 * Class PropertiesUpdateHandler
 * @package Api\Handler
 */
class PropertiesUpdateHandler implements RequestHandlerInterface
{
    protected $entityManager;
    protected $halResponseFactory;
    protected $resourceGenerator;
    protected $validTokens;

    /**
     * PropertiesUpdateHandler constructor.
     * @param EntityManager $entityManager
     * @param HalResponseFactory $halResponseFactory
     * @param ResourceGenerator $resourceGenerator
     * @param array $validTokens
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
            !isset($request->getParsedBody()['Request']['Properties'])
        ) {
            $result['_error']['error'] = 'bad_request';
            $result['_error']['error_description'] = 'Wrongly formated or missing request body.';

            return new JsonResponse($result, 400);
        }

        $requestBody = $request->getParsedBody()['Request']['Properties'];

        // Error handling: 400 Forbidden
        if (!isset($requestBody['token']) || empty($requestBody['token']) || (array_search($requestBody['token'], array_column($this->validTokens, 'token')) === false))
        {
            $result['_error']['error'] = 'forbidden';
            $result['_error']['error_description'] = 'Access validation error.';

            return new JsonResponse($result, 400);
        }

        $entityRepository = $this->entityManager->getRepository(Property::class);

        $entity = $entityRepository->find($request->getAttribute('id'));

        if (empty($entity)) {
            $result['_error']['error'] = 'not_found';
            $result['_error']['error_description'] = 'Record not found.';

            return new JsonResponse($result, 404);
        }

        if (!isset($requestBody['update']) || empty($requestBody['update']))
        {
            $result['_error']['error'] = 'missing_attribute';
            $result['_error']['error_description'] = 'No update body sent.';

            return new JsonResponse($result, 400);
        }

        $update = $requestBody['update'];

        try {
            $entity->setProperty($update);

            // new modified datetime only when not setting duplicates
            if (!isset($update['duplicates']) || (isset($update['duplicates']) && empty($update['duplicates'])))
            {
                $entity->setModified(new \DateTime("now"));
            }

            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        } catch(ORMException $e) {
            $result['_error']['error'] = 'not_updated';
            $result['_error']['error_description'] = $e->getMessage();

            return new JsonResponse($result, 400);
        }

        $resource = $this->resourceGenerator->fromObject($entity, $request);

        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
