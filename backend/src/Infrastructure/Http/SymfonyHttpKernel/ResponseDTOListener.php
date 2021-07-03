<?php

declare(strict_types=1);

namespace Infrastructure\Http\SymfonyHttpKernel;

use InvalidArgumentException;
use JsonException;
use LogicException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use function get_class;
use function json_encode;
use function sprintf;

final class ResponseDTOListener implements EventSubscriberInterface
{
    /**
     * @param ViewEvent $event
     *
     * @throws InvalidArgumentException
     */
    public function convertDTOToResponse(ViewEvent $event): void
    {
        $responseDTO = $event->getControllerResult();

        if (!($responseDTO instanceof ResponseDTO)) {
            throw new LogicException(
                sprintf('%s should implement %s', get_class($responseDTO), ResponseDTO::class)
            );
        }

        try {
            $responseBody = json_encode($responseDTO, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new LogicException(sprintf('%s should implement JsonSerializable', get_class($responseDTO)), 0, $e);
        }
        $event->setResponse(new Response($responseBody, $responseDTO->getStatus(), $responseDTO->getHeaders()));
    }

    /**
     * @return array<string, array>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['convertDTOToResponse', 30],
        ];
    }
}
