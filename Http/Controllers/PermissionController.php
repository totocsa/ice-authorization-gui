<?php

namespace Totocsa\AuthorizationGUI\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Totocsa\AuthorizationGUI\Http\Models\Route as RouteModel;
use Totocsa\Icseusd\Http\Controllers\IcseusdController;

class PermissionController extends IcseusdController
{
    public $modelClassName = RouteModel::class;

    public $sort = [
        'field' => 'routes-name',
        'direction' => 'asc',
    ];

    public $orders = [
        'index' => [
            'filters' => ['routes-name', 'routes-uri', 'routes-methods', 'permissions-id', 'permissions-name', 'permissions-guard_name'],
            'sorts' => [
                'routes-name' => ['routes-name', 'routes-uri', 'routes-methods', 'permissions-name', 'permissions-guard_name'],
                'routes-uri' => ['routes-uri', 'routes-name', 'routes-methods', 'permissions-name', 'permissions-guard_name'],
                'routes-methods' => ['routes-methods', 'routes-uri', 'routes-name', 'permissions-name', 'permissions-guard_name'],
                'permissions-name' => ['permissions-name', 'permissions-guard_name', 'routes-name', 'routes-uri', 'routes-methods'],
                'permissions-guard_name' => ['permissions-guard_name', 'permissions-name', 'routes-name', 'routes-uri', 'routes-methods', 'permissions-name', 'permissions-guard_name'],
            ],
            'item' => [
                'fields' => ['routes-name', 'routes-uri', 'routes-methods', 'permissions-name', 'permissions-guard_name'],
            ],
            'itemButtons' => ['assign', 'revoke'],
        ],
        'form' => [
            'item' => [
                'fields' => ['routes-name', 'routes-uri', 'routes-methods'],
            ],
        ],
        'show' => [
            'item' => [
                'fields' => ['routes-name', 'routes-uri', 'routes-methods'],
            ],
        ],
    ];

    public $filters = [
        'routes-name' => '',
        'routes-uri' => '',
        'routes-methods' => '',
        'permissions-id' => '',
        'permissions-name' => '',
        'permissions-guard_name' => '',
    ];

    public $conditions = [
        'routes-name' => [
            'operator' => 'ilike',
            'value' => "%{{routes-name}}%",
            'boolean' => 'and',
        ],
        'routes-uri' => [
            'operator' => 'ilike',
            'value' => "%{{routes-uri}}%",
            'boolean' => 'and',
        ],
        'routes-methods' => [
            'operator' => 'ilike',
            'value' => "%{{routes-methods}}%",
            'boolean' => 'and',
        ],
        'permissions-id' => [
            'operator' => 'null',
            'value' => "{{permissions-id}}",
            'boolean' => 'and',
        ],
        'permissions-name' => [
            'operator' => 'ilike',
            'value' => "%{{permissions-name}}%",
            'boolean' => 'and',
        ],
        'permissions-guard_name' => [
            'operator' => 'ilike',
            'value' => "%{{permissions-guard_name}}%",
            'boolean' => 'and',
        ],
    ];

