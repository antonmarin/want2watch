<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Infrastructure\Http\SymfonyHttpKernel;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class JsonRequestTransformerListenerTest extends TestCase
{
    public const HEADER_CONTENT_TYPE = 'HTTP_CONTENT_TYPE';

    /**
     * @test
     */
    public function shouldReplaceJsonBodyWithArrayWhenRequestContentTypeIsJson(): void
    {
        $listener = new JsonRequestTransformerListener();
        $expectedData = ['key1' => 'val1', 'key2' => 'val2'];

        $event = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request(
                [],
                [],
                [],
                [],
                [],
                [self::HEADER_CONTENT_TYPE => 'application/json'],
                json_encode($expectedData, JSON_THROW_ON_ERROR)
            ),
            HttpKernelInterface::MAIN_REQUEST,
        );
        $listener->onKernelRequest($event);

        self::assertSame($expectedData, $event->getRequest()->request->all());
    }

    /**
     * @test
     */
    public function shouldNotAlterRequestWhenContentTypeIsNotJson(): void
    {
        $listener = new JsonRequestTransformerListener();
        $expectedData = ['key1' => 'val1', 'key2' => 'val2'];

        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            [self::HEADER_CONTENT_TYPE => 'application/xml'],
            json_encode($expectedData, JSON_THROW_ON_ERROR)
        );
        $event = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );
        $listener->onKernelRequest($event);

        self::assertSame([], $event->getRequest()->request->all());
    }

    /**
     * @test
     */
    public function shouldNotAlterRequestWhenRequestBodyIsEmpty(): void
    {
        $listener = new JsonRequestTransformerListener();

        $event = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request(
                [],
                [],
                [],
                [],
                [],
                [self::HEADER_CONTENT_TYPE => 'application/json'],
                null
            ),
            HttpKernelInterface::MAIN_REQUEST,
        );
        $listener->onKernelRequest($event);

        self::assertSame([], $event->getRequest()->request->all());
    }

    /**
     * @test
     */
    public function shouldResponseWithBadRequestWhenRequestBodyIsNotValidJson(): void
    {
        $listener = new JsonRequestTransformerListener();

        $invalidJsonContent = '{asdfas:true}';
        $event = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request(
                [],
                [],
                [],
                [],
                [],
                [self::HEADER_CONTENT_TYPE => 'application/json'],
                $invalidJsonContent
            ),
            HttpKernelInterface::MAIN_REQUEST,
        );

        $this->expectException(JsonException::class);
        $listener->onKernelRequest($event);
    }
}
