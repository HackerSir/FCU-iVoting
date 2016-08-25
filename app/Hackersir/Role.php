<?php

namespace Hackersir;

use Zizaco\Entrust\EntrustRole;

/**
 * Hackersir\Role
 *
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Hackersir\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\Hackersir\Permission[] $perms
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Role whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Role whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Role whereDisplayName($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Role whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends EntrustRole
{
}
