<?php

namespace App\Actions;

final readonly class OnboardApplicationAction
{
    public function handle(array $attributes)
    {
        $result = $attributes
            |> new StoreApplicationAction()->handle(...)
            |> new StoreMonitorAction()->handle(...);
    }

}
