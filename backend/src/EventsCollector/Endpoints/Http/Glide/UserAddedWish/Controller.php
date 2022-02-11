<?php

declare(strict_types=1);

namespace EventsCollector\Endpoints\Http\Glide\UserAddedWish;

use Infrastructure\Http\SymfonyHttpKernel\ResponseDTO;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;

final class Controller
{
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route("/glide/wantieAdded", name="Glide_WantieAdded")
     * @param Request $request
     * @return ResponseDTO
     */
    public function __invoke(Request $request): ResponseDTO
    {
        $this->logger->info("{$request->getRequest()} received");
        return new Response204();
    }
}
