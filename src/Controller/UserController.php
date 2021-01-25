<?php

namespace App\Controller;

use App\CacheKernel;
use App\Entity\User;
use App\Exception\IdNotFoundException;
use App\Exception\ResourceValidationException;
use App\Handler\Cache;
use App\Handler\Paging;
use App\Handler\UserHandler;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\Cache\CacheInterface;

class UserController extends AbstractFOSRestController
{
    /**
     * function read user.
     *
     * @Get(
     *     path = "/BileMo/user/{id}",
     *     name="app_user_detail",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     * @SWG\Response(
     *     response=200,
     *     description="Return user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Return exception, the user does not exist",
     * )
     * @SWG\Tag(name="user")
     * @Security(name="Bearer")
     *
     * @param User           $user
     * @param CacheInterface $cache
     *
     * @return $user
     */
    public function read(User $user = null, Cache $cache)
    {
        if (empty($user)) {
            throw new IdNotFoundException('L\'utilisateur n\'éxiste pas');
        }
        $view = $this->view($user);
        $cache->save($view);
        return $this->handleView($view);
    }

    /**
     * function read users.
     *
     * @Get(
     *     path = "/BileMo/user",
     *     name="app_user_all",
     * )
     * @QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="5",
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
     *     description="Return all users",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @SWG\Tag(name="user")
     * @Security(name="Bearer")
     *
     * @param CacheInterface        $cache
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return $users
     */
    public function readAll(UserRepository $userRepository, ParamFetcher $paramFetcher, PaginatorInterface $paginator)
    {
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');
        $list = $userRepository->findAll();
        $users = $paginator->paginate(
            $list,
            $page,
            $limit
        );
        if (0 == $users->getTotalItemCount()) {
            return $this->view(['message' => 'Aucun utilisateur'], 200);
        }

        return new Paging($users);
    }

    /**
     * function create user.
     *
     * @Post(
     *     path = "/BileMo/user",
     *     name="app_user_post"
     * )
     * @View(
     *     StatusCode=201
     * )
     * @ParamConverter("user", converter="fos_rest.request_body")
     * @SWG\Parameter(
     *   name="User",
     *   description="Fields to provide to create an user",
     *   in="body",
     *   required=true,
     *   type="string",
     *   @SWG\Schema(
     *     type="object",
     *     title="User field",
     *     @SWG\Property(property="name", type="string"),
     *     @SWG\Property(property="first_name", type="string"),
     *     @SWG\Property(property="mail", type="string")
     *     )
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Return user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Return exception, syntax error in the query",
     * )
     * @SWG\Tag(name="user")
     * @Security(name="Bearer")
     *
     * @param EntityManagerInterface $em
     *
     * @return $user
     */
    public function create(User $user, ConstraintViolationList $violations, UserHandler $userHandler)
    {
        if (count($violations) > 0) {
            $message = 'Le JSON envoyé contient des données non valides. Voici les erreurs que vous devez corriger: ';
            foreach ($violations as $violation) {
                $message .= sprintf('Champ %s: %s ', $violation->getPropertyPath(), $violation->getMessage());
            }
            throw new ResourceValidationException($message);
        }
        $user->setCustomer($this->getUser());
        $userHandler->addUser($user);

        return $this->view(
            $user,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('app_user_detail', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            ]
        );
    }

    /**
     * function delete user.
     *
     * @Delete(
     *     path = "/BileMo/user/{id}",
     *     name="app_user_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @View(
     *     StatusCode=204
     * )
     * @SWG\Response(
     *     response=204,
     *     description="Return 204 response",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Return exception, the user does not exist",
     * )
     * @SWG\Tag(name="user")
     * @Security(name="Bearer")
     *
     * @param User                   $user
     * @param EntityManagerInterface $emi
     * @param CacheInterface         $cache
     *
     * @return $user
     */
    public function delete(User $user = null, UserHandler $userHandler)
    {
        if (!$user) {
            throw new IdNotFoundException('L\'utilisateur n\'éxiste pas');
        }
        $userHandler->removeUser($user);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
