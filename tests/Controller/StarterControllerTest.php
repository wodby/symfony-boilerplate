<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class StarterControllerTest extends WebTestCase
{
    /**
     * The starter landing page confirms that the application is running.
     */
    public function testTheLandingPageIsAvailable(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Your Symfony app is running.');
    }

    /**
     * The health endpoint is intentionally independent of application dependencies.
     */
    public function testTheHealthEndpointIsAvailable(): void
    {
        $client = static::createClient();
        $client->request('GET', '/healthz');

        self::assertResponseIsSuccessful();
        self::assertJsonStringEqualsJsonString('{"status":"ok"}', (string) $client->getResponse()->getContent());
    }
}
