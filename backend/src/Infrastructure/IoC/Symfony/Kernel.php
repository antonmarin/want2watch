<?php

declare(strict_types=1);

namespace Infrastructure\IoC\Symfony;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\EventListener\ErrorListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
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
        $container->setAlias(LoggerInterface::class, $this->isDebug() ? 'logger.debug' : 'logger.info');

        $container->setDefinition(
            'exception_listener',
            (new Definition(
                ErrorListener::class,
                [
                    (string)param('kernel.error_controller'),
                    new Reference($this->isDebug() ? 'logger.debug' : 'logger.critical'),
                    (string)param('kernel.debug'),
                ]
            ))
                ->addTag('kernel.event_subscriber')
                ->addTag('monolog.logger', ['channel' => 'request'])
        );

        $container->setDefinition(
            'router_listener',
            (new Definition(
                RouterListener::class,
                [
                    new Reference('router'),
                    new Reference('request_stack'),
                    new Reference('router.request_context'),
                    new Reference($this->isDebug() ? 'logger.debug' : 'logger.notice'),
                    (string)param('kernel.project_dir'),
                    (string)param('kernel.debug'),
                ]
            ))
                ->addTag('kernel.event_subscriber')
                ->addTag('monolog.logger', ['channel' => 'request'])
        );
    }
}
