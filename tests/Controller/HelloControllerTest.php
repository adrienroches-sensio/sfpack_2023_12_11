<?php

namespace App\Tests\Controller;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HelloControllerTest extends WebTestCase
{
    public static function provideValidHelloUris(): Generator
    {
        yield 'default' => ['/hello'];
        yield 'name without "-"' => ['/hello/Plop'];
        yield 'name having only one "-"' => ['/hello/Jean-Pierre'];
    }

    /**
     * @dataProvider provideValidHelloUris
     *
     * @group smoke-test
     */
    public function testHelloUri(string $uri): void
    {
        $client = static::createClient();
        $client->request('GET', $uri);

        $this->assertResponseIsSuccessful();
    }
}
