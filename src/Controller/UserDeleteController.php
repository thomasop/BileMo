<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class UserDeleteController extends AbstractFOSRestController
{
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