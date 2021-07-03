<?php
declare(strict_types=1);
namespace App\App\Activity;

#[\Temporal\Activity\ActivityInterface(prefix: "app.")]
class HelloActivity implements \App\Contract\ActivityInterface
{
    public function hello(string $name): string
    {
        return 'hello ' . $name;
    }

    public function slow(string $name): string
    {
        sleep(1);
        return 'hello ' . $name;
    }
}