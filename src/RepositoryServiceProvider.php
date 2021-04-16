<?php

namespace Gerfey\Repository;

use Gerfey\Repository\Console\RepositoryMakeCommand;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    RepositoryMakeCommand::class
                ]
            );
        }
    }
}
