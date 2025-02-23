<?php

declare(strict_types=1);

namespace SamboSearchTest\Client\DTO;

use PHPUnit\Framework\TestCase;
use SamboSearch\Client\DTO\ProductsDTO;

class ProductsDTOTest extends TestCase
{
    public function testInvalidProductsAreSkipped(): void
    {
        $productsDTO = ProductsDTO::build([['i am invalid haha']]);

        $this->assertCount(0, $productsDTO);
    }

    public function testInvalidProductsAreSkippedWhileValidOnesAreKept(): void
    {
        $productsDTO = ProductsDTO::build([
            ['i am invalid haha'],
            [
                'type' => 'product',
                'data' => [
                    'id' => '1',
                    'name' => 'Hello World',
                ],
            ],
        ]);

        $this->assertCount(1, $productsDTO);
    }

    public function testIteratorMethods(): void
    {
        $productsDTO = ProductsDTO::build([
            [
                'type' => 'product',
                'data' => [
                    'id' => '1',
                    'name' => 'Hello World',
                ],
            ],
            [
                'type' => 'product',
                'data' => [
                    'id' => '2',
                    'name' => 'Hello World',
                ],
            ],
            [
                'type' => 'product',
                'data' => [
                    'id' => '3',
                    'name' => 'Hello World',
                ],
            ],
        ]);

        $this->assertSame($productsDTO->key(), 0);
        $this->assertSame($productsDTO->current()->getId(), '1');

        $productsDTO->next();
        $this->assertSame($productsDTO->current()->getId(), '2');

        $productsDTO->next();
        $this->assertTrue($productsDTO->valid());
        $this->assertSame($productsDTO->key(), 2);
        $this->assertSame($productsDTO->current()->getId(), '3');

        $productsDTO->next();
        $this->assertFalse($productsDTO->valid());

        $productsDTO->rewind();
        $this->assertSame($productsDTO->current()->getId(), '1');
    }
}
