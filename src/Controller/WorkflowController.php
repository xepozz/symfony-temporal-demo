<?php
declare(strict_types=1);

namespace App\Controller;

use App\Temporal\Workflow\FastWorkflow;
use App\Temporal\Workflow\LongWorkflow;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Temporal\Client\WorkflowClientInterface;

final class WorkflowController
{
    private WorkflowClientInterface $workflowClient;

    public function __construct(WorkflowClientInterface $workflowClient)
    {
        $this->workflowClient = $workflowClient;
    }

    #[Route('/simple/{name}', name: 'simple')]
    public function simpleAction(string $name): Response
    {
        $start = microtime(true);

        $wf = $this->workflowClient->newWorkflowStub(FastWorkflow::class);
        $result = $wf->run($name, 5);

        $end = microtime(true);

        $response = [
            'microtime' => $end - $start,
            'result' => $result,
        ];

        return $this->json($response);
    }

    #[Route('/complicated/{name}', name: 'complicated')]
    public function complicatedAction(string $name): Response
    {
        $start = microtime(true);

        $wf = $this->workflowClient->newWorkflowStub(LongWorkflow::class);
        $result = $wf->run($name, 5);

        $end = microtime(true);

        $response = [
            'microtime' => $end - $start,
            'result' => $result,
        ];

        return $this->json($response);
    }

    #[Route('/asynchronous/{name}', name: 'asynchronous')]
    public function asynchronousAction(UrlGeneratorInterface $urlGenerator, string $name): Response
    {
        $start = microtime(true);

        $wf = $this->workflowClient->newWorkflowStub(LongWorkflow::class);
        $process = $this->workflowClient->start($wf, $name, 10);
        $id = $process->getExecution()->getID();

        $end = microtime(true);

        $url = $urlGenerator->generate('asynchronous-status', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL);

        $delay = $end - $start;
        $response = <<<HTML
        Microtime: {$delay} <br>
        Job ID: {$id} <br>
        To see status of this job open <a href="{$url}" target="_blank">click here</a>.
        HTML;

        return new Response($response);
    }

    #[Route('/asynchronous/status/{id}', name: 'asynchronous-status')]
    public function asynchronousStatusAction(string $id): Response
    {
        $start = microtime(true);

        $wf = $this->workflowClient->newRunningWorkflowStub(LongWorkflow::class, $id);
        $result = $wf->getStatus();

        $end = microtime(true);

        $response = [
            'Note' => 'Please update the page to see results',
            'microtime' => $end - $start,
            'result' => $result,
        ];

        return $this->json($response);
    }

    private function json(array $data): JsonResponse
    {
        $response = new JsonResponse($data);
        $response->setEncodingOptions(JSON_PRETTY_PRINT | $response->getEncodingOptions());
        return $response;
    }
}