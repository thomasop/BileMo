<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

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
            return $this->view(['message' => 'Aucun produit'], 200);
        }
        return $cache->get('products', function (ItemInterface $item) use ($list) {
            $item->expiresAfter(3600);
            return $list;
        });
    }
}
