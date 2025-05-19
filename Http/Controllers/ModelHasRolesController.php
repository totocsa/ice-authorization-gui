<?php

namespace Totocsa\AuthorizationGUI\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Totocsa\Icseusd\Services\GenericConfigLoader;
use Exception;

class ModelHasRolesController extends Controller
{
    public $allModels = [
        ['className' => User::class, 'configName' => 'users'],
    ];

    public function index()
    {
        $allModels = $this->allModels();
        $roleOrders = [
            'item' => [
                'fields' => ['model_has_roles-name', 'model_has_roles-guard_name']
            ]
        ];

        return Inertia::render('Authorization/ModelHasRoles/Index',  compact('allModels', 'roleOrders'));
    }

    public function roles($configName, $modelId)
    {
        $model = $this->loadModel($configName, $modelId);

        $modelRoles = $model->roles;
        $allRoles = $this->allRoles();

        return Inertia::render('Authorization/ModelHasRoles/Index',  compact('modelRoles', 'allRoles'));
    }

    public function store(Request $request)
    {
        $model = $this->loadModel($request->configName, $request->modelId);
        $model->assignRole(intval($request->roleId));

        $modelRoles = $model->roles;
        $allRoles = $this->allRoles();

        return Inertia::render('Authorization/ModelHasRoles/Index',  compact('modelRoles', 'allRoles'));
    }

    public function destroy($configName, $modelId, $roleId)
    {
        $model = $this->loadModel($configName, $modelId);
        $model->removeRole(intval($roleId));

        $modelRoles = $model->roles;
        $allRoles = $this->allRoles();

        return Inertia::render('Authorization/ModelHasRoles/Index',  compact('modelRoles', 'allRoles'));
    }

    public function allModels()
    {
        return $this->allModels;
    }

    public function allRoles()
    {
        return Role::orderBy('name')->get();
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
