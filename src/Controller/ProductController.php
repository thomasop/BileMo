<?php

namespace App\Controller;

use App\Entity\Product;
use App\Exception\IdNotFoundException;
use App\Handler\Paging;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;

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
        return $cache->get('product_' . $product->getId(), function (ItemInterface $item) use ($product) {
            $item->expiresAfter(3600);
            return $product;
        });
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
    public function readAll(CacheInterface $cache, ProductRepository $productRepository, ParamFetcher $paramFetcher, PaginatorInterface $paginator)
    {
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');
        $list = $productRepository->findAllProduct($page, $limit);
        $item = $cache->get('products', function (ItemInterface $item) use ($list) {
            $item->expiresAfter(3600);
            return ($list);
        });
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
