<?php

namespace Totocsa\AuthorizationGUI;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Totocsa\MigrationHelper\MigrationHelper;

class AuthorizationGUIServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $groupsBase = 'ice-authorization-gui';
        $groups = "$groupsBase-migrations";

        $paths = MigrationHelper::stubsToMigrations($groups, __DIR__ . '/database/migrations/');

        $this->publishes($paths, $groups);
        $this->publishes([__DIR__ . '/resources/js' =>  resource_path("js/totocsa/$groupsBase")], "$groupsBase-resources");
    }
}
