<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\IdNotFoundException;
use App\Exception\ResourceValidationException;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class UserController extends AbstractFOSRestController
{
    /**
     * function read user
     *
     * @Get(
     *     path = "/BileMo/user/{id}",
     *     name="app_user_detail",
     *     requirements = {"id"="\d+"}
     * )
     * @View
     *
     * @param User $user
     * @param CacheInterface $cache
     * @return $user
     */
    public function read(User $user = null, CacheInterface $cache)
    {
        if (empty($user)) {
            throw new IdNotFoundException('L\'utilisateur n\'éxiste pas');
        }
        return $cache->get('user_' . $user->getId(), function (ItemInterface $item) use ($user) {
            $item->expiresAfter(3600);
            return $user;
        });
    }

    /**
     * function read users
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
     *
     * @param CacheInterface $cache
     * @param UserRepository $userRepository
     * @param ParamFetcherInterface $paramFetcher
     * @param PaginatorInterface $paginator
     * @return $users
     */
    public function readAll(CacheInterface $cache, UserRepository $userRepository, ParamFetcher $paramFetcher, PaginatorInterface $paginator)
    {

        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');
        $list = $userRepository->findAllUser($page, $limit);
        $item = $cache->get('products', function (ItemInterface $item) use ($list) {
            $item->expiresAfter(3600);
            return ($list);
        });
        $users = $paginator->paginate(
            $list,
            $page,
            $limit
        );
        if ($users->getTotalItemCount() == 0) {
            return $this->view(['message' => 'Aucun utilisateur'], 200);
        }
        return new Paging($users);

    }

    /**
     * function create user
     *
     * @Post(
     *     path = "/BileMo/user",
     *     name="app_user_post"
     * )
     * @View(
     *     StatusCode=201
     * )
     * @ParamConverter("user", converter="fos_rest.request_body")
     *
     * @param User $user
     * @param EntityManagerInterface $em
     * @param ConstraintViolationList $violations
     * @return $user
     */
    public function create(User $user, ConstraintViolationList $violations, UserHandler $userHandler)
    {
        if (count($violations) > 0) {
            $message = 'Le JSON envoyé contient des données non valides. Voici les erreurs que vous devez corriger: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Champ %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
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
     * function delete user
     *
     * @Delete(
     *     path = "/BileMo/user/{id}",
     *     name="app_user_delete",
     *     requirements = {"id"="\d+"}
     * )
     * @View(
     *     StatusCode=204
     * )
     *
     * @param User $user
     * @param EntityManagerInterface $emi
     * @param CacheInterface $cache
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
