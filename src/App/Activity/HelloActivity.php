<?php
declare(strict_types=1);
namespace App\App\Activity;

#[\Temporal\Activity\ActivityInterface(prefix: "app.")]
class HelloActivity
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