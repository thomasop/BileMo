<?php

namespace App\Controller;

use App\Entity\Product;
use App\Exception\IdNotFoundException;
use App\Handler\Cache;
use App\Handler\Paging;
use App\Repository\ProductRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;

class LoginController extends AbstractFOSRestController
{
    /**
     * function read product.
     *
     * @Post(
     *     path = "/BileMo/login_check",
     *     name="app_login_check",
     * )
     * @SWG\Response(
     *     response="200",
     *     description="Success"
     * )
     * @SWG\Response(
     *     response="401",
     *     description="Unauthorized"
     * )
     * @SWG\Parameter(
     *   name="Login",
     *   description="Fields to provide to login",
     *   in="body",
     *   required=true,
     *   type="string",
     *   @SWG\Schema(
     *     type="object",
     *     title="Login field",
     *     @SWG\Property(property="username", type="string"),
     *     @SWG\Property(property="password", type="string")
     *     )
     * )
     * @SWG\Tag(name="login")
     *
     * @return token
     */
    public function login()
    {
    }
}