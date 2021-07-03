<?php

declare(strict_types=1);

namespace App;

use Baldinof\RoadRunnerBundle\Event\WorkerStartEvent;
use Baldinof\RoadRunnerBundle\Event\WorkerStopEvent;
use Baldinof\RoadRunnerBundle\RoadRunnerBridge\HttpFoundationWorkerInterface;
use Baldinof\RoadRunnerBundle\Worker\Dependencies;
use Baldinof\RoadRunnerBundle\Worker\WorkerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Temporal\Worker\WorkerOptions;
use Temporal\WorkerFactory;

/**
 * @internal
 */
final class TemporalWorker implements WorkerInterface
{
    private KernelInterface $kernel;
    private LoggerInterface $logger;
    private Dependencies $dependencies;
    private HttpFoundationWorkerInterface $httpFoundationWorker;
    private iterable $workflows;
    private iterable $activities;

    public function __construct(
        KernelInterface $kernel,
        LoggerInterface $logger,
        HttpFoundationWorkerInterface $httpFoundationWorker,
        iterable $workflows,
        iterable $activities,
    )
    {
        $this->kernel = $kernel;
        $this->logger = $logger;
        $this->httpFoundationWorker = $httpFoundationWorker;

        /** @var Dependencies */
        $dependencies = $kernel->getContainer()->get(Dependencies::class);
        $this->dependencies = $dependencies;

//        var_dump($workflows);
//        exit(1);
        $this->workflows = $workflows;
        $this->activities = $activities;
    }

    public function start(): void
    {
        if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false) {
            Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
        }

        if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false) {
            Request::setTrustedHosts(explode(',', $trustedHosts));
        }

        $this->dependencies->getEventDispatcher()->dispatch(new WorkerStartEvent());

//        $container = $this->kernel->l;
//        $result = $container->get(WorkflowInterface::class);
//        var_dump($result);
//        exit(1);
        $factory = WorkerFactory::create();

        $worker = $factory->newWorker(
            'default',
            WorkerOptions::new()
                ->withMaxConcurrentActivityTaskPollers(2)
                ->withMaxConcurrentWorkflowTaskPollers(2)
        );

        foreach ($this->workflows as $workflow) {
            $worker->registerWorkflowTypes(get_class($workflow));
        }

        foreach ($this->activities as $activity) {
            $worker->registerActivityImplementations($activity);
        }

        $factory->run();

        $this->dependencies->getEventDispatcher()->dispatch(new WorkerStopEvent());
    }
}
