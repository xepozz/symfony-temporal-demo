<?php
declare(strict_types=1);

namespace App\App\Workflow;

use App\App\Activity\HelloActivity;
use Temporal\Activity\ActivityOptions;
use Temporal\Promise;
use Temporal\Workflow;

#[\Temporal\Workflow\WorkflowInterface]
class MainWorkflow
{
    #[\Temporal\Workflow\WorkflowMethod("main_workflow")]
    public function run(string $name, int $count): \Generator
    {
        $activity = Workflow::newActivityStub(
            HelloActivity::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(5)
        );
        $promises = [];

        foreach (range(1, $count) as $item) {
            $promises[] = $activity->slow($name);
        }

        return yield Promise::all($promises);
    }
}