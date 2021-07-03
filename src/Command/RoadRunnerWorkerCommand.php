<?php

declare(strict_types=1);

namespace App\Command;

use App\TemporalWorker;
use Baldinof\RoadRunnerBundle\Worker\WorkerInterface as HttpWorker;
use Spiral\RoadRunner\Environment;
use Spiral\RoadRunner\Environment\Mode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class RoadRunnerWorkerCommand extends Command
{
    protected static $defaultName = 'roadrunner:worker';

    private HttpWorker $httpWorker;
    private TemporalWorker $temporalWorker;

    public function __construct(
        HttpWorker $httpWorker,
        TemporalWorker $temporalWorker,
    )
    {
        parent::__construct();

        $this->httpWorker = $httpWorker;
        $this->temporalWorker = $temporalWorker;
    }

    public function configure(): void
    {
        $this
            ->setDescription('Run the roadrunner worker')
            ->setHelp(<<<EOF
            This command should not be run manually but specified in a <info>.rr.yaml</info>
            configuration file.
            EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = Environment::fromGlobals();
        if ($env->getMode() === Mode::MODE_HTTP) {
            $worker = $this->httpWorker;
        } else {
            $worker = $this->temporalWorker;
        }

        $worker->start();
        return 0;
    }
}
