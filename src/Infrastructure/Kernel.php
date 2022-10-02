<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use function dirname;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getProjectDir() : string
    {
        return dirname(__DIR__) . '/..';
    }

    /**
     * @codeCoverageIgnore
     */
    protected function configureContainer(ContainerConfigurator $container) : void
    {
        $confDir = $this->getProjectDir() . '/config';

        $container->import($confDir . '/{packages}/*.yaml');
        $container->import($confDir . '/{packages}/' . $this->getEnvironment() . '/*.yaml');

        $container->import($confDir . '/{services}.yaml');
        $container->import($confDir . '/{services}_' . $this->getEnvironment() . '.yaml');
    }

    /**
     * @codeCoverageIgnore
     */
    protected function configureRoutes(RoutingConfigurator $routes) : void
    {
        $confDir = $this->getProjectDir() . '/config';

        $routes->import($confDir . '/{routes}/' . $this->getEnvironment() . '/*.yaml');
        $routes->import($confDir . '/{routes}/*.yaml');

        $routes->import($confDir . '/{routes}.yaml');
    }
}
