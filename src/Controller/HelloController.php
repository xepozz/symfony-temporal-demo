<?php
declare(strict_types=1);

namespace App\Controller;

use App\App\Workflow\HeavyWorkflow;
use App\App\Workflow\MainWorkflow;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Temporal\Client\WorkflowClientInterface;

class HelloController
{
    private WorkflowClientInterface $workflowClient;

    public function __construct(WorkflowClientInterface $workflowClient)
    {
        $this->workflowClient = $workflowClient;
    }

    #[Route('/hello/{name}')]
    public function t($name = 'World')
    {
        $start = microtime(true);

        $wf = $this->workflowClient->newWorkflowStub(MainWorkflow::class);
        $response = $wf->run('Dmitry2', 5);

        $response = print_r([
            'time' => microtime(true) - $start,
            'response' => $response,
        ], true);

        return new Response($response);
    }

    #[Route('/heavy/{name}')]
    public function t2($name = 'World')
    {
        $wf = $this->workflowClient->newWorkflowStub(HeavyWorkflow::class);

        $process = $this->workflowClient->start($wf, 'my name', 10);

        $id = $process->getExecution()->getID();

        $response = sprintf('http://localhost:8080/status/%s', $id);

        return new Response($response);
    }

    #[Route('/status/{id}')]
    public function t3($id = null)
    {
        $wf = $this->workflowClient->newRunningWorkflowStub(HeavyWorkflow::class, $id);

        $response = print_r([
            'result' => $wf->getStatus(),
        ], true);

        return new Response($response);
    }
}