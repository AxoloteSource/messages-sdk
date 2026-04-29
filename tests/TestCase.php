<?php

namespace AxoloteSource\MessagesSdk\Tests;

use AxoloteSource\MessagesSdk\AxMessagesServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            AxMessagesServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('axMessages.url', 'https://api.ejemplo.com');
        $app['config']->set('axMessages.token', 'fake-token');
        $app['config']->set('axMessages.debug', false);
        $app['config']->set('axMessages.disabled', false);
    }
}
