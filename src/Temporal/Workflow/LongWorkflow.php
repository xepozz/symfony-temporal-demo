<?php
declare(strict_types=1);

namespace App\Temporal\Workflow;

use App\Temporal\Activity\CommonActivity;
use Temporal\Activity\ActivityOptions;
use Temporal\Promise;
use Temporal\Workflow;

#[\Temporal\Workflow\WorkflowInterface]
final class LongWorkflow
{
    private array $done = [];
    private string $status = 'start';

    #[Workflow\QueryMethod]
    public function getStatus(): array
    {
        return [
            'status' => $this->status,
            'done' => $this->done,
        ];
    }

    #[\Temporal\Workflow\WorkflowMethod("long_workflow")]
    public function run(string $name, int $count): \Generator
    {
        $activity = Workflow::newActivityStub(
            CommonActivity::class,
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