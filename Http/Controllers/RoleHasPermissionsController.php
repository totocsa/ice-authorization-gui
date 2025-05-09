<?php

namespace Totocsa\AuthorizationGUI\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Totocsa\Icseusd\Http\Controllers\IcseusdController;

class RoleHasPermissionsController extends IcseusdController
{
    public $modelClassName = Role::class;

    public $sort = [
        'field' => 'roles-name',
        'direction' => 'asc',
    ];

    public $orders = [
        'index' => [
            'filters' => ['roles-name', 'roles-guard_name'],
            'sorts' => [
                'roles-name' => ['roles-name', 'roles-guard_name'],
                'roles-guard_name' => ['roles-guard_name', 'roles-name'],
            ],
            'item' => [
                'fields' => ['roles-name', 'roles-guard_name'],
            ],
            'itemButtons' => [],
            'revoke' => [
                'item' => [
                    'fields' => ['roles-name', 'roles-guard_name', 'permissions-name'],
                ]
            ],
        ],
        'form' => [
            'item' => [
                'fields' => ['roles-name', 'roles-guard_name'],
            ],
        ],
        'show' => [
            'item' => [
                'fields' => ['roles-name', 'roles-guard_name'],
            ]
        ],
    ];

    public $filters = [
        'roles-name' => '',
        'roles-guard_name' => '',
    ];

    public $conditions = [
        'roles-name' => [
            'operator' => 'ilike',
            'value' => "%{{roles-name}}%",
            'boolean' => 'and',
        ],
        'roles-guard_name' => [
            'operator' => 'ilike',
            'value' => "%{{roles-guard_name}}%",
            'boolean' => 'and',
        ],
    ];

    public function fields()
    {
        return [
            'roles-name' => [
                'editableOnIndex' => true,
                'form' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'filter' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'roles-guard_name' => [
                'form' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'roles-guard_name',
                    ],
                ],
                'filter' => [
                    'tag' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
        ];
    }

    public function __construct()
    {
        if (method_exists(parent::class, '__construct')) {
            $prefix = 'authorization';
            parent::__construct(ucfirst($prefix) . '/', "$prefix.");
        }
    }

    public function indexQuery(): LengthAwarePaginator
    {
        /* @var $query \App\Models\XModel */
        $t0 = 'roles';

        $query = $this->modelClassName::query()
            ->select([
                "$t0.id",
                "$t0.name as $t0-name",
                "$t0.guard_name as $t0-guard_name",
            ]);

        foreach ($this->conditions as $k => $v) {
            if ($this->filters[$k] > 0) {
                $cond = $this->conditions[$k];
                $value = strtr($cond['value'], $this->replaceFieldToValue());
                $query->where(str_replace('-', '.', $k), $cond['operator'], $value, $cond['boolean']);
            }
        }

        foreach ($this->orders['index']['sorts'][$this->sort['field']] as $v) {
            $query->orderBy($v, $this->sort['direction']);
        }

        $results = $query->paginate($this->paging['per_page'], ['*'], null, $this->paging['page']);

        return $results;
    }

    public function store(Request $request)
    {
        $a = $request->all();
        $validator = Validator::make($request->all(), [
            'roleid' => 'required|integer',
            'permissionid' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->messages(),
            ], 422);
        }

        $role = Role::findById($request->roleid);
        $role->givePermissionTo(intval($request->permissionid));

        $rolePermissions = $this->getRolePermissions($role);
        $allPermissions = $this->allPermissions();

        return Inertia::render($this->vueComponents['index'],  compact('rolePermissions', 'allPermissions'));
    }

    public function revoke($roleId, $permissionName)
    {
        $attributes = [
            'roleId' => $roleId,
            'permissionName' => $permissionName,
        ];

        $validator = Validator::make($attributes, [
            'roleId' => 'required|integer',
            'permissionName' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->messages(),
            ], 422);
        }

        $role = Role::findById($roleId);

        $role->revokePermissionTo($permissionName);

        $rolePermissions = $this->getRolePermissions($role);
        $allPermissions = $this->allPermissions();

        return Inertia::render($this->vueComponents['index'],  compact('rolePermissions', 'allPermissions'));
    }

    public function rolePermissions(Request $request, Role $role)
    {
        $validator = Validator::make($role->getAttributes(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->messages(),
            ], 422);
        }

        $rolePermissions = $this->getRolePermissions($role);
        $allPermissions = $this->allPermissions();

        return Inertia::render($this->vueComponents['index'],  compact('rolePermissions', 'allPermissions'));
    }

    public function allPermissions()
    {
        return Permission::orderBy('name')->get();
    }

    public function getRolePermissions(Role $role)
    {
        return $role->getPermissionNames()->toArray();
    }
}
