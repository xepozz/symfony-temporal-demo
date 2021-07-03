<?php
declare(strict_types=1);

namespace App\App\Hello;

use App\App\Workflow\HeavyWorkflow;
use App\App\Workflow\WorkflowInterface;
use App\Util\Endpoint;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HeavyStatusEndpoint extends Endpoint
{
    public const PATH = '/status';

    public function handle(ServerRequestInterface $request): ?ResponseInterface
    {
        $id = $request->getQueryParams()['id'];

        $wf = $this->workflowClient->newRunningWorkflowStub(HeavyWorkflow::class, $id);

        var_dump([
            'result' => $wf->getStatus(),
        ]);

        return null;
    }
}