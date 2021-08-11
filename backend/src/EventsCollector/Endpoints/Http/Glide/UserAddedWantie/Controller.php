<?php

declare(strict_types=1);

namespace EventsCollector\Endpoints\Http\Glide\UserAddedWantie;

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Infrastructure\Http\SymfonyHttpKernel\ResponseDTO;

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
     * @Route("/glide/usedAddedWantie", name="Glide_UserAddedWantie")
     * @param Request $request
     * @return ResponseDTO
     */
    public function __invoke(Request $request): ResponseDTO
    {
        $this->logger->info("{$request->getRequest()} received");
        return new Response204();
    }
}
