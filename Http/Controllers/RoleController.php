<?php

namespace Totocsa\AuthorizationGUI\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Totocsa\AuthorizationGUI\Http\Models\Role;
use Inertia\Inertia;
use Totocsa\Icseusd\Http\Controllers\IcseusdController;

class RoleController extends IcseusdController
{
    public $modelClassName = Role::class;

    public $sort = [
        'field' => 'roles-name',
        'direction' => 'asc',
    ];

    public $orders = [
        'index' => [
            'filters' => ['roles-name'],
            'sorts' => [
                'roles-name' => ['roles-name'],
            ],
            'item' => [
                'fields' => ['roles-name'],
            ],
            'itemButtons' => ['destroy'],
        ],
        'form' => [
            'item' => [
                'fields' => ['roles-name'],
            ],
        ],
        'show' => [
            'item' => [
                'fields' => ['roles-name'],
            ],
        ]
    ];

    public $filters = [
        'roles-name' => '',
    ];

    public $conditions = [
        'roles-name' => [
            'operator' => 'ilike',
            'value' => "%{{roles-name}}%",
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
        ];
    }

    public function __construct()
    {
        if (method_exists(parent::class, '__construct')) {
            parent::__construct('Authorization/');
        }

        $this->routeController = "authorization.{$this->routeController}";
    }

    public function indexQuery(): LengthAwarePaginator
    {
        /* @var $query \App\Models\XModel */
        $t0 = 'roles';

        $query = $this->modelClassName::query()
            ->select([
                "$t0.id",
                "$t0.name as $t0-name",
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

    /*public function index()
    {
        $allRoles = $this->allRoles()->toArray();
        return Inertia::render('Authorization/Roles/Roles',  compact('allRoles'));
    }*/

    public function store(Request $request)
    {
        $attributes = $request->all();
        $rules = array_intersect_key($this->modelClassName::rules(), ['name' => '']);

        $validator = Validator::make($attributes, $rules);
        $errors = $validator->messages('translatable');

        if (count($errors) === 0) {
            Role::create(['name' => $request->name]);
        }

        return Inertia::render($this->vueComponents['index'],  compact('errors'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($request->id)],
        ]);

        if ($validator->fails()) {
            $errors = [$request->id => $validator->errors()->toArray()];
            $allRoles = $this->allRoles()->toArray();

            return Inertia::render('Authorization/Roles/Roles',  compact('errors', 'allRoles'));
        }

        Role::where('id', $request->id)->update(['name' => $request->name]);

        return $this->index($request);
    }

    public function additionalIndexData()
    {
        return [
            'CreateRoleProps' => [
                'attributes' => [
                    'name' => '',
                ],
            ],
        ];
    }

    /*public function allRoles()
    {
        return Role::select(['id', 'name'])->orderBy('name')->get();
    }*/
}
