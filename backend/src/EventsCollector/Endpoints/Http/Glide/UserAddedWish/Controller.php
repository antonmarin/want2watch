<?php

declare(strict_types=1);

namespace EventsCollector\Endpoints\Http\Glide\UserAddedWish;

use Core\UseCases\UpdateMovieInfo;
use Infrastructure\Http\SymfonyHttpKernel\ResponseDTO;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;

final class Controller
{
    private LoggerInterface $logger;
    private UpdateMovieInfo $useCase;

    public function __construct(LoggerInterface $logger, UpdateMovieInfo $useCase)
    {
        $this->logger = $logger;
        $this->useCase = $useCase;
    }

    /**
     * @Route("/glide/wantieAdded", name="Glide_WantieAdded")
     * @param Request $request
     * @return ResponseDTO
     */
    public function __invoke(Request $request): ResponseDTO
    {
        $this->logger->info("{$request->getRequest()} received");
        $this->useCase->execute($request->getWishTitle());
        return new Response204();
    }
}
