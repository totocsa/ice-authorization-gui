<?php

namespace Totocsa\AuthorizationGUI\Http\Models;

use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public static function rules($attributes = []): array
    {
        $id = key_exists('id', $attributes) ? $attributes['id'] : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($id),
            ],
            'guard_name' => ['required', 'string', 'max:255',],
        ];
    }
}
