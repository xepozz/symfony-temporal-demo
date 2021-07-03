<?php
declare(strict_types=1);

namespace App\App\Workflow;

use App\App\Activity\HelloActivity;
use App\Contract\WorkflowInterface;
use Temporal\Activity\ActivityOptions;
use Temporal\Promise;
use Temporal\Workflow;

//#[\Temporal\Workflow\WorkflowInterface]
class MainWorkflow implements WorkflowInterface
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

//        return yield $activity->slow($name);
        foreach (range(1, $count) as $item) {
            $promises[] = $activity->slow($name);
        }

        $result = yield Promise::all($promises);
//        $result = $promises;
//        $result = print_r($activity, true);

        return $result;
    }
}