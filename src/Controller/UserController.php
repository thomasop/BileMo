<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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
    public function read(User $user, CacheInterface $cache)
    {
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
            return $this->view(['message' => 'Aucun utilisateur'], 200);
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
     * @param Request $request
     * @param SerializerInterface $serialize
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     * @return $user
     */
    public function create(User $user, SerializerInterface $serialize, EntityManagerInterface $emi, ValidatorInterface $validator)
    {
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $data = $serialize->serialize($errors, 'json');
            return new JsonResponse($data, 400, [], true);
        }
        $user->setCustomer($this->getUser());
        $emi->persist($user);
        $emi->flush();

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
     * @return $user
     */
    public function delete(User $user, EntityManagerInterface $emi, CacheInterface $cache)
    {
        if($user) {
            $emi->remove($user);
            $emi->flush();
            $cache->delete('user_'.$user->getId());
            return $user;
        }        
    }
}
