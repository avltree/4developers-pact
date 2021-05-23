<?php declare(strict_types=1);

namespace Tests\Integration;

use App\Infrastructure\GuzzleProducts;
use PhpPact\Consumer\Matcher\Matcher;

class GuzzleProductsTest extends PactTestCase
{
    /** @test */
    public function shouldReturnProducts(): void
    {
        $matcher = new Matcher();
        $this->mockInteraction(
            [
                'method'  => 'GET',
                'path'    => '/products',
                'headers' => [
                    'Content-Type' => 'application/vnd.api+json',
                    'Accept'       => 'application/vnd.api+json',
                ],
            ],
            [
                'status'  => 200,
                'body'    => [
                    'data' => $matcher->eachLike(
                        [
                            'type'       => 'products',
                            'id'         => $matcher->like(1),
                            'attributes' => [
                                'name' => $matcher->like('Awesome product'),
                            ],
                        ]
                    ),
                ],
                'headers' => [
                    'Content-Type' => 'application/vnd.api+json',
                ],
            ]
        );

        $service  = new GuzzleProducts();
        $products = $service->all();

        $this->assertCount(1, $products);
    }
}
