<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Core;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../../config/{packages}/*.yaml');
        $container->import('../../config/common_services.yaml');
        $container->import(__DIR__.'/Resources/services.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../../config/routes.yaml');
    }

    /**
     * {@inheritdoc}
     *
     * Hides log dir as logs directed to STDOUT/STDERR
     * @see https://12factor.net/logs
     */
    public function getLogDir(): string
    {
        return $this->getProjectDir().'/var';
    }
}
