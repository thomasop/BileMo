<?php

namespace App\Controller;

use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class UserGetController extends AbstractFOSRestController
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
}
