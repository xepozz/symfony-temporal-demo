<?php
declare(strict_types=1);

namespace App\App\Hello;

use App\App\Workflow\HeavyWorkflow;
use App\App\Workflow\WorkflowInterface;
use App\Util\Endpoint;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HeavyEndpoint extends Endpoint
{
    public const PATH = '/heavy';

    public function handle(ServerRequestInterface $request): ?ResponseInterface
    {
        $wf = $this->workflowClient->newWorkflowStub(HeavyWorkflow::class);

        $process = $this->workflowClient->start($wf, 'my name', 10);

        $id = $process->getExecution()->getID();

        echo sprintf('http://localhost:8080/status?id=%s', $id);

        return null;
    }
}