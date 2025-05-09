<?php

namespace Totocsa\AuthorizationGUI;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Totocsa\MigrationHelper\MigrationHelper;

class AuthorizationGUIServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $groups = 'ice-authorization-gui';
        $global = $GLOBALS;
        $isPublish = isset($global['argv']) && isset($global['argv'][0]) && $global['argv'][0] === 'artisan'
            && isset($global['argv'][1]) && $global['argv'][1] === 'vendor:publish'
            && isset($global['argv'][2]) && $global['argv'][2] === "--tag=$groups";

        $files = File::files(__DIR__ . '/database/migrations/');
        $sortedFiles = collect($files)->sortBy(fn($file) => $file->getFilename())->values();

        $paths = [];
        foreach ($sortedFiles as $v) {
            $fileInfo = $v->getFileInfo();
            $publishAs = MigrationHelper::publishAs($v, $isPublish, 2);

            $paths[$fileInfo->getPathname()] = $publishAs;
        }

        $paths[__DIR__ . '/resources'] = resource_path();

        $this->publishes($paths, $groups);
    }
}
