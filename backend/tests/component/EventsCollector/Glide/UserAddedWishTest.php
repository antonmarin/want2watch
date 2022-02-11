<?php

declare(strict_types=1);

namespace EventsCollector\Glide;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserAddedWishTest extends WebTestCase
{
    /**
     * @test
     */
    public function should401WhenNotAuthenticated(): void
    {
        $client = self::createClient();

        $client->request('GET', '/glide/wantieAdded');

        self::assertResponseStatusCodeSame(401);
    }

    /**
     * @test
     */
    public function should401WhenAuthenticatedBad(): void
    {
        $client = self::createClient();

        $client->request(
            'GET',
            '/glide/wantieAdded',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Basic anyStringExceptRequiredPassword']
        );

        self::assertResponseStatusCodeSame(401);
    }
}
