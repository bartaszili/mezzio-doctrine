<?php

declare(strict_types=1);

namespace Api\Handler;

use Api\Entity\Property;
use Api\Entity\Collection\PropertyCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PropertiesSearchHandler implements RequestHandlerInterface
{
    protected $entityManager;
    protected $halResponseFactory;
    protected $resourceGenerator;
    protected $validTokens;
    protected $pageCount;

    public function __construct(
        EntityManager $entityManager,
        HalResponseFactory $halResponseFactory,
        ResourceGenerator $resourceGenerator,
        $validTokens,
        $pageCount
    ) {
        $this->entityManager = $entityManager;
        $this->halResponseFactory = $halResponseFactory;
        $this->resourceGenerator = $resourceGenerator;
        $this->validTokens = $validTokens;
        $this->pageCount = $pageCount;
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

        $repository = $this->entityManager
            ->getRepository(Property::class);

        $query = $repository
            ->createQueryBuilder('p')
            // return only active rows
            ->andwhere('p.is_active = :is_active')
            ->setParameter('is_active', true);

        // apply search parameters
        if (isset($requestBody['search']) || !empty($requestBody['search'])) {
            $search = $requestBody['search'];

            foreach ($search as $key => $value)
            {
                if (!empty($search[$key]) && (substr_compare($key, '_min', -4, 4, true) != 0) && (substr_compare($key, '_max', -4, 4, true) != 0))
                {
                    $sql = '';
                    $i = 0;
                    foreach ($value as $item)
                    {
                        if ($i == 0) { $sql .= "p.{$key} LIKE '%{$item}%'"; }
                        else { $sql .= " OR p.{$key} LIKE '%{$item}%'"; }
                        $i++;
                    }
    
                    $query->andwhere($sql);
                }

                if (!empty($search[$key]) && (substr_compare($key, '_min', -4, 4, true) == 0))
                {
                    $key = substr($key, 0, -4);
                    $value = is_string($value) ? "'".$value."'" : $value;

                    $query->andwhere('p.'.$key.' >= '.$value);
                }

                if (!empty($search[$key]) && (substr_compare($key, '_max', -4, 4, true) == 0))
                {
                    $key = substr($key, 0, -4);
                    $value = is_string($value) ? "'".$value."'" : $value;

                    $query->andwhere('p.'.$key.' <= '.$value);
                }
            }
        }

        // order and pagination
        $pageLimit = (isset($requestBody['page_limit']) && !empty($requestBody['page_limit'])) ? $requestBody['page_limit'] : $this->pageCount;
        $query->addOrderBy('p.modified','desc')
            ->addOrderBy('p.name','asc')
            ->setMaxResults($pageLimit)
            ->getQuery();

        $paginator = new PropertyCollection($query);

        $resource = $this->resourceGenerator->fromObject($paginator, $request);

        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
