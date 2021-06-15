<?php

declare(strict_types=1);

namespace antonmarin\want2watch\Infrastructure\IoC\Symfony;

use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\EventListener\ErrorListener;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

final class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import($this->getProjectDir() . '/config/{packages}/*.yaml');
        $container->import($this->getProjectDir() . '/config/services.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import($this->getProjectDir() . '/config/routes.yaml');
    }

    /**
     * {@inheritdoc}
     *
     * Hides log dir as logs directed to STDOUT/STDERR
     * @see https://12factor.net/logs
     */
    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var';
    }

    public function process(ContainerBuilder $container): void
    {
        $criticalLoggerId = 'logger.critical';
        $container->setDefinition($criticalLoggerId, new Definition(Logger::class, [LogLevel::CRITICAL]));
        $exceptionListenerId = 'exception_listener';
        $container->removeDefinition($exceptionListenerId);
        $container->setDefinition(
            $exceptionListenerId,
            (new Definition(
                ErrorListener::class,
                [
                    param('kernel.error_controller'),
                    new Reference($criticalLoggerId),
                    param('kernel.debug'),
                ]
            ))
                ->addTag('kernel.event_subscriber')
                ->addTag('monolog.logger', ['channel' => 'request'])
        );
    }
}
