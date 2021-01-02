<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
     *
     * @param Product $product
     * @param CacheInterface $cache
     * @return $product
     */
    public function read(Product $product, CacheInterface $cache)
    {
        if(!$product) {
            throw new HttpException(400, 'Le produit demandé n\'existe pas');
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
     * @View(
     *     StatusCode=200
     * )
     *
     * @param CacheInterface $cache
     * @param ProductRepository $productRepository
     * @return $list
     */
    public function readAll(CacheInterface $cache, ProductRepository $productRepository)
    {
        $list = $productRepository->findAll();
        if (empty($list)) {
            throw new HttpException(200, 'Aucun produit');
        }
        return $cache->get('products', function (ItemInterface $item) use ($list) {
            $item->expiresAfter(3600);
            return $list;
        });
    }
}
