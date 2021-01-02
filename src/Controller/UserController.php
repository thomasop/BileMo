<?php

namespace App\Controller;

use App\Entity\User;
use App\Handler\UserHandler;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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
     * @View(
     *     serializerGroups={"user_read"},
     * )
     *
     * @param User $user
     * @param CacheInterface $cache
     * @return $user
     */
    public function read(User $user = null, CacheInterface $cache)
    {
        if (empty($user)) {
            throw new HttpException(400, 'L\'utilisateur demandé n\'existe pas');
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
     * @View(
     *     serializerGroups={"user_read"},
     *     StatusCode=200
     * )
     *
     * @param CacheInterface $cache
     * @param UserRepository $userRepository
     * @return $user
     */
    public function readAll(CacheInterface $cache, UserRepository $userRepository)
    {
        $list = $userRepository->findAll();
        if (empty($list)) {
            throw new HttpException(200, 'Aucun utilisateur');
        }
        return $cache->get('users', function (ItemInterface $item) use ($list) {
            $item->expiresAfter(3600);
            return $list;
        });
    }
    
    /**
     * function create user
     *
     * @Post(
     *     path = "/BileMo/user",
     *     name="app_user_post"
     * )
     * @View(
     *     serializerGroups={"user_read"},
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
            throw new HttpException(404, $message);
        }
        $user->setCustomer($this->getUser());
        $userHandler->addUser($user);

        return $this->view(
            $user,
            Response::HTTP_CREATED,
            [
                'Location' => $this->generateUrl('app_user_detail', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
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
     *     serializerGroups={"user_read"},
     *     StatusCode=204
     * )
     *
     * @param User $user
     * @param EntityManagerInterface $emi
     * @param CacheInterface $cache
     * @return $user
     */
    public function delete(User $user, UserHandler $userHandler)
    {
        if(!$user) {
            throw new HttpException(400, 'L\'utilisateur demandé n\'existe pas');
        }
        $userHandler->removeUser($user);
        
        return $user;
    }
}
