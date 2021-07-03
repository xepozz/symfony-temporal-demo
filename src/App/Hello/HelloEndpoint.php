<?php
declare(strict_types=1);

namespace App\App\Hello;

use App\App\Workflow\WorkflowInterface;
use App\Util\Endpoint;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HelloEndpoint extends Endpoint
{
    public const PATH = '/';

    public function handle(ServerRequestInterface $request): ?ResponseInterface
    {
        $wf = $this->workflowClient->newWorkflowStub(WorkflowInterface::class);
        $start = microtime(true);
        $result = $wf->run('Dmitry', 10);
        var_dump([
            'result' => $result,
            'time' => microtime(true) - $start
        ]);
        return null;
    }
}