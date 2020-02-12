<?php

namespace Bag2\HttpTest;

use Nyholm\Psr7\Factory\Psr17Factory;

final class HttpFactoryTest extends TestCase
{
    /** @var Psr17Factory */
    private $http_factory;

    public function setUp(): void
    {
        $this->http_factory = new Psr17Factory();
    }

    public function test(): void
    {
        $subject = new HttpFactory($this->http_factory, $this->http_factory, $this->http_factory, ['number' => 2]);

        $this->assertInstanceOf(Request::class, $subject->createRequest('GET', 'https://example.com/'));
        $this->assertInstanceOf(Response::class, $subject->createResponse(200));
        $this->assertInstanceOf(ServerRequest::class, $subject->createServerRequest('GET', 'https://example.com/', []));
    }
}
