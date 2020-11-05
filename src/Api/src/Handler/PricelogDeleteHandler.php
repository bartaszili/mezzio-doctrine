<?php

declare(strict_types=1);

namespace Api\Handler;

use Api\Entity\Pricelog;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class PricelogDeleteHandler
 * @package Api\Handler
 */
class PricelogDeleteHandler implements RequestHandlerInterface
{
    protected $entityManager;
    protected $halResponseFactory;
    protected $resourceGenerator;
    protected $validTokens;

    /**
     * PricelogDeleteHandler constructor.
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
        $requestBody = $request->getParsedBody()['Request']['Pricelog'];

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

        $result = [];

        $entityRepository = $this->entityManager->getRepository(Pricelog::class);

        $entity = $entityRepository->find($request->getAttribute('id'));

        if (empty($entity)) {
            $result['_error']['error'] = 'not_found';
            $result['_error']['error_description'] = 'Record not found.';

            return new JsonResponse($result, 404);
        }

        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        } catch(ORMException $e) {
            $result['_error']['error'] = 'not_removed';
            $result['_error']['error_description'] = $e->getMessage();

            return new JsonResponse($result, 400);
        }

        $result['Request']['success'] = true;
        $result['Request']['success_message'] = [
            'action' => 'delete',
            'table_name' => 'pricelog',
            'row_id' => $request->getAttribute('id')
        ];

        return new JsonResponse($result);
    }
}
