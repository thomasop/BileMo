<?php

namespace App\Controller;

use Test;
use App\Entity\Product;
use App\Handler\Paging;
use Swagger\Annotations as SWG;
use App\Repository\ProductRepository;
use App\Exception\IdNotFoundException;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class ProductController extends AbstractFOSRestController
{
    /**
     * function read product
     *
     * @Get(
     *     path = "/BileMo/product/{id}",
     *     name="app_product_detail",
     *     requirements = {"id"="\d+"}
     * )
     * @View(
     *     StatusCode=200
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Return product",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * @SWG\Tag(name="product")
     * @Security(name="Bearer")
     * 
     * @param Product $product
     * @param CacheInterface $cache
     * @return $product
     */
    public function read(Product $product = null, CacheInterface $cache)
    {
        if (!$product) {
            throw new IdNotFoundException('Le produit demandé n\'existe pas');
        }
        $response = new Response();
        $response->setPublic();
        $response->setMaxAge(3600);
        $response->headers->addCacheControlDirective('must-revalidate', true);

        $view = $this->view($product);
        $view->setResponse($response);

        return $this->handleView($view);
    }

    /**
     * function read products
     *
     * @Get(
     *     path = "/BileMo/product",
     *     name="app_product_all",
     * )
     * @QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="10",
     *     description="Max number of products per page"
     * )
     * @QueryParam(
     *     name="page",
     *     requirements="\d+",
     *     default="1",
     *     description="Page"
     * )
     * @View(
     *     StatusCode=200
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Return all products",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * @SWG\Tag(name="product")
     * @Security(name="Bearer")
     *
     * @param CacheInterface $cache
     * @param ProductRepository $productRepository
     * @param ParamFetcherInterface $paramFetcher
     * @param PaginatorInterface $paginator
     * @return $products
     */
    public function readAll(ProductRepository $productRepository, ParamFetcher $paramFetcher, PaginatorInterface $paginator)
    {
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');
        $list = $productRepository->findAll();
        $products = $paginator->paginate(
            $list,
            $page,
            $limit
        );
        if ($products->getTotalItemCount() == 0) {
            return $this->view(['message' => 'Aucun produit'], 200);
        }
        return new Paging($products);
    }
}
