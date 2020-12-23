<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;



class UserTest extends KernelTestCase
{

    /**
     * Test User Entity
     *
     * @return void
     */
	public function testUser()
	{
        $user = (new User())
            ->setName("test name")
		    ->setFirstName("test first name")
		    ->setMail("tdss33@hotmail.com");
		self::bootKernel();
		$error = self::$container->get('validator')->validate($user);
		$this->assertCount(0, $error);
	}
}