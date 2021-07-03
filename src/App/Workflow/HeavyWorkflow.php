<?php
declare(strict_types=1);

namespace App\App\Workflow;

use App\App\Activity\HelloActivity;
use App\Contract\WorkflowInterface;
use Temporal\Activity\ActivityOptions;
use Temporal\Promise;
use Temporal\Workflow;

class HeavyWorkflow implements WorkflowInterface
{
    private array $done = [];
    private string $status = 'start';

    #[Workflow\QueryMethod]
    public function getStatus()
    {
        return [
            'status' => $this->status,
            'done' => $this->done,
        ];
    }

    #[\Temporal\Workflow\WorkflowMethod("heavy_workflow")]
    public function run(string $name, int $count)
    {
        $activity = Workflow::newActivityStub(
            HelloActivity::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(5)
        );

        $promises = [];

        foreach (range(1, $count) as $item) {
            $promises[] = $activity->slow($name)
                ->then(
                    function () use ($item) {
                        $this->done[] = $item;
                    }
                );
        }
        $this->status = 'processing';

        $result = yield Promise::all($promises);
        $this->status = 'done';

        return $result;
    }
}