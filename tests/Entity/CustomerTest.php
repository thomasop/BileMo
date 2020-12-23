<?php

namespace App\Tests\Entity;

use App\Entity\Customer;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;



class CustomerTest extends KernelTestCase
{

    /**
     * Test Customer Entity
     *
     * @return void
     */
	public function testCustomer()
	{
		$customer = (new Customer())
		    ->setName("test name")
		    ->setFirstName("test first name")
		    ->setPassword("Tes5765TG6?t")
            ->setMail("tdss33@hotmail.com");
		self::bootKernel();
		$error = self::$container->get('validator')->validate($customer);
		$this->assertCount(0, $error);
	}
}