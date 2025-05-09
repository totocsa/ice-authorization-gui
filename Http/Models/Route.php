<?php

namespace Totocsa\AuthorizationGUI\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Route extends Model
{

    protected $fillable = [
        'name',
        'guard_name',
    ];

    public static function rules($attributes = []): array
    {
        $id = key_exists('id', $attributes) ? $attributes['id'] : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'guard_name' => [
                'required',
                'email',
                Rule::unique('users')->ignore($id),
            ],
        ];
    }
}
