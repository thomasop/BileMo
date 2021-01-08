<?php

namespace App\Handler;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class UserHandler
{
    private $emi;
    private $cache;

    public function __construct(EntityManagerInterface $emi, CacheInterface $cache)
    {
        $this->emi = $emi;
        $this->cache = $cache;
    }
    public function addUser(User $user)
    {
        $this->emi->persist($user);
        $this->emi->flush();
    }

    public function removeUser(User $user)
    {
        $this->emi->remove($user);
        $this->emi->flush();
        $this->cache->delete('user_' . $user->getId());
    }
}
