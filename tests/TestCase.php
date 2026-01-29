<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUpTraits()
    {
        if (app()->configurationIsCached()) {
            $this->fail(
                "CRITICAL: Application is in optimized mode (Config Cached).\n" .
                "Testing halted to prevent data corruption.\n" .
                "Run 'php artisan optimize:clear' first."
            );
        }

        return parent::setUpTraits();
    }
}
