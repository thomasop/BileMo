<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class UsersGetController extends AbstractFOSRestController
{
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
}
