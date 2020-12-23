<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;



class ProductTest extends KernelTestCase
{

    /**
     * Test Product Entity
     *
     * @return void
     */
	public function testProduct()
	{
		$product = (new Product())
		    ->setModel("test model")
		    ->setDescription("test description")
		    ->setPrice("5765")
            ->setBrand("test brand");
		self::bootKernel();
		$error = self::$container->get('validator')->validate($product);
		$this->assertCount(0, $error);
	}
}