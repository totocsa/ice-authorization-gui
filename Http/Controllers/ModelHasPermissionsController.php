<?php

namespace Totocsa\AuthorizationGUI\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Totocsa\AuthorizationGUI\Http\Models\Role;
use Totocsa\Icseusd\Services\GenericConfigLoader;
use Exception;

class ModelHasPermissionsController extends Controller
{
    public $allModels = [
        ['className' => User::class, 'configName' => 'users'],
        ['className' => Role::class, 'configName' => 'roles'],
    ];

    public function index()
    {
        $allModels = $this->allModels();
        $revokeOrders = [
            'item' => [
                'fields' => ['model', 'permissions-name', 'permissions-guard_name', 'permissions-route_name']
            ]
        ];

        return Inertia::render('Authorization/ModelHasPermissions/Index',  compact('allModels', 'revokeOrders'));
    }

    public function permissions($configName, $modelId)
    {
        $model = $this->loadModel($configName, $modelId);

        $modelPermissions = $model->permissions;
        $allPermissions = $this->allPermissions();

        return Inertia::render('Authorization/ModelHasPermissions/Index',  compact('modelPermissions', 'allPermissions'));
    }

    public function store(Request $request)
    {
        $model = $this->loadModel($request->configName, $request->modelId);
        $model->givePermissionTo(intval($request->permissionId));

        $modelPermissions = $model->permissions;
        $allPermissions = $this->allPermissions();

        return Inertia::render('Authorization/ModelHasPermissions/Index',  compact('modelPermissions', 'allPermissions'));
    }

    public function destroy($configName, $modelId, $permissionId)
    {
        $model = $this->loadModel($configName, $modelId);
        $model->revokePermissionTo(intval($permissionId));

        $modelPermissions = $model->permissions;
        $allPermissions = $this->allPermissions();

        return Inertia::render('Authorization/ModelHasPermissions/Index',  compact('modelPermissions', 'allPermissions'));
    }

    public function allModels()
    {
        return $this->allModels;
    }

    public function allPermissions()
    {
        return Permission::orderBy('name')->get();
    }

    public function loadModel($configName, $id)
    {
        $genericConfig = $this->loadGenericConfig($configName);
        return $genericConfig['modelClassName']::find($id);
    }

    public function loadGenericConfig($configName)
    {
        try {
            return GenericConfigLoader::get($configName);
        } catch (\Exception $e) {
            abort(404, $e->getMessage());
        }
    }
}
