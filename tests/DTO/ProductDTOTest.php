<?php

declare(strict_types=1);

namespace SamboSearchTest\Client\DTO;

use PHPUnit\Framework\TestCase;
use SamboSearch\Client\DTO\ProductDTO;
use SamboSearch\Client\Exception\Server\UnparsableResponseException;

class ProductDTOTest extends TestCase
{
    public function testUnparsableProductThrowsException(): void
    {
        $this->expectException(UnparsableResponseException::class);
        $this->expectExceptionMessage('Can not parse product data');

        new ProductDTO(['i am unparsable :)']);
    }

    public function testGetters(): void
    {
        $expectedId = 'some-cool-id';
        $expectedName = 'This is an awesome product';

        $product = new ProductDTO([
            'type' => 'product',
            'data' => [
                'id' => $expectedId,
                'name' => $expectedName,
            ],
        ]);

        $this->assertSame($expectedId, $product->getId());
        $this->assertSame($expectedName, $product->getName());
    }
}
