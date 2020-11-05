<?php

declare(strict_types=1);

namespace Api\Handler;

use Api\Entity\Pricelog;
use Api\Entity\Collection\PricelogCollection;
use Doctrine\ORM\EntityManager;
// use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class PricelogSearchHandler
 * @package Api\Handler
 */
class PricelogSearchHandler implements RequestHandlerInterface
{
    protected $entityManager;
    protected $halResponseFactory;
    protected $resourceGenerator;
    protected $validTokens;
    protected $pageCount;

    /**
     * PricelogSearchHandler constructor.
     * @param EntityManager $entityManager
     * @param HalResponseFactory $halResponseFactory
     * @param ResourceGenerator $resourceGenerator
     * @param array $validTokens;
     * @param int $pageCount;
     */
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

        $recursive = false;
        if (isset($requestBody['recursive']) && !empty($requestBody['recursive'])) {
            $recursive = true;
        }

        $repository = $this->entityManager->getRepository(Pricelog::class);

        $query = $repository->createQueryBuilder('pl');

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
                        if ($i == 0) { $sql .= "pl.{$key} LIKE '%{$item}%'"; }
                        else { $sql .= " OR pl.{$key} LIKE '%{$item}%'"; }
                        $i++;
                    }

                    $query->andwhere($sql);
                }

                if (!empty($search[$key]) && (substr_compare($key, '_min', -4, 4, true) == 0))
                {
                    $key = substr($key, 0, -4);
                    $value = is_string($value) ? "'".$value."'" : $value;

                    $query->andwhere('pl.'.$key.' >= '.$value);
                }

                if (!empty($search[$key]) && (substr_compare($key, '_max', -4, 4, true) == 0))
                {
                    $key = substr($key, 0, -4);
                    $value = is_string($value) ? "'".$value."'" : $value;

                    $query->andwhere('pl.'.$key.' <= '.$value);
                }
            }
        }
        
        // order and pagination
        $pageLimit = (isset($requestBody['page_limit']) && !empty($requestBody['page_limit'])) ? $requestBody['page_limit'] : $this->pageCount;
        $query->addOrderBy('pl.created','asc')
            ->setMaxResults($pageLimit)
            ->getQuery();


        $paginator = new PricelogCollection($query);

        // var_dump($paginator);die;
        // $paginator->getPricelog($recursive);
        // $resource = $this->resourceGenerator->fromArray($paginator);

        $resource = $this->resourceGenerator->fromObject($paginator, $request);

        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