    public function fields()
    {
        return [
            'filter' => [
                'routes-name' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'routes-uri' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'routes-methods' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'permissions-id' => [
                    'tagName' => 'select',
                    'options' => [
                        ['value' => '', 'text' => ''],
                        ['value' => '0', 'text' => 'Assigned'],
                        ['value' => '1', 'text' => 'Revoked']
                    ],
                    'attributes' => [],
                ],
                'permissions-name' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'permissions-guard_name' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'item' => [],
            'form' => [
                'routes-name' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'routes-uri' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'routes-methods' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'permissions-id' => [
                    'tagName' => 'select',
                    'options' => ['additionalData', 'permissions-idValueTexts'],
                    'attributes' => [],
                ],
                'permissions-name' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
                'permissions-guard_name' => [
                    'tagName' => 'input',
                    'attributes' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'show' => [],
        ];
    }

    public $itemButtons = [
        'assign' => [
            'itemButtonClick' => true,
            'url' => [
                'type' => 'route',
                'route' => '{{props.routePrefix}}{{props.routeController}}.assign',
                'routeParams' => [
                    'routeId' => '{{item.id}}',
                    'permissionId' => '',
                ],
            ],
            'label' => ['category' => '', 'subtitle' => ''],
            'icon' => [
                'name' => 'PlusCircleIcon',
                'type' => 'outline',
            ],
            'showIf' => 'item["permissions-id"] === null',
            'showIcon' => true,
            'showText' => false,
            'cssClass' => 'inline-block bg-emerald-600 hover:bg-emerald-500 mr-1 p-1 rounded-md text-gray-100 w-8',
        ],
        'revoke' => [
            'itemButtonClick' => true,
            'url' => [
                'type' => 'route',
                'route' => '{{props.routePrefix}}{{props.routeController}}.revoke',
                'routeParams' => [
                    'routeName' => '{{item["permissions-name"]}}',
                ],
            ],
            'label' => ['category' => '', 'subtitle' => ''],
            'icon' => [
                'name' => 'MinusCircleIcon',
                'type' => 'outline',
            ],
            'showIf' => 'item["permissions-id"] !== null',
            'showIcon' => true,
            'showText' => false,
            'cssClass' => 'inline-block bg-rose-500 hover:bg-rose-600 mr-1 p-1 rounded-md text-gray-100 w-8',
        ],
    ];

    public function __construct()
    {
        if (method_exists(parent::class, '__construct')) {
            parent::__construct('Authorization/');
        }

        $this->routeController = "authorization.{$this->routeController}";
    }

    public function indexQuery(): LengthAwarePaginator
    {
        $t0 = 'routes';
        $t1 = 'permissions';

        $query = $this->modelClassName::query()
            ->select([
                "$t0.id",
                "$t0.name as $t0-name",
                "$t0.uri as $t0-uri",
                "$t0.methods as $t0-methods",
                "$t1.id as $t1-id",
                "$t1.name as $t1-name",
                "$t1.guard_name as $t1-guard_name",
            ])
            ->leftJoin($t1, "$t0.name", '=', "$t1.route_name");

        foreach ($this->conditions as $k => $v) {
            if ($this->filters[$k] > '') {
                $cond = $this->conditions[$k];
                $value = strtr($cond['value'], $this->replaceFieldToValue());

                if ($cond['operator'] === 'null') {
                    if ($value === '1') {
                        $query->whereNull(str_replace('-', '.', $k), $cond['boolean']);
                    } elseif ($value === '0') {
                        $query->whereNotNull(str_replace('-', '.', $k), $cond['boolean']);
                    }
                } else {
                    $query->where(str_replace('-', '.', $k), $cond['operator'], $value, $cond['boolean']);
                }
            }
        }

        foreach ($this->orders['index']['sorts'][$this->sort['field']] as $v) {
            $query->orderBy($v, $this->sort['direction']);
        }

        $results = $query->paginate($this->paging['per_page'], ['*'], null, $this->paging['page']);

        return $results;
    }

    public function refreshRoutes(Request $request)
    {
        $allRoutes = Route::getRoutes()->getRoutesByName();

        $data = [];

        foreach ($allRoutes as $k => $v) {
            $data[] = [
                'name' => $k,
                'uri' => $v->uri,
                'methods' => implode(', ', $v->methods),
            ];
        }

        DB::table('routes')->insertOrIgnore($data);
        return redirect()->action(
            [$this::class, 'index'],
            $request->all()
        );
    }


    public function assign(Request $request)
    {
        $request->validate(['routeName' => 'required|string|unique:permissions,name|max:255']);

        $nameArray = array_reverse(explode('.', $request->routeName));
        $name = implode(' ', $nameArray);

        Permission::create(['name' => $name, 'route_name' => $request->routeName]);

        return Inertia::render($this->vueComponents['index']);
    }

    public function revoke($routeName)
    {
        Permission::query()->where('route_name', $routeName)->delete();
        return Inertia::render($this->vueComponents['index']);
    }

    public function additionalIndexData()
    {
        $empty = [['value' => '', 'text' => '']];
        $permissions_idValueTexts = array_merge($empty, [
            ['value' => '0', 'text' => 'Assigned'],
            ['value' => '1', 'text' => 'Revoked']
        ]);

        return [
            'permissions-idValueTexts' => $permissions_idValueTexts,
        ];
    }
}
