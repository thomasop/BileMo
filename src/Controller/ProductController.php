<?php

namespace App\Controller;

use App\Entity\Product;
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
     * @View
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
}