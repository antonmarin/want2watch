<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\Http\SymfonyHttpKernel;

use InvalidArgumentException;
use JsonException;
use LogicException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

use function PHPUnit\Framework\assertEquals;

final class ResponseDTOListenerTest extends TestCase
{
    /**
     * @test
     */
    public function subscribeToKernelViewEvent(): void
    {
        $subscribedEvents = ResponseDTOListener::getSubscribedEvents();

        self::assertArrayHasKey(KernelEvents::VIEW, $subscribedEvents);
    }

    /**
     * @throws JsonException
     *
     * @test
     */
    public function shouldConvertDTOtoResponseBodyWhenDTOSupportsAndHasBody(): void
    {
        $listener = new ResponseDTOListener();
        $responseDTO = new DTOConvertableToBody();

        $event = new ViewEvent(
            $this->createMock(HttpKernelInterface::class),
            $this->createMock(Request::class),
            HttpKernel::MASTER_REQUEST,
            $responseDTO
        );
        $listener->convertDTOToResponse($event);

        $response = $event->getResponse();
        self::assertInstanceOf(Response::class, $response);
        assertEquals(json_encode($responseDTO, JSON_THROW_ON_ERROR), $response->getContent());
        assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        assertEquals(
            new ResponseHeaderBag(['Content-Type' => DTOConvertableToBody::HEADER_TEXT_JAVASCRIPT]),
            $response->headers
        );
    }

    /**
     * @test
     */
    public function shouldExceptionWhenDTONotImplementRequired(): void
    {
        $listener = new ResponseDTOListener();
        $responseDTO = new NotImplements();

        $event = new ViewEvent(
            $this->createMock(HttpKernelInterface::class),
            $this->createMock(Request::class),
            HttpKernel::MASTER_REQUEST,
            $responseDTO
        );
        $this->expectException(LogicException::class);
        $listener->convertDTOToResponse($event);
    }

    /**
     * @test
     */
    public function shouldExceptionWhenDTONotSerializable(): void
    {
        $listener = new ResponseDTOListener();
        $responseDTO = new NotSerializable();

        $event = new ViewEvent(
            $this->createMock(HttpKernelInterface::class),
            $this->createMock(Request::class),
            HttpKernel::MASTER_REQUEST,
            $responseDTO
        );
        $this->expectException(LogicException::class);
        $listener->convertDTOToResponse($event);
    }

    /**
     * @test
     */
    public function shouldExceptionWhenBadStatusCode(): void
    {
        $listener = new ResponseDTOListener();
        $responseDTO = new BadStatusCode();

        $event = new ViewEvent(
            $this->createMock(HttpKernelInterface::class),
            $this->createMock(Request::class),
            HttpKernel::MASTER_REQUEST,
            $responseDTO
        );
        $this->expectException(InvalidArgumentException::class);
        $listener->convertDTOToResponse($event);
    }
}

final class NotImplements
{
    public string $message = 'Some message';
}

final class BadStatusCode implements ResponseDTO
{
    public function getStatus(): int
    {
        return 734;
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function jsonSerialize()
    {
        return 'smth';
    }
}

final class NotSerializable implements ResponseDTO
{
    public function getStatus(): int
    {
        return 200;
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function jsonSerialize()
    {
        /** @noinspection PhpComposerExtensionStubsInspection */
        return curl_init('https://google.com');
    }
}

final class DTOConvertableToBody implements ResponseDTO
{
    public const HEADER_TEXT_JAVASCRIPT = 'text/javascript';
    private string $message = 'Some message';

    public function jsonSerialize(): array
    {
        return ['message' => $this->message];
    }

    public function getStatus(): int
    {
        return Response::HTTP_CREATED;
    }

    public function getHeaders(): array
    {
        return [
            'Content-Type' => self::HEADER_TEXT_JAVASCRIPT,
        ];
    }
}
